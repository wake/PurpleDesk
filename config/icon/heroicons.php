<?php

/**
 * Hero Icons 配置
 * 
 * 從前端 allHeroicons.js 完整提取的 Hero Icons v1 清單
 * 包含 230 個 outline 樣式的圖標
 */

return [
    /**
     * Hero Icons v1 完整清單（outline 樣式）
     * 
     * 這些圖標名稱對應到 heroicons/outline 目錄中的 SVG 檔案
     * 在前端使用時會透過 HeroIcon 組件動態載入
     */
    'icons' => [
        'academic-cap', 'adjustments', 'annotation', 'archive', 'arrow-circle-down', 
        'arrow-circle-left', 'arrow-circle-right', 'arrow-circle-up', 'arrow-down', 'arrow-left', 
        'arrow-narrow-down', 'arrow-narrow-left', 'arrow-narrow-right', 'arrow-narrow-up', 'arrow-right', 
        'arrow-sm-down', 'arrow-sm-left', 'arrow-sm-right', 'arrow-sm-up', 'arrow-up', 
        'arrows-expand', 'at-symbol', 'backspace', 'badge-check', 'ban', 
        'beaker', 'bell', 'book-open', 'bookmark', 'bookmark-alt', 
        'briefcase', 'cake', 'calculator', 'calendar', 'camera', 
        'cash', 'chart-bar', 'chart-pie', 'chart-square-bar', 'chat', 
        'chat-alt', 'chat-alt2', 'check', 'check-circle', 'chevron-double-down', 
        'chevron-double-left', 'chevron-double-right', 'chevron-double-up', 'chevron-down', 'chevron-left', 
        'chevron-right', 'chevron-up', 'chip', 'clipboard', 'clipboard-check', 
        'clipboard-copy', 'clipboard-list', 'clock', 'cloud', 'cloud-download', 
        'cloud-upload', 'code', 'cog', 'collection', 'color-swatch', 
        'credit-card', 'cube', 'cube-transparent', 'currency-bangladeshi', 'currency-dollar', 
        'currency-euro', 'currency-pound', 'currency-rupee', 'currency-yen', 'cursor-click', 
        'database', 'desktop-computer', 'device-mobile', 'device-tablet', 'document', 
        'document-add', 'document-download', 'document-duplicate', 'document-remove', 'document-report', 
        'document-search', 'document-text', 'dots-circle-horizontal', 'dots-horizontal', 'dots-vertical', 
        'download', 'duplicate', 'emoji-happy', 'emoji-sad', 'exclamation', 
        'exclamation-circle', 'external-link', 'eye', 'eye-off', 'fast-forward', 
        'film', 'filter', 'finger-print', 'fire', 'flag', 
        'folder', 'folder-add', 'folder-download', 'folder-open', 'folder-remove', 
        'gift', 'globe', 'globe-alt', 'hand', 'hashtag', 
        'heart', 'home', 'identification', 'inbox', 'inbox-in', 
        'information-circle', 'key', 'library', 'light-bulb', 'lightning-bolt', 
        'link', 'location-marker', 'lock-closed', 'lock-open', 'login', 
        'logout', 'mail', 'mail-open', 'map', 'menu', 
        'menu-alt1', 'menu-alt2', 'menu-alt3', 'menu-alt4', 'microphone', 
        'minus', 'minus-circle', 'minus-sm', 'moon', 'music-note', 
        'newspaper', 'office-building', 'paper-airplane', 'paper-clip', 'pause', 
        'pencil', 'pencil-alt', 'phone', 'phone-incoming', 'phone-missed-call', 
        'phone-outgoing', 'photograph', 'play', 'plus', 'plus-circle', 
        'plus-sm', 'presentation-chart-bar', 'presentation-chart-line', 'printer', 'puzzle', 
        'qrcode', 'question-mark-circle', 'receipt-refund', 'receipt-tax', 'refresh', 
        'reply', 'rewind', 'rss', 'save', 'save-as', 
        'scale', 'scissors', 'search', 'search-circle', 'selector', 
        'server', 'share', 'shield-check', 'shield-exclamation', 'shopping-bag', 
        'shopping-cart', 'sort-ascending', 'sort-descending', 'sparkles', 'speakerphone', 
        'star', 'status-offline', 'status-online', 'stop', 'sun', 
        'support', 'switch-horizontal', 'switch-vertical', 'table', 'tag', 
        'template', 'terminal', 'thumb-down', 'thumb-up', 'ticket', 
        'translate', 'trash', 'trending-down', 'trending-up', 'truck', 
        'upload', 'user', 'user-add', 'user-circle', 'user-group', 
        'user-remove', 'users', 'variable', 'video-camera', 'view-boards', 
        'view-grid', 'view-grid-add', 'view-list', 'volume-off', 'volume-up', 
        'wifi', 'x', 'x-circle', 'zoom-in', 'zoom-out', 
    ],
    
    /**
     * 圖標樣式
     * 
     * Hero Icons 支援 outline 和 solid 兩種樣式
     * outline: 線條風格，適合大部分 UI 場景
     * solid: 實心風格，適合需要強調的場景
     */
    'styles' => ['outline', 'solid'],
    
    /**
     * 預設樣式
     */
    'default_style' => 'outline',
];