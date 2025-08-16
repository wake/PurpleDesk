<?php

namespace App\Icon;

class Color
{
    /**
     * 預設調色盤（16 色 + 系統主色）
     */
    private static array $standardColors = [
        '#ef4444', // 紅色 Red
        '#f97316', // 橙色 Orange
        '#f59e0b', // 黃色 Amber
        '#eab308', // 黃綠色 Yellow
        '#84cc16', // 萊姆色 Lime
        '#22c55e', // 綠色 Green
        '#10b981', // 翠綠色 Emerald
        '#14b8a6', // 青綠色 Teal
        '#06b6d4', // 青色 Cyan
        '#0ea5e9', // 天空藍 Sky Blue
        '#3b82f6', // 藍色 Blue
        '#6366f1', // 靛藍色 Indigo
        '#9b6eff', // 紫色 Primary（系統主色）
        '#8b5cf6', // 紫羅蘭 Violet
        '#a855f7', // 紫色 Purple
        '#d946ef', // 紫紅色 Fuchsia
        '#ec4899', // 桃紅色 Pink
    ];
    
    /**
     * 淡色系調色盤（17 色，包含系統預設淡紫色）
     */
    private static array $lightColors = [
        '#fecaca', // 淡紅色 Light Red
        '#fed7aa', // 淡橙色 Light Orange
        '#fde68a', // 淡黃色 Light Amber
        '#fef08a', // 淡黃綠色 Light Yellow
        '#d9f99d', // 淡萊姆色 Light Lime
        '#bbf7d0', // 淡綠色 Light Green
        '#a7f3d0', // 淡翠綠色 Light Emerald
        '#99f6e4', // 淡青綠色 Light Teal
        '#a5f3fc', // 淡青色 Light Cyan
        '#bae6fd', // 淡天空藍 Light Sky
        '#dbeafe', // 淡藍色 Light Blue
        '#c7d2fe', // 淡靛藍色 Light Indigo
        '#ddd6fe', // 淡紫羅蘭 Light Violet
        '#e9d5ff', // 淡紫色 Light Purple
        '#faf5ff', // purple-50 (組織預設背景色)
        '#f5d0fe', // 淡紫紅色 Light Fuchsia
        '#fbcfe8', // 淡桃紅色 Light Pink
    ];
    
    /**
     * 淡色系背景與對應深色前景
     */
    private static array $lightColorsWithForeground = [
        '#fecaca' => '#991b1b', // 淡紅色 -> 深紅色
        '#fed7aa' => '#9a3412', // 淡橙色 -> 深橙色
        '#fde68a' => '#92400e', // 淡黃色 -> 深黃色
        '#fef08a' => '#854d0e', // 淡黃綠色 -> 深黃綠色
        '#d9f99d' => '#3f6212', // 淡萊姆色 -> 深萊姆色
        '#bbf7d0' => '#14532d', // 淡綠色 -> 深綠色
        '#a7f3d0' => '#064e3b', // 淡翠綠色 -> 深翠綠色
        '#99f6e4' => '#134e4a', // 淡青綠色 -> 深青綠色
        '#a5f3fc' => '#164e63', // 淡青色 -> 深青色
        '#bae6fd' => '#0c4a6e', // 淡天空藍 -> 深天空藍
        '#dbeafe' => '#1e3a8a', // 淡藍色 -> 深藍色
        '#c7d2fe' => '#312e81', // 淡靛藍色 -> 深靛藍色
        '#ddd6fe' => '#4c1d95', // 淡紫羅蘭 -> 深紫羅蘭
        '#e9d5ff' => '#581c87', // 淡紫色 -> 深紫色
        '#faf5ff' => '#7c3aed', // purple-50 -> purple-600
        '#f5d0fe' => '#701a75', // 淡紫紅色 -> 深紫紅色
        '#fbcfe8' => '#831843', // 淡桃紅色 -> 深桃紅色
    ];
    
    /**
     * 基礎色彩（黑白灰）
     */
    private static array $grayColors = [
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
     * 取得標準調色盤
     */
    public static function getStandardColors(): array
    {
        return self::$standardColors;
    }
    
    /**
     * 取得淡色系調色盤
     */
    public static function getLightColors(): array
    {
        return self::$lightColors;
    }
    
    /**
     * 取得淡色系與對應深色前景
     */
    public static function getLightColorsWithForeground(): array
    {
        return self::$lightColorsWithForeground;
    }
    
    /**
     * 取得灰階色彩
     */
    public static function getGrayColors(): array
    {
        return self::$grayColors;
    }
    
    /**
     * 取得所有允許的背景色
     */
    public static function getAllowedBackgroundColors(): array
    {
        return array_merge(
            self::$standardColors,
            self::$lightColors,
            self::$grayColors
        );
    }
    
    /**
     * 檢查是否為允許的背景色
     */
    public static function isAllowedBackgroundColor(string $color): bool
    {
        $normalizedColor = self::normalizeHexColor($color);
        if ($normalizedColor === null) {
            return false;
        }
        
        return in_array($normalizedColor, self::getAllowedBackgroundColors(), true);
    }
    
    /**
     * 計算顏色的相對亮度（W3C 公式）
     */
    public static function getLuminance(string $hexColor): float
    {
        $hex = str_replace('#', '', $hexColor);
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    }
    
    /**
     * 根據背景色取得對比文字顏色
     */
    public static function getContrastColor(string $backgroundColor): string
    {
        $luminance = self::getLuminance($backgroundColor);
        
        // 亮度大於 0.6 使用深灰色，否則使用白色（調整閾值以配合紫色主色）
        return $luminance > 0.6 ? '#1f2937' : '#ffffff';
    }
    
    /**
     * 隨機生成標準色
     */
    public static function randomStandardColor(): string
    {
        $colors = self::getStandardColors();
        return $colors[array_rand($colors)];
    }
    
    /**
     * 隨機生成淡色
     */
    public static function randomLightColor(): string
    {
        $colors = self::getLightColors();
        return $colors[array_rand($colors)];
    }
    
    /**
     * 隨機生成淡色與對應前景色
     */
    public static function randomLightColorWithForeground(): array
    {
        $backgroundColor = self::randomLightColor();
        $foregroundColor = self::$lightColorsWithForeground[$backgroundColor];
        
        return [
            'background' => $backgroundColor,
            'foreground' => $foregroundColor,
        ];
    }
    
    /**
     * 檢查是否為有效的 hex 顏色
     */
    public static function isValidHexColor(string $color): bool
    {
        return preg_match('/^#[0-9a-fA-F]{6}$/', $color) === 1;
    }
    
    /**
     * 標準化 hex 顏色（轉小寫，確保有 # 前綴）
     */
    public static function normalizeHexColor(string $color): ?string
    {
        // 移除空白
        $color = trim($color);
        
        // 如果沒有 # 前綴，加上它
        if (!str_starts_with($color, '#')) {
            $color = '#' . $color;
        }
        
        // 轉換為小寫
        $color = strtolower($color);
        
        // 驗證格式
        if (!self::isValidHexColor($color)) {
            return null;
        }
        
        return $color;
    }
}