<?php

namespace App\Icon\Types;

use App\Icon\Color;
use App\Icon\IconTypeInterface;

class HeroIcon implements IconTypeInterface
{
    public function getType(): string
    {
        return 'hero_icon';
    }
    
    public function getRequiredFields(): array
    {
        return ['type', 'icon', 'style', 'iconColor'];
    }
    
    public function getOptionalFields(): array
    {
        return ['backgroundColor'];
    }
    
    public function validate(array $data): bool
    {
        // 檢查類型
        if (!isset($data['type']) || $data['type'] !== 'hero_icon') {
            return false;
        }
        
        // 檢查必要欄位
        foreach (['icon', 'style', 'iconColor'] as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        
        // 檢查樣式
        if (!in_array($data['style'], ['outline', 'solid'], true)) {
            return false;
        }
        
        // 檢查圖標顏色是否為有效的 hex 顏色
        $iconColor = Color::normalizeHexColor($data['iconColor']);
        if ($iconColor === null) {
            return false;
        }
        
        // 如果有背景色，檢查是否有效
        if (isset($data['backgroundColor'])) {
            if (!Color::isAllowedBackgroundColor($data['backgroundColor'])) {
                return false;
            }
        }
        
        return true;
    }
    
    public function generateRandom(): array
    {
        // 使用成對的顏色組合
        $colorCombination = Color::randomColorCombination();
        $style = rand(0, 1) ? 'outline' : 'solid';
        
        return [
            'type' => 'hero_icon',
            'icon' => $this->getRandomIcon(),
            'style' => $style,
            'backgroundColor' => $colorCombination['background'],
            'iconColor' => $colorCombination['foreground'],
        ];
    }
    
    /**
     * 取得組織的預設圖標
     */
    public function getDefaultForOrganization(): array
    {
        return [
            'type' => 'hero_icon',
            'icon' => 'office-building',
            'style' => 'outline',
            'backgroundColor' => '#faf5ff', // purple-50
            'iconColor' => '#7c3aed', // purple-600
        ];
    }
    
    /**
     * 檢查圖標名稱是否有效
     */
    public function isValidIcon(string $iconName): bool
    {
        $icons = config('icon.heroicons.icons', []);
        return in_array($iconName, $icons, true);
    }
    
    /**
     * 取得隨機圖標
     */
    private function getRandomIcon(): string
    {
        $icons = config('icon.heroicons.icons', []);
        return $icons[array_rand($icons)];
    }
}