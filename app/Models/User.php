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
        'account',
        'full_name',
        'display_name',
        'email',
        'password',
        'avatar_data',
        'is_admin',
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
            'is_admin' => 'boolean',
            'birth_date' => 'date',
            'last_login_at' => 'datetime',
            'avatar_data' => 'json',
        ];
    }

    /**
     * 取得頭像數據（自動生成預設頭像）
     */
    public function getAvatarDataAttribute()
    {
        $avatarData = $this->getAttributeFromArray('avatar_data');
        
        if (!$avatarData) {
            return \App\Helpers\IconDataHelper::generateUserIconDefault($this->full_name ?: $this->display_name ?: $this->account);
        }
        
        return $avatarData;
    }
    
    /**
     * 取得頭像完整 URL（僅適用於圖片類型）
     */
    public function getAvatarUrlAttribute()
    {
        $avatarData = $this->avatar_data;
        
        if ($avatarData && is_array($avatarData) && 
            isset($avatarData['type']) && $avatarData['type'] === 'image' && 
            isset($avatarData['path'])) {
            return asset('storage/' . $avatarData['path']);
        }
        
        return null;
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
        return $this->organizations()->where('organizations.id', $organizationId)->exists();
    }

    /**
     * 檢查使用者是否屬於特定團隊
     */
    public function belongsToTeam($teamId): bool
    {
        return $this->teams()->where('teams.id', $teamId)->exists();
    }

    /**
     * 取得顯示名稱 (display_name > full_name > account)
     */
    public function getDisplayNameAttribute($value)
    {
        return $value ?: ($this->full_name ?: $this->account);
    }

    /**
     * 取得用於顯示的名稱
     */
    public function getVisibleNameAttribute()
    {
        return $this->display_name ?: ($this->full_name ?: $this->account);
    }
}
