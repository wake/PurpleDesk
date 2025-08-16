<?php

/**
 * 圖標系統顏色配置
 * 
 * 定義圖標系統使用的所有顏色設定，包含標準色、淡色系、灰階色
 * 以及前景色配對規則
 */

return [
    /**
     * 亮度閾值（用於判斷使用深色或淺色前景）
     * 使用 W3C 公式計算相對亮度，閾值 0.6 可確保良好對比度
     */
    'luminance_threshold' => 0.6,
    
    /**
     * 深色前景色（用於亮背景）
     */
    'dark_foreground_color' => '#1f2937', // gray-800
    
    /**
     * 淺色前景色（用於暗背景）
     */
    'light_foreground_color' => '#ffffff', // white
    
    /**
     * 標準深色調色盤
     * 
     * 這些顏色通常與白色前景搭配，提供良好的對比度
     * 基於 Tailwind CSS 的 500-600 色階
     */
    'standard_colors' => [
        '#6366f1', // indigo-500
        '#8b5cf6', // violet-500
        '#a855f7', // purple-500
        '#ec4899', // pink-500
        '#ef4444', // red-500
        '#f97316', // orange-500
        '#f59e0b', // amber-500
        '#eab308', // yellow-500
        '#84cc16', // lime-500
        '#22c55e', // green-500
        '#10b981', // emerald-500
        '#14b8a6', // teal-500
        '#06b6d4', // cyan-500
        '#0ea5e9', // sky-500
        '#3b82f6', // blue-500
    ],
    
    /**
     * 淡色系調色盤
     * 
     * 較淺的背景色，通常需要深色前景以確保可讀性
     * 基於 Tailwind CSS 的 50-100 色階
     */
    'light_colors' => [
        '#eef2ff', // indigo-50
        '#f3e8ff', // violet-50
        '#faf5ff', // purple-50
        '#fdf2f8', // pink-50
        '#fef2f2', // red-50
        '#fff7ed', // orange-50
        '#fffbeb', // amber-50
        '#fefce8', // yellow-50
        '#f7fee7', // lime-50
        '#f0fdf4', // green-50
        '#ecfdf5', // emerald-50
        '#f0fdfa', // teal-50
        '#ecfeff', // cyan-50
        '#f0f9ff', // sky-50
        '#eff6ff', // blue-50
    ],
    
    /**
     * 淡色背景與對應深色前景的配對
     * 
     * 定義每個淡色背景應該使用的特定深色前景，
     * 確保最佳的視覺對比和品牌一致性
     */
    'light_colors_with_foreground' => [
        '#eef2ff' => '#4338ca', // indigo-50 -> indigo-700
        '#f3e8ff' => '#6d28d9', // violet-50 -> violet-700
        '#faf5ff' => '#7c3aed', // purple-50 -> purple-600
        '#fdf2f8' => '#be185d', // pink-50 -> pink-700
        '#fef2f2' => '#b91c1c', // red-50 -> red-700
        '#fff7ed' => '#c2410c', // orange-50 -> orange-700
        '#fffbeb' => '#b45309', // amber-50 -> amber-700
        '#fefce8' => '#a16207', // yellow-50 -> yellow-700
        '#f7fee7' => '#4d7c0f', // lime-50 -> lime-700
        '#f0fdf4' => '#15803d', // green-50 -> green-700
        '#ecfdf5' => '#047857', // emerald-50 -> emerald-700
        '#f0fdfa' => '#0f766e', // teal-50 -> teal-700
        '#ecfeff' => '#0e7490', // cyan-50 -> cyan-700
        '#f0f9ff' => '#0369a1', // sky-50 -> sky-700
        '#eff6ff' => '#1d4ed8', // blue-50 -> blue-700
    ],
    
    /**
     * 灰階色彩
     * 
     * 中性色彩，適用於各種場景
     * 基於 Tailwind CSS 的 gray 色階
     */
    'gray_colors' => [
        '#374151', // gray-700
        '#4b5563', // gray-600
        '#6b7280', // gray-500
        '#9ca3af', // gray-400
        '#d1d5db', // gray-300
        '#e5e7eb', // gray-200
        '#f3f4f6', // gray-100
        '#f9fafb', // gray-50
    ],
];