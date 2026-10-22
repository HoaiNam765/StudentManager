<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Soạn thông báo và lịch sử đã gửi (FR-NOT-006, 009, 015; FE #30 cổng quản trị, #31 cổng giảng viên). Một view cho hai
 * cổng; API theo cổng của route. Chỉ trả view; phạm vi đối tượng kiểm tra ở máy chủ (AudienceResolver).
 */
class AnnouncementPageController extends Controller
{
    public function index(Request $request): View
    {
        $portal = Str::before($request->route()->getName(), '.');
        /** @var User $user */
        $user = $request->user();

        return view('notification.compose', ['pageData' => [
            'portal' => $portal,
            'api' => [
                'options' => route("{$portal}.announcements.options"),
                'preview' => route("{$portal}.announcements.preview"),
                'history' => route("{$portal}.announcements.index"),
                'store' => route("{$portal}.announcements.store"),
                'revoke' => $portal === 'admin' ? route('admin.announcements.revoke', ['notification' => '__ID__']) : null,
                'groupStore' => route("{$portal}.recipient-groups.store"),
                'groupDelete' => route("{$portal}.recipient-groups.destroy", ['recipientGroup' => '__ID__']),
            ],
            'can' => ['revoke' => $portal === 'admin' && $user->hasRole(Role::ADMIN)],
        ]]);
    }
}
