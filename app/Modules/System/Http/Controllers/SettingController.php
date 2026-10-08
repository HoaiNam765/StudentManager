<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\System\Http\Requests\UpdateSettingsRequest;
use App\Modules\System\Http\Requests\UploadLogoRequest;
use App\Modules\System\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/** API tham số hệ thống (FR-SYS-001). Quyền: `permission:SYS.view` / `SYS.update` ở routes/admin.php. */
class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}

    public function index(): JsonResponse
    {
        return response()->json($this->presentAll());
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $changed = $this->settings->set($request->input('values'), $request->user(), $request->input('reason'));

        return response()->json(['changed' => $changed, 'settings' => $this->presentAll()]);
    }

    public function uploadLogo(UploadLogoRequest $request): JsonResponse
    {
        $path = $this->settings->storeLogo($request->file('logo'), $request->user());

        return response()->json(['path' => $path, 'url' => Storage::disk('public')->url($path)]);
    }

    /** @return list<array<string, mixed>> */
    private function presentAll(): array
    {
        $rows = [];

        foreach ($this->settings->all() as $key => $setting) {
            $rows[] = ['key' => $key] + $setting;
        }

        return $rows;
    }
}
