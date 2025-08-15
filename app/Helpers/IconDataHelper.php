<?php

namespace App\Helpers;

class IconDataHelper
{
    /**
     * 圖標顯示預設色彩調色盤 (與 ColorPicker 完全一致)
     */
    private static $defaultColors = [
        '#ef4444', // 紅色 Red
        '#f97316', // 橙色 Orange
        '#f59e0b', // 黃色 Amber
        '#eab308', // 黃綠色 Yellow
        '#84cc16', // 萊色 Lime
        '#22c55e', // 綠色 Green
        '#10b981', // 翠綠色 Emerald
        '#14b8a6', // 青綠色 Teal
        '#06b6d4', // 青色 Cyan
        '#0ea5e9', // 天空藍 Sky Blue
        '#3b82f6', // 藍色 Blue
        '#6366f1', // 靛藍色 Indigo (primary)
        '#8b5cf6', // 紫羅蘭 Violet
        '#a855f7', // 紫色 Purple
        '#d946ef', // 紫紅色 Fuchsia
        '#ec4899', // 桃紅色 Pink
    ];
    
    /**
     * 淡色系調色盤 (與 ColorPicker 完全一致)
     */
    private static $lightColors = [
        '#fef2f2', // 淡紅色 Light Red
        '#fff7ed', // 淡橙色 Light Orange
        '#fffbeb', // 淡黃色 Light Amber
        '#fefce8', // 淡黃綠色 Light Yellow
        '#f7fee7', // 淡萊色 Light Lime
        '#f0fdf4', // 淡綠色 Light Green
        '#ecfdf5', // 淡翠綠色 Light Emerald
        '#f0fdfa', // 淡青綠色 Light Teal
        '#ecfeff', // 淡青色 Light Cyan
        '#f0f9ff', // 淡天空藍 Light Sky
        '#eff6ff', // 淡藍色 Light Blue
        '#eef2ff', // 淡靛藍色 Light Indigo
        '#f5f3ff', // 淡紫羅蘭 Light Violet
        '#faf5ff', // 淡紫色 Light Purple
        '#fdf4ff', // 淡紫紅色 Light Fuchsia
        '#fdf2f8', // 淡桃紅色 Light Pink
    ];
    
    /**
     * 基礎色彩調色盤 (黑、白、灰色系)
     */
    private static $basicColors = [
        '#000000', // 黑色
        '#ffffff', // 白色
        '#f9fafb', // gray-50
        '#f3f4f6', // gray-100
        '#e5e7eb', // gray-200
        '#d1d5db', // gray-300
        '#9ca3af', // gray-400
        '#6b7280', // gray-500
        '#374151', // gray-700
        '#1f2937', // gray-800
        '#111827', // gray-900
    ];
    
    /**
     * 生成個人圖標預設配置（適用於頭像顯示）
     *
     * @param string $fullName
     * @return array
     */
    public static function generateUserIconDefault($fullName)
    {
        if (empty($fullName)) {
            $fullName = '使用者';
        }
        
        // 取得前兩個字符（支援中文，最多2字）
        $displayText = mb_substr($fullName, 0, 2, 'UTF-8');
        
        return [
            'type' => 'text',
            'text' => $displayText,
            'backgroundColor' => '#6366f1', // primary-500 背景
            'textColor' => '#ffffff', // 白色文字
        ];
    }
    
    /**
     * 生成組織圖標預設配置（淡紫背景+深紫色 office-building icon）
     *
     * @return array
     */
    public static function generateOrganizationIconDefault()
    {
        return [
            'type' => 'hero_icon',
            'icon' => 'office-building',
            'backgroundColor' => '#faf5ff', // 淡紫色背景 (purple-50)
            'iconColor' => '#7c3aed', // 深紫色圖標 (purple-600)
            'style' => 'outline',
        ];
    }
    
    /**
     * 生成團隊圖標預設配置（淡藍背景+深藍色 bi-people icon）
     *
     * @return array
     */
    public static function generateTeamIconDefault()
    {
        return [
            'type' => 'bootstrap_icon',
            'icon' => 'people',
            'backgroundColor' => '#dbeafe', // 淡藍色背景 (blue-100)
            'iconColor' => '#2563eb', // 深藍色圖標 (blue-600)
            'style' => 'fill',
        ];
    }
    
