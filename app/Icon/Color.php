<?php

namespace App\Icon;

class Color
{
    /**
     * 亮度閾值（用於判斷使用深色或淺色前景）
     */
    private static ?float $luminanceThreshold = null;
    
    /**
     * 深色前景色（用於亮背景）
     */
    private static ?string $darkForegroundColor = null;
    
    /**
     * 淺色前景色（用於暗背景）
     */
    private static ?string $lightForegroundColor = null;
    
    /**
     * 初始化配置值
     */
    private static function initializeConfig(): void
    {
        if (self::$luminanceThreshold === null) {
            self::$luminanceThreshold = config('icon.colors.luminance_threshold', 0.6);
            self::$darkForegroundColor = config('icon.colors.dark_foreground_color', '#1f2937');
            self::$lightForegroundColor = config('icon.colors.light_foreground_color', '#ffffff');
        }
    }
    
    /**
     * 取得標準調色盤
     */
    public static function getStandardColors(): array
    {
        return config('icon.colors.standard_colors', []);
    }
    
    /**
     * 取得淡色系調色盤
     */
    public static function getLightColors(): array
    {
        return config('icon.colors.light_colors', []);
    }
    
    /**
     * 取得淡色系與對應深色前景
     */
    public static function getLightColorsWithForeground(): array
    {
        return config('icon.colors.light_colors_with_foreground', []);
    }
    
    /**
     * 取得灰階色彩
     */
    public static function getGrayColors(): array
    {
        return config('icon.colors.gray_colors', []);
    }
    
    /**
     * 取得所有允許的背景色
     */
    public static function getAllowedBackgroundColors(): array
    {
        return array_merge(
            config('icon.colors.standard_colors', []),
            config('icon.colors.light_colors', []),
            config('icon.colors.gray_colors', [])
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
        self::initializeConfig();
        $luminance = self::getLuminance($backgroundColor);
        
        // 使用可配置的閾值和前景色
        return $luminance > self::$luminanceThreshold 
            ? self::$darkForegroundColor 
            : self::$lightForegroundColor;
    }
    
    /**
     * 取得亮度閾值
     */
    public static function getLuminanceThreshold(): float
    {
        self::initializeConfig();
        return self::$luminanceThreshold;
    }
    
    /**
     * 設定亮度閾值
     */
    public static function setLuminanceThreshold(float $threshold): void
    {
        self::initializeConfig();
        self::$luminanceThreshold = $threshold;
    }
    
    /**
     * 取得深色前景色
     */
    public static function getDarkForegroundColor(): string
    {
        self::initializeConfig();
        return self::$darkForegroundColor;
    }
    
    /**
     * 設定深色前景色
     */
    public static function setDarkForegroundColor(string $color): void
    {
        self::initializeConfig();
        self::$darkForegroundColor = self::normalizeHexColor($color) ?? $color;
    }
    
    /**
     * 取得淺色前景色
     */
    public static function getLightForegroundColor(): string
    {
        self::initializeConfig();
        return self::$lightForegroundColor;
    }
    
    /**
     * 設定淺色前景色
     */
    public static function setLightForegroundColor(string $color): void
    {
        self::initializeConfig();
        self::$lightForegroundColor = self::normalizeHexColor($color) ?? $color;
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
        $lightColorsWithForeground = config('icon.colors.light_colors_with_foreground', []);
        $foregroundColor = $lightColorsWithForeground[$backgroundColor] ?? self::getContrastColor($backgroundColor);
        
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
    
    /**
     * 驗證前景色與背景色的組合是否有效
     * 
     * @param string $backgroundColor 背景色
     * @param string $foregroundColor 前景色
     * @return bool
     */
    public static function validateColorCombination(string $backgroundColor, string $foregroundColor): bool
    {
        // 標準化顏色
        $backgroundColor = self::normalizeHexColor($backgroundColor);
        $foregroundColor = self::normalizeHexColor($foregroundColor);
        
        if ($backgroundColor === null || $foregroundColor === null) {
            return false;
        }
        
        $lightColorsWithForeground = config('icon.colors.light_colors_with_foreground', []);
        $standardColors = config('icon.colors.standard_colors', []);
        
        // 檢查是否為預設淡色背景（優先檢查）
        if (array_key_exists($backgroundColor, $lightColorsWithForeground)) {
            // 預設淡色背景應該使用對應的深色前景
            return $foregroundColor === $lightColorsWithForeground[$backgroundColor];
        }
        
        // 檢查是否為標準深色背景
        if (in_array($backgroundColor, $standardColors, true)) {
            // 標準深色背景應該使用白色前景
            self::initializeConfig();
            return $foregroundColor === self::$lightForegroundColor;
        }
        
        // 非預設顏色，根據亮度判斷
        $expectedForeground = self::getContrastColor($backgroundColor);
        return $foregroundColor === $expectedForeground;
    }
    
    /**
     * 隨機生成前景色與背景色的組合
     * 
     * @param bool $completelyRandom 是否完全隨機（不限於預設顏色）
     * @return array ['background' => string, 'foreground' => string]
     */
    public static function randomColorCombination(bool $completelyRandom = false): array
    {
        if ($completelyRandom) {
            // 完全隨機生成
            $backgroundColor = self::generateRandomHexColor();
            $foregroundColor = self::getContrastColor($backgroundColor);
            
            return [
                'background' => $backgroundColor,
                'foreground' => $foregroundColor,
            ];
        }
        
        // 從預設顏色中隨機選擇
        $allBackgrounds = self::getAllowedBackgroundColors();
        $backgroundColor = $allBackgrounds[array_rand($allBackgrounds)];
        
        $lightColorsWithForeground = config('icon.colors.light_colors_with_foreground', []);
        
        // 根據背景色類型決定前景色
        if (array_key_exists($backgroundColor, $lightColorsWithForeground)) {
            // 淡色背景，使用對應的深色前景
            $foregroundColor = $lightColorsWithForeground[$backgroundColor];
        } else {
            // 其他背景，根據亮度自動判斷
            $foregroundColor = self::getContrastColor($backgroundColor);
        }
        
        return [
            'background' => $backgroundColor,
            'foreground' => $foregroundColor,
        ];
    }
    
    /**
     * 生成隨機的 hex 顏色
     * 
     * @return string
     */
    private static function generateRandomHexColor(): string
    {
        return sprintf('#%06x', mt_rand(0, 0xFFFFFF));
    }
}