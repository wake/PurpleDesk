<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'description',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_data', 'logo_url'];
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'avatar' => 'json',
        ];
    }

    /**
     * 取得頭像數據（自動生成預設頭像）
     */
    public function getAvatarDataAttribute()
    {
        if (!$this->avatar) {
            return \App\Helpers\IconDataHelper::generateOrganizationIconDefault();
        }
        
        return $this->avatar;
    }
    
    /**
     * 取得組織 Logo 完整 URL（僅適用於圖片類型）
     */
    public function getLogoUrlAttribute()
    {
        $avatarData = $this->avatar_data;
        
        if ($avatarData && $avatarData['type'] === 'image' && isset($avatarData['path'])) {
            return asset('storage/' . $avatarData['path']);
        }
        
        return null;
    }

    /**
     * 組織的成員
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_organizations')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * 組織的團隊
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * 組織的擁有者
     */
    public function owners()
    {
        return $this->users()->wherePivot('role', 'owner');
    }

    /**
     * 組織的管理員
     */
    public function admins()
    {
        return $this->users()->wherePivot('role', 'admin');
    }

    /**
     * 組織的一般成員
     */
    public function members()
    {
        return $this->users()->wherePivot('role', 'member');
    }
}