    /**
     * 獲取所有允許的背景顏色 (IconPicker ColorPicker 預設顏色)
     *
     * @return array
     */
    public static function getAllowedBackgroundColors()
    {
        return array_merge(self::$defaultColors, self::$lightColors, self::$basicColors);
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
     * 驗證背景顏色是否為允許的顏色
     *
     * @param string $color
     * @return bool
     */
    public static function isAllowedBackgroundColor($color)
    {
        return in_array(strtolower($color), array_map('strtolower', self::getAllowedBackgroundColors()));
    }
    
    /**
     * 解析圖標數據（相容舊版頭像格式）
     *
     * @param string|null $iconData JSON 字串或 null
     * @return array|null
     */
    public static function parseIconData($iconData)
    {
        if (empty($iconData)) {
            return null;
        }
        
        // 如果是舊格式的圖片路徑，轉換為新格式
        if (!self::isJsonString($iconData)) {
            return [
                'type' => 'image',
                'path' => $iconData,
            ];
        }
        
        return json_decode($iconData, true);
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
     * 將圖標數據編碼為 JSON 字串
     *
     * @param array $iconData
     * @return string
     */
    public static function encodeIconData($iconData)
    {
        return json_encode($iconData, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 生成所有類型的測試圖標數據 (符合新規範)
     *
     * @return array
     */
    public static function generateTestIconData()
    {
        return [
            // 預設個人圖標 (primary-500 背景 + 白色文字)
            'user_default' => self::generateUserIconDefault('張小明'),
            
            // 預設組織圖標 (Hero Icon office-building)
            'organization_default' => self::generateOrganizationIconDefault(),
            
            // 預設團隊圖標 (Bootstrap Icon bi-people)
            'team_default' => self::generateTeamIconDefault(),
            
            // 文字+淡色背景+深色文字 (符合規範)
            'text_light_bg' => [
                'type' => 'text',
                'text' => '王',
                'backgroundColor' => '#f0fdf4', // 淡綠色 (允許的顏色)
                'textColor' => '#374151', // 深灰色 (允許的深色)
            ],
            
            // 文字+深色背景+白色文字 (符合規範)
            'text_dark_bg' => [
                'type' => 'text',
                'text' => '李',
                'backgroundColor' => '#22c55e', // 綠色 (允許的顏色)
                'textColor' => '#ffffff', // 白色 (允許的顏色)
            ],
            
            // Emoji+淡色背景
            'emoji_simple' => [
                'type' => 'emoji',
                'emoji' => '😀',
                'backgroundColor' => '#fef2f2', // 淡紅色 (允許的顏色)
            ],
            
            // Emoji+膚色+淡色背景
            'emoji_skin_tone' => [
                'type' => 'emoji',
                'emoji' => '👋🏻', // 揮手+淺膚色
                'backgroundColor' => '#fff7ed', // 淡橙色 (允許的顏色)
            ],
            
            // Hero Icon + 淡色背景 + 深色圖標
            'hero_outline_light' => [
                'type' => 'hero_icon',
                'icon' => 'user',
                'backgroundColor' => '#f0f9ff', // 淡藍色 (允許的顏色)
                'iconColor' => '#374151', // 深灰色 (允許的深色)
                'style' => 'outline',
            ],
            
            // Hero Icon + 深色背景 + 白色圖標
            'hero_solid_dark' => [
                'type' => 'hero_icon',
                'icon' => 'heart',
                'backgroundColor' => '#ec4899', // 桃紅色 (允許的顏色)
                'iconColor' => '#ffffff', // 白色 (允許的顏色)
                'style' => 'solid',
            ],
            
            // Bootstrap Icon + 淡色背景 + 深色圖標
            'bs_outline_light' => [
                'type' => 'bootstrap_icon',
                'icon' => 'star',
                'backgroundColor' => '#fffbeb', // 淡黃色 (允許的顏色)
                'iconColor' => '#374151', // 深灰色 (允許的深色)
                'style' => 'outline',
            ],
            
            // Bootstrap Icon + 深色背景 + 白色圖標
            'bs_fill_dark' => [
                'type' => 'bootstrap_icon',
                'icon' => 'lightning',
                'backgroundColor' => '#84cc16', // 萊色 (允許的顏色)
                'iconColor' => '#ffffff', // 白色 (允許的顏色)
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