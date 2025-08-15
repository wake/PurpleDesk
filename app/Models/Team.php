<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon_data',
        'organization_id',
    ];

    protected $appends = ['avatar_data', 'logo_url'];
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'icon_data' => 'json',
        ];
    }

    /**
     * 取得頭像數據（自動生成預設頭像）
     */
    public function getAvatarDataAttribute()
    {
        $iconData = $this->getAttributeFromArray('icon_data');
        
        if (!$iconData) {
            return \App\Helpers\IconDataHelper::generateTeamIconDefault();
        }
        
        return $iconData;
    }
    
    /**
     * 取得團隊 Logo 完整 URL（僅適用於圖片類型）
     */
    public function getLogoUrlAttribute()
    {
        $iconData = $this->icon_data;
        
        if ($iconData && is_array($iconData) && 
            isset($iconData['type']) && $iconData['type'] === 'image' && 
            isset($iconData['path'])) {
            return asset('storage/' . $iconData['path']);
        }
        
        return null;
    }

    /**
     * 團隊所屬的組織
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * 團隊的成員
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_teams')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * 團隊的領導者
     */
    public function leaders()
    {
        return $this->users()->wherePivot('role', 'lead');
    }

    /**
     * 團隊的一般成員
     */
    public function members()
    {
        return $this->users()->wherePivot('role', 'member');
    }
}