<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\Auth\Concerns\HasRoles;
use App\Modules\Auth\Enums\AccountStatus;
use App\Modules\Auth\Enums\ProfileType;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Tài khoản đăng nhập. Các cột bảo mật (buộc đổi mật khẩu, khóa tạm, đếm lần sai…) chỉ được
 * thay đổi qua App\Modules\Auth\Services\LoginService và PasswordService, không gán hàng loạt.
 */
#[Fillable(['username', 'name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'temporary_password_expires_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'failed_login_started_at' => 'datetime',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'status' => AccountStatus::class,
            'status_changed_at' => 'datetime',
            'deactivate_at' => 'datetime',
            'profile_type' => ProfileType::class,
        ];
    }

    /**
     * Được đăng nhập không: tài khoản hoạt động hoặc chỉ đọc, chưa tới ngày hẹn ngừng (BR-AUTH-07).
     * Khóa tạm do nhập sai mật khẩu kiểm tra riêng bằng isLockedOut().
     */
    public function canSignIn(): bool
    {
        if ($this->deactivate_at !== null && $this->deactivate_at->isPast()) {
            return false;
        }

        return ($this->status ?? AccountStatus::Active)->canSignIn();
    }

    /** Tài khoản đang hoạt động đầy đủ: trạng thái active, chưa tới ngày hẹn ngừng (dùng khi đếm ADMIN còn lại). */
    public function scopeOperational(Builder $query): void
    {
        $query->where('status', AccountStatus::Active)
            ->where(fn (Builder $q) => $q->whereNull('deactivate_at')->orWhere('deactivate_at', '>', now()));
    }

    /** Chế độ chỉ đọc (sinh viên đã tốt nghiệp): xem được, không thao tác ghi. */
    public function isReadOnly(): bool
    {
        return $this->status === AccountStatus::ReadOnly;
    }

    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordHistory::class);
    }

    /** Đang bị khóa tạm vì nhập sai mật khẩu nhiều lần (BR-AUTH-06). */
    public function isLockedOut(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }
}
