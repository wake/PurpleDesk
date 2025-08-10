<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'email',
        'password',
        'avatar',
        'locale',
        'timezone',
        'email_notifications',
        'browser_notifications',
        'theme',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_url'];

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
            'email_notifications' => 'boolean',
            'browser_notifications' => 'boolean',
        ];
    }

    /**
     * 取得頭像完整 URL
     */
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return null;
        }
        
        return asset('storage/' . $this->avatar);
    }

    /**
     * 使用者所屬的組織
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'user_organizations')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * 使用者所屬的團隊
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'user_teams')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * 取得使用者的主要組織（第一個加入的）
     */
    public function primaryOrganization()
    {
        return $this->organizations()->oldest('user_organizations.created_at')->first();
    }

    /**
     * 檢查使用者是否屬於特定組織
     */
    public function belongsToOrganization($organizationId): bool
    {
        return $this->organizations()->where('organization_id', $organizationId)->exists();
    }

    /**
     * 檢查使用者是否屬於特定團隊
     */
    public function belongsToTeam($teamId): bool
    {
        return $this->teams()->where('team_id', $teamId)->exists();
    }
}
