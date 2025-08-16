<?php

namespace App\Icon\Types;

use App\Icon\Color;
use App\Icon\IconTypeInterface;

class BootstrapIcon implements IconTypeInterface
{
    public function getType(): string
    {
        return 'bootstrap_icon';
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
        if (!isset($data['type']) || $data['type'] !== 'bootstrap_icon') {
            return false;
        }
        
        // 檢查必要欄位
        foreach (['icon', 'style', 'iconColor'] as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        
        // 檢查樣式
        if (!in_array($data['style'], ['outline', 'fill'], true)) {
            return false;
        }
        
        // 檢查圖標顏色
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
        $style = rand(0, 1) ? 'outline' : 'fill';
        
        return [
            'type' => 'bootstrap_icon',
            'icon' => $this->getRandomIcon(),
            'style' => $style,
            'backgroundColor' => $colorCombination['background'],
            'iconColor' => $colorCombination['foreground'],
        ];
    }
    
    /**
     * 取得團隊的預設圖標
     */
    public function getDefaultForTeam(): array
    {
        return [
            'type' => 'bootstrap_icon',
            'icon' => 'bi-people',
            'style' => 'outline',
            'backgroundColor' => '#dbeafe', // blue-100
            'iconColor' => '#2563eb', // blue-600
        ];
    }
    
    /**
     * 檢查圖標名稱是否有效
     */
    public function isValidIcon(string $iconName): bool
    {
        return in_array($iconName, $this->getAllIcons(), true);
    }
    
    /**
     * 取得所有圖標
     */
    private function getAllIcons(): array
    {
        $categories = config('icon.bootstrap_icons.categories', []);
        return array_merge(...array_values($categories));
    }
    
    /**
     * 取得隨機圖標
     */
    private function getRandomIcon(): string
    {
        $all = $this->getAllIcons();
        return $all[array_rand($all)];
    }
}