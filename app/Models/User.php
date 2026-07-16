<?php

namespace App\Models;

use App\Domain\Auth\Models\LoginHistory;
use App\Domain\Auth\Models\OtpCode;
use App\Domain\Auth\Models\UserDevice;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Organization\Models\Opd;
use App\Domain\Organization\Models\Unit;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasRoles;
    use HasUuids;
    use Notifiable;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'nik',
        'password',
        'avatar',
        'opd_id',
        'unit_id',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'preferences',
        'email_verified_at',
        'phone_verified_at',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'preferences' => 'array',
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class);
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function reportedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'reporter_id');
    }

    public function assignedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'current_pic_id');
    }
}
