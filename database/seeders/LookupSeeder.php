<?php

namespace Database\Seeders;

use App\Modules\System\Enums\AdministrativeLevel;
use App\Modules\System\Enums\AdministrativeScheme;
use App\Modules\System\Models\AdministrativeUnit;
use App\Modules\System\Models\LookupCategory;
use Illuminate\Database\Seeder;

/**
 * Danh mục dùng chung chuẩn (FR-SYS-002). Chạy lặp lại an toàn: chỉ thêm mục còn thiếu theo mã,
 * không ghi đè tên hay trạng thái mà quản trị viên đã sửa.
 *
 * Nguồn và mức độ chắc chắn (cần đối chiếu trước khi dùng thật):
 *   - Dân tộc: 54 dân tộc, mã 01–54 theo danh mục các dân tộc Việt Nam của Tổng cục Thống kê.
 *   - Đơn vị hành chính: 34 tỉnh/thành theo mô hình hai cấp từ 01/07/2025; mã tỉnh theo danh mục mã
 *     đơn vị hành chính mới, cần đối chiếu với danh mục chính thức. Danh sách xã/phường (khoảng 3.300 đơn vị)
 *     nhập bằng trung tâm import (Importer `administrative_units`), không seed ở đây.
 *   - Tôn giáo, đối tượng/khu vực ưu tiên, loại hợp đồng: mã nội bộ của hệ thống.
 *   - Quốc tịch: mã ISO 3166-1 alpha-2; chỉ seed một số nước, thêm bằng import khi cần.
 */
class LookupSeeder extends Seeder
{
    /** Mã danh mục => [tên, mô tả, [mã => tên]] */
    public const CATEGORIES = [
        LookupCategory::GENDER => ['Giới tính', 'Mã theo ISO/IEC 5218', [
            '1' => 'Nam', '2' => 'Nữ',
        ]],
        LookupCategory::ETHNICITY => ['Dân tộc', '54 dân tộc Việt Nam, mã theo danh mục của Tổng cục Thống kê', [
            '01' => 'Kinh', '02' => 'Tày', '03' => 'Thái', '04' => 'Hoa', '05' => 'Khơ-me', '06' => 'Mường',
            '07' => 'Nùng', '08' => 'Mông', '09' => 'Dao', '10' => 'Gia-rai', '11' => 'Ngái', '12' => 'Ê-đê',
            '13' => 'Ba-na', '14' => 'Xơ-đăng', '15' => 'Sán Chay', '16' => 'Cơ-ho', '17' => 'Chăm', '18' => 'Sán Dìu',
            '19' => 'Hrê', '20' => 'Mnông', '21' => 'Ra-glai', '22' => 'Xtiêng', '23' => 'Bru-Vân Kiều', '24' => 'Thổ',
            '25' => 'Giáy', '26' => 'Cơ-tu', '27' => 'Gié-Triêng', '28' => 'Mạ', '29' => 'Khơ-mú', '30' => 'Co',
            '31' => 'Tà-ôi', '32' => 'Chơ-ro', '33' => 'Kháng', '34' => 'Xinh-mun', '35' => 'Hà Nhì', '36' => 'Chu-ru',
            '37' => 'Lào', '38' => 'La Chí', '39' => 'La Ha', '40' => 'Phù Lá', '41' => 'La Hủ', '42' => 'Lự',
            '43' => 'Lô Lô', '44' => 'Chứt', '45' => 'Mảng', '46' => 'Pà Thẻn', '47' => 'Cơ Lao', '48' => 'Cống',
            '49' => 'Bố Y', '50' => 'Si La', '51' => 'Pu Péo', '52' => 'Brâu', '53' => 'Ơ-đu', '54' => 'Rơ-măm',
        ]],
        LookupCategory::RELIGION => ['Tôn giáo', 'Mã nội bộ', [
            '00' => 'Không', '01' => 'Phật giáo', '02' => 'Công giáo', '03' => 'Tin lành', '04' => 'Cao Đài',
            '05' => 'Phật giáo Hòa Hảo', '06' => 'Hồi giáo', '99' => 'Tôn giáo khác',
        ]],
        LookupCategory::NATIONALITY => ['Quốc tịch', 'Mã ISO 3166-1 alpha-2', [
            'VN' => 'Việt Nam', 'LA' => 'Lào', 'KH' => 'Campuchia', 'CN' => 'Trung Quốc',
            'KR' => 'Hàn Quốc', 'JP' => 'Nhật Bản', 'US' => 'Hoa Kỳ',
        ]],
        LookupCategory::PRIORITY_GROUP => ['Đối tượng ưu tiên tuyển sinh', 'Nhóm ƯT1 gồm đối tượng 01–04, nhóm ƯT2 gồm 05–07', [
            '01' => 'Đối tượng 01 (nhóm ƯT1)', '02' => 'Đối tượng 02 (nhóm ƯT1)', '03' => 'Đối tượng 03 (nhóm ƯT1)',
            '04' => 'Đối tượng 04 (nhóm ƯT1)', '05' => 'Đối tượng 05 (nhóm ƯT2)', '06' => 'Đối tượng 06 (nhóm ƯT2)',
            '07' => 'Đối tượng 07 (nhóm ƯT2)',
        ]],
        LookupCategory::PRIORITY_AREA => ['Khu vực ưu tiên tuyển sinh', 'Mã nội bộ', [
            'KV1' => 'Khu vực 1', 'KV2-NT' => 'Khu vực 2 nông thôn', 'KV2' => 'Khu vực 2', 'KV3' => 'Khu vực 3',
        ]],
        LookupCategory::CONTRACT_TYPE => ['Loại hợp đồng', 'Dùng cho hồ sơ giảng viên, cán bộ (TCH)', [
            'VC' => 'Viên chức', 'HDLD_XDTH' => 'Hợp đồng lao động xác định thời hạn',
            'HDLD_KXDTH' => 'Hợp đồng lao động không xác định thời hạn', 'THINH_GIANG' => 'Thỉnh giảng',
        ]],
    ];

