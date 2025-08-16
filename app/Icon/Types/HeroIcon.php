<?php

namespace App\Icon\Types;

use App\Icon\Color;
use App\Icon\IconTypeInterface;

class HeroIcon implements IconTypeInterface
{
    /**
     * 常用的 Hero Icons 列表
     */
    private array $commonIcons = [
        'academic-cap', 'adjustments', 'annotation', 'archive', 'arrow-circle-down',
        'arrow-circle-left', 'arrow-circle-right', 'arrow-circle-up', 'arrow-down',
        'arrow-left', 'arrow-narrow-down', 'arrow-narrow-left', 'arrow-narrow-right',
        'arrow-narrow-up', 'arrow-right', 'arrow-up', 'at-symbol', 'backspace',
        'badge-check', 'ban', 'beaker', 'bell', 'book-open', 'bookmark',
        'bookmark-alt', 'briefcase', 'calendar', 'camera', 'cash', 'chart-bar',
        'chart-pie', 'chart-square-bar', 'chat', 'chat-alt', 'chat-alt-2',
        'check', 'check-circle', 'chevron-double-down', 'chevron-double-left',
        'chevron-double-right', 'chevron-double-up', 'chevron-down', 'chevron-left',
        'chevron-right', 'chevron-up', 'chip', 'clipboard', 'clipboard-check',
        'clipboard-copy', 'clipboard-list', 'clock', 'cloud', 'cloud-download',
        'cloud-upload', 'code', 'cog', 'collection', 'color-swatch', 'credit-card',
        'cube', 'cube-transparent', 'currency-bangladeshi', 'currency-dollar',
        'currency-euro', 'currency-pound', 'currency-rupee', 'currency-yen',
        'cursor-click', 'database', 'desktop-computer', 'device-mobile',
        'device-tablet', 'document', 'document-add', 'document-download',
        'document-duplicate', 'document-remove', 'document-report', 'document-search',
        'document-text', 'dots-circle-horizontal', 'dots-horizontal', 'dots-vertical',
        'download', 'duplicate', 'emoji-happy', 'emoji-sad', 'exclamation',
        'exclamation-circle', 'external-link', 'eye', 'eye-off', 'fast-forward',
        'film', 'filter', 'finger-print', 'fire', 'flag', 'folder', 'folder-add',
        'folder-download', 'folder-open', 'folder-remove', 'gift', 'globe',
        'globe-alt', 'hand', 'hashtag', 'heart', 'home', 'identification',
        'inbox', 'inbox-in', 'information-circle', 'key', 'library', 'light-bulb',
        'lightning-bolt', 'link', 'location-marker', 'lock-closed', 'lock-open',
        'login', 'logout', 'mail', 'mail-open', 'map', 'menu', 'menu-alt-1',
        'menu-alt-2', 'menu-alt-3', 'menu-alt-4', 'microphone', 'minus',
        'minus-circle', 'minus-sm', 'moon', 'music-note', 'newspaper',
        'office-building', 'paper-airplane', 'paper-clip', 'pause', 'pencil',
        'pencil-alt', 'phone', 'phone-incoming', 'phone-missed-call',
        'phone-outgoing', 'photograph', 'play', 'plus', 'plus-circle', 'plus-sm',
        'presentation-chart-bar', 'presentation-chart-line', 'printer', 'puzzle',
        'qrcode', 'question-mark-circle', 'receipt-refund', 'receipt-tax',
        'refresh', 'reply', 'rewind', 'rss', 'save', 'save-as', 'scale', 'scissors',
        'search', 'search-circle', 'selector', 'server', 'share', 'shield-check',
        'shield-exclamation', 'shopping-bag', 'shopping-cart', 'sort-ascending',
        'sort-descending', 'sparkles', 'speakerphone', 'star', 'status-offline',
        'status-online', 'stop', 'sun', 'support', 'switch-horizontal',
        'switch-vertical', 'table', 'tag', 'template', 'terminal', 'thumb-down',
        'thumb-up', 'ticket', 'translate', 'trash', 'trending-down', 'trending-up',
        'truck', 'upload', 'user', 'user-add', 'user-circle', 'user-group',
        'user-remove', 'users', 'variable', 'video-camera', 'view-boards',
        'view-grid', 'view-grid-add', 'view-list', 'volume-off', 'volume-up',
        'wifi', 'x', 'x-circle', 'zoom-in', 'zoom-out'
    ];
    
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
        $colorPair = Color::randomLightColorWithForeground();
        $style = rand(0, 1) ? 'outline' : 'solid';
        
        return [
            'type' => 'hero_icon',
            'icon' => $this->commonIcons[array_rand($this->commonIcons)],
            'style' => $style,
            'backgroundColor' => $colorPair['background'],
            'iconColor' => $colorPair['foreground'],
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
        return in_array($iconName, $this->commonIcons, true);
    }
}