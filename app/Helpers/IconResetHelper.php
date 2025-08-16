<?php

namespace App\Helpers;

class IconResetHelper
{
    /**
     * 重置組織頭像為預設狀態
     *
     * @param \App\Models\Organization $organization
     * @return void
     */
    public static function resetOrganizationAvatar($organization)
    {
        $organization->update([
            'avatar' => null
        ]);
    }
    
    /**
     * 重置團隊頭像為預設狀態
     *
     * @param \App\Models\Team $team
     * @return void
     */
    public static function resetTeamAvatar($team)
    {
        $team->update([
            'avatar' => null
        ]);
    }
    
    /**
     * 重置用戶頭像為預設狀態
     *
     * @param \App\Models\User $user
     * @return void
     */
    public static function resetUserAvatar($user)
    {
        $user->update([
            'avatar' => null
        ]);
    }
    
    /**
     * 批量重置組織頭像
     *
     * @param array $organizationIds
     * @return int 重置的數量
     */
    public static function resetMultipleOrganizationAvatars(array $organizationIds)
    {
        return \App\Models\Organization::whereIn('id', $organizationIds)
            ->update(['avatar' => null]);
    }
    
    /**
     * 批量重置團隊頭像
     *
     * @param array $teamIds
     * @return int 重置的數量
     */
    public static function resetMultipleTeamAvatars(array $teamIds)
    {
        return \App\Models\Team::whereIn('id', $teamIds)
            ->update(['avatar' => null]);
    }
    
    /**
     * 批量重置用戶頭像
     *
     * @param array $userIds
     * @return int 重置的數量
     */
    public static function resetMultipleUserAvatars(array $userIds)
    {
        return \App\Models\User::whereIn('id', $userIds)
            ->update(['avatar' => null]);
    }
    
    /**
     * 獲取預設頭像配置（用於前端預覽）
     *
     * @param string $type 類型: 'user', 'organization', 'team'
     * @param string|null $name 名稱（用於用戶頭像生成）
     * @return array
     */
    public static function getDefaultAvatarPreview($type, $name = null)
    {
        switch ($type) {
            case 'organization':
                return IconDataHelper::generateOrganizationIconDefault();
                
            case 'team':
                return IconDataHelper::generateTeamIconDefault();
                
            case 'user':
                return IconDataHelper::generateUserIconDefault($name ?: '使用者');
                
            default:
                throw new \InvalidArgumentException("不支援的類型: {$type}");
        }
    }
    
    /**
     * 驗證頭像數據是否符合規範
     *
     * @param array $avatarData
     * @return array 驗證結果 ['valid' => bool, 'errors' => array]
     */
    public static function validateAvatarData($avatarData)
    {
        $errors = [];
        
        if (!isset($avatarData['type'])) {
            $errors[] = '缺少必要的 type 欄位';
            return ['valid' => false, 'errors' => $errors];
        }
        
        $allowedTypes = ['text', 'emoji', 'hero_icon', 'bootstrap_icon', 'image'];
        if (!in_array($avatarData['type'], $allowedTypes)) {
            $errors[] = "不支援的頭像類型: {$avatarData['type']}";
        }
        
        // 檢查背景顏色
        if (isset($avatarData['backgroundColor'])) {
            if (!IconDataHelper::isAllowedBackgroundColor($avatarData['backgroundColor'])) {
                $errors[] = "背景顏色不在允許清單中: {$avatarData['backgroundColor']}";
            }
        }
        
        // 檢查文字/圖標顏色
        $allowedContentColors = ['#ffffff', '#000000', '#374151', '#1f2937'];
        
        if (isset($avatarData['textColor'])) {
            if (!in_array(strtolower($avatarData['textColor']), array_map('strtolower', $allowedContentColors))) {
                $errors[] = "文字顏色只能是白色或深色: {$avatarData['textColor']}";
            }
        }
        
        if (isset($avatarData['iconColor'])) {
            if (!in_array(strtolower($avatarData['iconColor']), array_map('strtolower', $allowedContentColors))) {
                $errors[] = "圖標顏色只能是白色或深色: {$avatarData['iconColor']}";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}