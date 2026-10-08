<?php

namespace App\Modules\System\Services;

use App\Models\User;
use App\Modules\System\Models\SystemSetting;
use App\Modules\System\Settings\SettingDefinitions;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Tham số hệ thống (FR-SYS-001, BR-SYS-08, GC-12).
 *
 *     app(SettingService::class)->get('school.name');
 *
 * - Tham số chưa ai lưu thì dùng giá trị mặc định (SettingDefinitions); tham số gắn config được ghi đè lúc khởi động.
 * - Mỗi thay đổi ghi nhật ký trước – sau (kể cả khi trước đó đang dùng mặc định); thay đổi quan trọng
 *   (múi giờ, ngôn ngữ, chính sách phiên) còn được báo cho các ADMIN khác.
 */
class SettingService extends BaseService
{
    private const CACHE_KEY = 'studentmanager.system_settings';

    public function __construct(
        private readonly AuditLogger $audit,
        private readonly ConfigurationChangeNotifier $notifier,
    ) {}

    /** Giá trị đang hiệu lực. */
    public function get(string $key): mixed
    {
        $stored = $this->stored();

        return array_key_exists($key, $stored) ? $stored[$key] : (SettingDefinitions::all()[$key]['default'] ?? null);
    }

    /**
     * Mọi tham số kèm nhãn, nhóm, giá trị đang hiệu lực và mặc định (cho màn hình cấu hình).
     *
     * @return array<string, array{label: string, group: string, value: mixed, default: mixed, is_default: bool, important: bool}>
     */
    public function all(): array
    {
        $stored = $this->stored();
        $result = [];

        foreach (SettingDefinitions::all() as $key => $definition) {
            $result[$key] = [
                'label' => $definition['label'],
                'group' => $definition['group'],
                'value' => array_key_exists($key, $stored) ? $stored[$key] : $definition['default'],
                'default' => $definition['default'],
                'is_default' => ! array_key_exists($key, $stored),
                'important' => $definition['important'],
            ];
        }

        return $result;
    }

    /**
     * Lưu một hoặc nhiều tham số. Chỉ các tham số thật sự đổi giá trị mới được lưu và ghi nhật ký.
     *
     * @param  array<string, mixed>  $values
     * @return list<string> các khóa đã thay đổi
     *
     * @throws ValidationException
     */
    public function set(array $values, User $actor, ?string $reason = null): array
    {
        $definitions = SettingDefinitions::all();
        $rules = [];
        $attributes = [];

        foreach (array_keys($values) as $key) {
            if (! isset($definitions[$key])) {
                throw ValidationException::withMessages(["values.{$key}" => "Không có tham số hệ thống \"{$key}\"."]);
            }

            $rules["values.{$key}"] = $definitions[$key]['rules'];
            $attributes["values.{$key}"] = mb_strtolower($definitions[$key]['label']);
        }

        // Khóa có dấu chấm ("session.idle_minutes"): đổi tạm thành "__" để Validator không hiểu nhầm thành mảng lồng nhau,
        // rồi trả lỗi về đúng tên khóa gốc
        $validator = Validator::make(['values' => $this->nest($values)], $this->nestRules($rules), [], $this->nestRules($attributes));

        if ($validator->fails()) {
            $messages = [];

            foreach ($validator->errors()->messages() as $field => $fieldMessages) {
                $messages['values.'.str_replace('__', '.', substr($field, strlen('values.')))] = $fieldMessages;
            }

            throw ValidationException::withMessages($messages);
        }

        $changed = [];
        $lines = [];
        $important = false;

        $this->transaction(function () use ($values, $definitions, $actor, $reason, &$changed, &$lines, &$important): void {
            foreach ($values as $key => $value) {
                $value = $this->normalize($key, $value);
                $old = $this->get($key);

                if ($old === $value) {
                    continue;
                }

                $setting = SystemSetting::query()->firstOrNew(['key' => $key]);
                $setting->value = $value;
                $setting->updated_by = $actor->id;
                $setting->created_by ??= $actor->id;
                $setting->save();

                $this->audit->record(AuditEvent::Updated, $setting, [$key => $old], [$key => $value], $reason ?? 'Đổi tham số hệ thống');

                $changed[] = $key;
                $lines[] = $definitions[$key]['label'].': '.$this->display($old).' → '.$this->display($value);
                $important = $important || $definitions[$key]['important'];

                // Đọc lại trong cùng giao dịch phải thấy giá trị mới
                Cache::forget(self::CACHE_KEY);
            }
        });

        if ($changed !== []) {
            Cache::forget(self::CACHE_KEY);
            $this->applyToConfig();

            if ($important) {
                $this->notifier->notifyOtherAdmins($actor, 'Đổi tham số hệ thống', $lines);
            }
        }

        return $changed;
    }

    /** Tải logo mới lên disk public (FR-SYS-001), xóa logo cũ. Trả về đường dẫn đã lưu. */
    public function storeLogo(UploadedFile $file, User $actor): string
    {
        $old = $this->get('school.logo_path');
        $path = $file->store('branding', 'public');

        if ($path === false) {
            $this->fail('Không lưu được logo.', 'Kiểm tra quyền ghi thư mục storage/app/public rồi thử lại.');
        }

        $this->set(['school.logo_path' => $path], $actor, 'Đổi logo');

        if (is_string($old) && $old !== $path) {
            Storage::disk('public')->delete($old);
        }

        return $path;
    }

    /**
     * Ghi đè các khóa config gắn với tham số đã lưu (múi giờ hiển thị, ngôn ngữ, định dạng ngày, thời gian phiên…).
     * Gọi lúc khởi động ứng dụng; bảng chưa có (đang cài đặt, chạy migrate) thì bỏ qua.
     */
    public function applyToConfig(): void
    {
        try {
            $stored = $this->stored();
        } catch (QueryException) {
            return;
        }

        foreach (SettingDefinitions::all() as $key => $definition) {
            if (! isset($definition['config']) || ! array_key_exists($key, $stored)) {
                continue;
            }

            config([$definition['config'] => $stored[$key]]);

            if ($definition['config'] === 'studentmanager.formats.date') {
                config(['studentmanager.formats.datetime' => $stored[$key].' H:i']);
            }

            if ($definition['config'] === 'app.locale') {
                app()->setLocale($stored[$key]);
            }
        }
    }

    /** @return array<string, mixed> */
    private function stored(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => SystemSetting::query()->pluck('value', 'key')->all());
    }

    private function normalize(string $key, mixed $value): mixed
    {
        $rules = SettingDefinitions::all()[$key]['rules'];

        if ($value === '' && in_array('nullable', $rules, true)) {
            return null;
        }

        return in_array('integer', $rules, true) ? (int) $value : $value;
    }

    private function display(mixed $value): string
    {
        return $value === null || $value === '' ? '(trống)' : (string) $value;
    }

    /** @param  array<string, mixed>  $flat */
    private function nest(array $flat): array
    {
        $nested = [];

        foreach ($flat as $key => $value) {
            $nested[str_replace('.', '__', $key)] = $value;
        }

        return $nested;
    }

    /** @param  array<string, mixed>  $flat */
    private function nestRules(array $flat): array
    {
        $nested = [];

        foreach ($flat as $key => $value) {
            $nested['values.'.str_replace('.', '__', substr($key, strlen('values.')))] = $value;
        }

        return $nested;
    }
}
