<?php

/**
 * Bootstrap Icons 配置
 * 
 * 按分類整理的 Bootstrap Icons 清單
 * 包含最常用的圖標，覆蓋多種使用場景
 */

return [
    /**
     * Bootstrap Icons 分類清單
     * 
     * 總計 11 個分類，包含商業、社交、技術、UI 等各種場景的圖標
     * 圖標名稱包含 'bi-' 前綴，對應 Bootstrap Icons 的 CSS 類別名稱
     */
    'categories' => [
        // 通用圖標
        'general' => [
            'bi-house', 'bi-house-fill', 'bi-house-door', 'bi-house-door-fill',
            'bi-building', 'bi-shop', 'bi-shop-window', 
            'bi-hospital', 'bi-hospital-fill', 'bi-bank', 'bi-bank2',
            'bi-bell', 'bi-bell-fill', 'bi-bell-slash', 'bi-bell-slash-fill',
            'bi-bookmark', 'bi-bookmark-fill', 'bi-bookmark-star', 'bi-bookmark-star-fill',
            'bi-calendar', 'bi-calendar-fill', 'bi-calendar-check', 'bi-calendar-check-fill',
            'bi-clock', 'bi-clock-fill', 'bi-clock-history', 'bi-alarm', 'bi-alarm-fill',
            'bi-stopwatch', 'bi-stopwatch-fill',
        ],
        
        // UI 元素
        'ui' => [
            'bi-arrow-up', 'bi-arrow-down', 'bi-arrow-left', 'bi-arrow-right',
            'bi-arrow-up-circle', 'bi-arrow-down-circle', 'bi-arrow-left-circle', 'bi-arrow-right-circle',
            'bi-arrow-up-circle-fill', 'bi-arrow-down-circle-fill', 'bi-arrow-left-circle-fill', 'bi-arrow-right-circle-fill',
            'bi-chevron-up', 'bi-chevron-down', 'bi-chevron-left', 'bi-chevron-right',
            'bi-chevron-double-up', 'bi-chevron-double-down', 'bi-chevron-double-left', 'bi-chevron-double-right',
            'bi-caret-up', 'bi-caret-down', 'bi-caret-left', 'bi-caret-right',
            'bi-caret-up-fill', 'bi-caret-down-fill', 'bi-caret-left-fill', 'bi-caret-right-fill',
            'bi-plus', 'bi-plus-circle', 'bi-plus-circle-fill', 'bi-plus-square', 'bi-plus-square-fill',
            'bi-dash', 'bi-dash-circle', 'bi-dash-circle-fill', 'bi-dash-square', 'bi-dash-square-fill',
            'bi-x', 'bi-x-circle', 'bi-x-circle-fill', 'bi-x-square', 'bi-x-square-fill',
            'bi-check', 'bi-check-circle', 'bi-check-circle-fill', 'bi-check-square', 'bi-check-square-fill',
            'bi-three-dots', 'bi-three-dots-vertical', 'bi-list', 'bi-list-ul', 'bi-list-ol',
            'bi-grid', 'bi-grid-fill', 'bi-grid-3x3', 'bi-grid-3x3-gap', 'bi-grid-3x3-gap-fill',
        ],
        
        // 人物與社交
        'people' => [
            'bi-person', 'bi-person-fill', 'bi-person-circle', 'bi-person-square',
            'bi-person-plus', 'bi-person-plus-fill', 'bi-person-dash', 'bi-person-dash-fill',
            'bi-person-check', 'bi-person-check-fill', 'bi-person-x', 'bi-person-x-fill',
            'bi-people', 'bi-people-fill',
            'bi-person-badge', 'bi-person-badge-fill', 'bi-person-workspace',
            'bi-gender-male', 'bi-gender-female', 'bi-gender-trans', 'bi-gender-ambiguous',
        ],
        
        // 檔案與文件
        'files' => [
            'bi-file', 'bi-file-fill', 'bi-file-text', 'bi-file-text-fill',
            'bi-file-earmark', 'bi-file-earmark-fill', 'bi-file-earmark-text', 'bi-file-earmark-text-fill',
            'bi-file-code', 'bi-file-code-fill', 'bi-file-earmark-code', 'bi-file-earmark-code-fill',
            'bi-file-pdf', 'bi-file-pdf-fill', 'bi-file-earmark-pdf', 'bi-file-earmark-pdf-fill',
            'bi-file-word', 'bi-file-word-fill', 'bi-file-earmark-word', 'bi-file-earmark-word-fill',
            'bi-file-excel', 'bi-file-excel-fill', 'bi-file-earmark-excel', 'bi-file-earmark-excel-fill',
            'bi-file-ppt', 'bi-file-ppt-fill', 'bi-file-earmark-ppt', 'bi-file-earmark-ppt-fill',
            'bi-file-zip', 'bi-file-zip-fill', 'bi-file-earmark-zip', 'bi-file-earmark-zip-fill',
            'bi-folder', 'bi-folder-fill', 'bi-folder-open', 'bi-folder2', 'bi-folder2-open',
            'bi-folder-plus', 'bi-folder-minus', 'bi-folder-check', 'bi-folder-x',
        ],
        
        // 通訊與訊息
        'communication' => [
            'bi-envelope', 'bi-envelope-fill', 'bi-envelope-open', 'bi-envelope-open-fill',
            'bi-chat', 'bi-chat-fill', 'bi-chat-dots', 'bi-chat-dots-fill',
            'bi-chat-left', 'bi-chat-left-fill', 'bi-chat-right', 'bi-chat-right-fill',
            'bi-chat-square', 'bi-chat-square-fill', 'bi-chat-text', 'bi-chat-text-fill',
            'bi-messenger', 'bi-telegram', 'bi-whatsapp', 'bi-wechat',
            'bi-telephone', 'bi-telephone-fill', 'bi-telephone-inbound', 'bi-telephone-inbound-fill',
            'bi-telephone-outbound', 'bi-telephone-outbound-fill',
            'bi-mic', 'bi-mic-fill', 'bi-mic-mute', 'bi-mic-mute-fill',
            'bi-camera-video', 'bi-camera-video-fill', 'bi-camera-video-off', 'bi-camera-video-off-fill',
        ],
        
        // 媒體與裝置
        'media' => [
            'bi-image', 'bi-image-fill', 'bi-image-alt', 'bi-images',
            'bi-camera', 'bi-camera-fill', 'bi-camera2', 'bi-camera-reels', 'bi-camera-reels-fill',
            'bi-film', 'bi-music-note', 'bi-music-note-beamed', 'bi-music-note-list',
            'bi-play', 'bi-play-fill', 'bi-play-circle', 'bi-play-circle-fill',
            'bi-pause', 'bi-pause-fill', 'bi-pause-circle', 'bi-pause-circle-fill',
            'bi-stop', 'bi-stop-fill', 'bi-stop-circle', 'bi-stop-circle-fill',
            'bi-skip-forward', 'bi-skip-forward-fill', 'bi-skip-backward', 'bi-skip-backward-fill',
            'bi-volume-up', 'bi-volume-up-fill', 'bi-volume-down', 'bi-volume-down-fill',
            'bi-volume-mute', 'bi-volume-mute-fill', 'bi-volume-off', 'bi-volume-off-fill',
            'bi-headphones', 'bi-speaker', 'bi-speaker-fill',
        ],
        
        // 商業與金融
        'business' => [
            'bi-briefcase', 'bi-briefcase-fill', 'bi-bag', 'bi-bag-fill',
            'bi-bag-check', 'bi-bag-check-fill', 'bi-bag-plus', 'bi-bag-plus-fill',
            'bi-cart', 'bi-cart-fill', 'bi-cart-check', 'bi-cart-check-fill',
            'bi-cart-plus', 'bi-cart-plus-fill', 'bi-cart-dash', 'bi-cart-dash-fill',
            'bi-cash', 'bi-cash-stack', 'bi-cash-coin', 'bi-currency-dollar',
            'bi-currency-euro', 'bi-currency-pound', 'bi-currency-yen', 'bi-currency-bitcoin',
            'bi-credit-card', 'bi-credit-card-fill', 'bi-credit-card-2-back', 'bi-credit-card-2-back-fill',
            'bi-wallet', 'bi-wallet-fill', 'bi-wallet2', 'bi-piggy-bank', 'bi-piggy-bank-fill',
            'bi-graph-up', 'bi-graph-up-arrow', 'bi-graph-down', 'bi-graph-down-arrow',
            'bi-bar-chart', 'bi-bar-chart-fill', 'bi-bar-chart-line', 'bi-bar-chart-line-fill',
            'bi-pie-chart', 'bi-pie-chart-fill',
        ],
        
        // 技術與編程
        'technology' => [
            'bi-laptop', 'bi-laptop-fill', 'bi-pc', 'bi-pc-display', 'bi-pc-display-horizontal',
            'bi-phone', 'bi-phone-fill', 'bi-phone-landscape', 'bi-phone-landscape-fill',
            'bi-tablet', 'bi-tablet-fill', 'bi-tablet-landscape', 'bi-tablet-landscape-fill',
            'bi-smartwatch', 'bi-display', 'bi-display-fill',
            'bi-cpu', 'bi-cpu-fill', 'bi-gpu-card', 'bi-memory', 'bi-motherboard', 'bi-motherboard-fill',
            'bi-hdd', 'bi-hdd-fill', 'bi-hdd-rack', 'bi-hdd-rack-fill',
            'bi-usb', 'bi-usb-fill', 'bi-usb-drive', 'bi-usb-drive-fill',
            'bi-ethernet', 'bi-wifi', 'bi-wifi-off', 'bi-wifi-1', 'bi-wifi-2',
            'bi-bluetooth', 'bi-router', 'bi-router-fill',
            'bi-code', 'bi-code-slash', 'bi-code-square', 'bi-terminal', 'bi-terminal-fill',
            'bi-bug', 'bi-bug-fill', 'bi-github', 'bi-git',
        ],
        
        // 編輯與工具
        'editing' => [
            'bi-pencil', 'bi-pencil-fill', 'bi-pencil-square', 'bi-pen', 'bi-pen-fill',
            'bi-brush', 'bi-brush-fill', 'bi-paint-bucket', 'bi-palette', 'bi-palette-fill',
            'bi-scissors', 'bi-eraser', 'bi-eraser-fill',
            'bi-type', 'bi-type-bold', 'bi-type-italic', 'bi-type-underline', 'bi-type-strikethrough',
            'bi-fonts', 'bi-text-left', 'bi-text-center', 'bi-text-right', 'bi-text-paragraph',
            'bi-link', 'bi-link-45deg', 'bi-paperclip', 'bi-pin', 'bi-pin-fill',
            'bi-gear', 'bi-gear-fill', 'bi-gear-wide', 'bi-gear-wide-connected',
            'bi-tools', 'bi-wrench', 'bi-wrench-adjustable', 'bi-hammer', 'bi-screwdriver',
        ],
        
        // 其他常用
        'misc' => [
            'bi-heart', 'bi-heart-fill', 'bi-heart-half', 'bi-heartbreak', 'bi-heartbreak-fill',
            'bi-star', 'bi-star-fill', 'bi-star-half', 'bi-stars',
            'bi-sun', 'bi-sun-fill', 'bi-moon', 'bi-moon-fill', 'bi-moon-stars', 'bi-moon-stars-fill',
            'bi-cloud', 'bi-cloud-fill', 'bi-cloud-sun', 'bi-cloud-sun-fill',
            'bi-cloud-rain', 'bi-cloud-rain-fill', 'bi-cloud-snow', 'bi-cloud-snow-fill',
            'bi-lightning', 'bi-lightning-fill', 'bi-lightning-charge', 'bi-lightning-charge-fill',
            'bi-droplet', 'bi-droplet-fill', 'bi-droplet-half', 'bi-umbrella', 'bi-umbrella-fill',
            'bi-flag', 'bi-flag-fill', 'bi-geo', 'bi-geo-fill', 'bi-geo-alt', 'bi-geo-alt-fill',
            'bi-globe', 'bi-globe2', 'bi-map', 'bi-map-fill', 'bi-compass', 'bi-compass-fill',
            'bi-search', 'bi-zoom-in', 'bi-zoom-out', 'bi-funnel', 'bi-funnel-fill',
            'bi-filter', 'bi-filter-circle', 'bi-filter-circle-fill', 'bi-filter-square', 'bi-filter-square-fill',
            'bi-key', 'bi-key-fill', 'bi-lock', 'bi-lock-fill', 'bi-unlock', 'bi-unlock-fill',
            'bi-shield', 'bi-shield-fill', 'bi-shield-check', 'bi-shield-lock', 'bi-shield-lock-fill',
            'bi-award', 'bi-award-fill', 'bi-trophy', 'bi-trophy-fill', 'bi-gift', 'bi-gift-fill',
            'bi-box', 'bi-box2', 'bi-box2-fill', 'bi-box-seam', 'bi-boxes',
            'bi-trash', 'bi-trash-fill', 'bi-trash2', 'bi-trash2-fill', 'bi-trash3', 'bi-trash3-fill',
            'bi-recycle', 'bi-arrow-repeat', 'bi-arrow-clockwise', 'bi-arrow-counterclockwise',
            'bi-question', 'bi-question-circle', 'bi-question-circle-fill', 'bi-question-square', 'bi-question-square-fill',
            'bi-exclamation', 'bi-exclamation-circle', 'bi-exclamation-circle-fill', 
            'bi-exclamation-triangle', 'bi-exclamation-triangle-fill',
            'bi-info', 'bi-info-circle', 'bi-info-circle-fill', 'bi-info-square', 'bi-info-square-fill',
        ],
    ],
    
    /**
     * 取得所有圖標的扁平化陣列
     * 
     * 這個設定會在執行時動態計算
     */
    'all' => null, // 將在使用時動態計算
];