    /** 34 tỉnh/thành từ 01/07/2025: mã => tên đầy đủ (thành phố trực thuộc trung ương ghi "Thành phố …"). */
    public const PROVINCES_2025 = [
        '01' => 'Thành phố Hà Nội', '04' => 'Tỉnh Cao Bằng', '08' => 'Tỉnh Tuyên Quang', '11' => 'Tỉnh Điện Biên',
        '12' => 'Tỉnh Lai Châu', '14' => 'Tỉnh Sơn La', '15' => 'Tỉnh Lào Cai', '19' => 'Tỉnh Thái Nguyên',
        '20' => 'Tỉnh Lạng Sơn', '22' => 'Tỉnh Quảng Ninh', '24' => 'Tỉnh Bắc Ninh', '25' => 'Tỉnh Phú Thọ',
        '31' => 'Thành phố Hải Phòng', '33' => 'Tỉnh Hưng Yên', '37' => 'Tỉnh Ninh Bình', '38' => 'Tỉnh Thanh Hóa',
        '40' => 'Tỉnh Nghệ An', '42' => 'Tỉnh Hà Tĩnh', '44' => 'Tỉnh Quảng Trị', '46' => 'Thành phố Huế',
        '48' => 'Thành phố Đà Nẵng', '51' => 'Tỉnh Quảng Ngãi', '52' => 'Tỉnh Gia Lai', '56' => 'Tỉnh Khánh Hòa',
        '66' => 'Tỉnh Đắk Lắk', '68' => 'Tỉnh Lâm Đồng', '75' => 'Tỉnh Đồng Nai', '79' => 'Thành phố Hồ Chí Minh',
        '80' => 'Tỉnh Tây Ninh', '82' => 'Tỉnh Đồng Tháp', '86' => 'Tỉnh Vĩnh Long', '91' => 'Tỉnh An Giang',
        '92' => 'Thành phố Cần Thơ', '96' => 'Tỉnh Cà Mau',
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $code => [$name, $description, $values]) {
            $category = LookupCategory::firstOrCreate(
                ['code' => $code],
                ['name' => $name, 'description' => $description, 'is_system' => true],
            );

            $order = 0;

            foreach ($values as $valueCode => $valueName) {
                $category->values()->firstOrCreate(
                    ['code' => (string) $valueCode],
                    ['name' => $valueName, 'sort_order' => ++$order],
                );
            }
        }

        $this->seedAdministrativeUnits();
    }

    private function seedAdministrativeUnits(): void
    {
        foreach (self::PROVINCES_2025 as $code => $name) {
            $this->unit(AdministrativeScheme::TwoLevel2025, AdministrativeLevel::Province, (string) $code, $name,
                str_starts_with($name, 'Thành phố') ? 'Thành phố' : 'Tỉnh');
        }

        // Mẫu dữ liệu ba cấp cũ để tra cứu địa chỉ đã nhập trước 01/07/2025
        $newHanoi = AdministrativeUnit::query()->scheme(AdministrativeScheme::TwoLevel2025)->where('code', '01')->first();
        $oldHanoi = $this->unit(AdministrativeScheme::ThreeLevelLegacy, AdministrativeLevel::Province, '01', 'Thành phố Hà Nội', 'Thành phố', successorId: $newHanoi?->id);
        $baDinh = $this->unit(AdministrativeScheme::ThreeLevelLegacy, AdministrativeLevel::District, '001', 'Quận Ba Đình', 'Quận', $oldHanoi->id);
        $this->unit(AdministrativeScheme::ThreeLevelLegacy, AdministrativeLevel::Commune, '00001', 'Phường Phúc Xá', 'Phường', $baDinh->id);
    }

    private function unit(AdministrativeScheme $scheme, AdministrativeLevel $level, string $code, string $name, string $type, ?int $parentId = null, ?int $successorId = null): AdministrativeUnit
    {
        return AdministrativeUnit::firstOrCreate(
            ['scheme' => $scheme->value, 'code' => $code],
            ['level' => $level, 'name' => $name, 'unit_type' => $type, 'parent_id' => $parentId, 'successor_id' => $successorId],
        );
    }
}
