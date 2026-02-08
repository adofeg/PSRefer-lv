<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'profileable_id',
        'profileable_type',
        'phone',
        'logo_url',
        'theme',
    ];

    protected $appends = [
        'role',
        'category',
        'phone',
        'logo_url',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'is_active' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    // Relationships

    public function profileable()
    {
        return $this->morphTo();
    }

    public function associateProfile()
    {
        return $this->profileable_type === Associate::class ? $this->profileable : null;
    }

    public function employeeProfile()
    {
        return $this->profileable_type === Employee::class ? $this->profileable : null;
    }

    // Network Logic - Redirect to Associate Profile if possible
    public function getAssociateAttribute()
    {
        return $this->profileable_type === Associate::class ? $this->profileable : null;
    }

    // Helper to access common profile fields
    public function getLogoUrlAttribute()
    {
        return $this->attributes['logo_url']
            ?? optional($this->profileable)->logo_url
            ?? 'https://ui-avatars.com/api/?name='.urlencode($this->name);
    }

    public function getRoleAttribute()
    {
        return $this->getRoleNames()->first();
    }

    public function getPhoneAttribute($value)
    {
        return $value ?? optional($this->profileable)->phone;
    }

    public function getBalanceAttribute($value)
    {
        return optional($this->profileable)->balance ?? 0;
    }

    public function getCategoryAttribute($value)
    {
        return optional($this->profileable)->category;
    }

    public function getPaymentInfoAttribute($value)
    {
        return optional($this->profileable)->payment_info;
    }
}
