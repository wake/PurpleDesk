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
        'avatar',
        'organization_id',
    ];

    protected $appends = ['avatar_url'];

    /**
     * 取得團隊頭像完整 URL
     */
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return null;
        }
        
        return asset('storage/' . $this->avatar);
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