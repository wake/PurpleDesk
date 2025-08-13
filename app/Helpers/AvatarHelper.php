<?php

namespace App\Helpers;

class AvatarHelper
{
    /**
     * 頭像預設色彩調色盤
     */
    private static $defaultColors = [
        '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16', '#22c55e', 
        '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9', '#3b82f6', '#6366f1', 
        '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'
    ];
    
    /**
     * 淡色系調色盤
     */
    private static $lightColors = [
        '#fef2f2', '#fff7ed', '#fffbeb', '#fefce8', '#f7fee7', '#f0fdf4',
        '#ecfdf5', '#f0fdfa', '#ecfeff', '#f0f9ff', '#eff6ff', '#eef2ff',
        '#f5f3ff', '#faf5ff', '#fdf4ff', '#fdf2f8'
    ];
    
    /**
     * 生成個人頭像預設配置
     *
     * @param string $fullName
     * @return array
     */
    public static function generateUserAvatarDefault($fullName)
    {
        if (empty($fullName)) {
            $fullName = '使用者';
        }
        
        // 取得第一個字母（支援中文）
        $firstChar = mb_substr($fullName, 0, 1, 'UTF-8');
        
        // 根據字母選擇顏色（保持一致性）
        $colorIndex = ord(mb_convert_encoding($firstChar, 'UTF-8')) % count(self::$lightColors);
        $backgroundColor = self::$lightColors[$colorIndex];
        
        return [
            'type' => 'text',
            'text' => $firstChar,
            'backgroundColor' => $backgroundColor,
            'textColor' => '#374151', // 深灰色文字
        ];
    }
    
    /**
     * 生成組織頭像預設配置（淡紫背景+紫色建築 icon）
     *
     * @return array
     */
    public static function generateOrganizationAvatarDefault()
    {
        return [
            'type' => 'bootstrap_icon',
            'icon' => 'building',
            'backgroundColor' => '#faf5ff', // 淡紫色背景
            'iconColor' => '#a855f7', // 紫色圖標
            'style' => 'fill',
        ];
    }
    
    /**
     * 生成團隊頭像預設配置（淡藍背景+藍色團隊管理 icon）
     *
     * @return array
     */
    public static function generateTeamAvatarDefault()
    {
        return [
            'type' => 'bootstrap_icon',
            'icon' => 'people',
            'backgroundColor' => '#eff6ff', // 淡藍色背景
            'iconColor' => '#3b82f6', // 藍色圖標
            'style' => 'fill',
        ];
    }
    
    /**
     * 生成隨機色彩
     *
     * @param bool $lightColor 是否使用淡色系
     * @return string
     */
    public static function generateRandomColor($lightColor = true)
    {
        $colors = $lightColor ? self::$lightColors : self::$defaultColors;
        return $colors[array_rand($colors)];
    }
    
    /**
     * 解析頭像數據
     *
     * @param string|null $avatarData JSON 字串或 null
     * @return array|null
     */
    public static function parseAvatarData($avatarData)
    {
        if (empty($avatarData)) {
            return null;
        }
        
        // 如果是舊格式的圖片路徑，轉換為新格式
        if (!self::isJsonString($avatarData)) {
            return [
                'type' => 'image',
                'path' => $avatarData,
            ];
        }
        
        return json_decode($avatarData, true);
    }
    
    /**
     * 檢查字串是否為有效 JSON
     *
     * @param string $string
     * @return bool
     */
    private static function isJsonString($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * 將頭像數據編碼為 JSON 字串
     *
     * @param array $avatarData
     * @return string
     */
    public static function encodeAvatarData($avatarData)
    {
        return json_encode($avatarData, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 生成所有類型的測試頭像數據
     *
     * @return array
     */
    public static function generateTestAvatarData()
    {
        return [
            // 預設個人頭像
            'user_default' => self::generateUserAvatarDefault('張小明'),
            
            // 預設組織頭像
            'organization_default' => self::generateOrganizationAvatarDefault(),
            
            // 預設團隊頭像
            'team_default' => self::generateTeamAvatarDefault(),
            
            // 文字+顏色
            'text_custom' => [
                'type' => 'text',
                'text' => '王',
                'backgroundColor' => '#f0fdf4',
                'textColor' => '#059669',
            ],
            
            // Emoji+顏色
            'emoji_simple' => [
                'type' => 'emoji',
                'emoji' => '😀',
                'backgroundColor' => '#fef2f2',
            ],
            
            // Emoji+膚色+顏色
            'emoji_skin_tone' => [
                'type' => 'emoji',
                'emoji' => '👋🏻', // 揮手+淺膚色
                'backgroundColor' => '#fff7ed',
            ],
            
            // Hero Icon + 顏色 + outline
            'hero_outline' => [
                'type' => 'hero_icon',
                'icon' => 'user',
                'backgroundColor' => '#f0f9ff',
                'iconColor' => '#0ea5e9',
                'style' => 'outline',
            ],
            
            // Hero Icon + 顏色 + solid
            'hero_solid' => [
                'type' => 'hero_icon',
                'icon' => 'heart',
                'backgroundColor' => '#fdf2f8',
                'iconColor' => '#ec4899',
                'style' => 'solid',
            ],
            
            // Bootstrap Icon + 顏色 + outline
            'bs_outline' => [
                'type' => 'bootstrap_icon',
                'icon' => 'star',
                'backgroundColor' => '#fffbeb',
                'iconColor' => '#f59e0b',
                'style' => 'outline',
            ],
            
            // Bootstrap Icon + 顏色 + fill
            'bs_fill' => [
                'type' => 'bootstrap_icon',
                'icon' => 'lightning',
                'backgroundColor' => '#f7fee7',
                'iconColor' => '#84cc16',
                'style' => 'fill',
            ],
            
            // 圖片
            'image_sample' => [
                'type' => 'image',
                'path' => 'avatars/sample.jpg',
            ],
        ];
    }
}