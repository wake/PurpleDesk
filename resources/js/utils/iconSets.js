// 匯入完整的圖標列表
import { allBootstrapIcons } from './allBootstrapIcons.js'
import { cleanEmojis } from './cleanEmojis.js'

// 匯出完整列表
export const bootstrapIcons = allBootstrapIcons
export const emojis = cleanEmojis

// 保留精選列表供快速訪問
export const featuredBootstrapIcons = [
  { name: '首頁 Home', class: 'bi-house' },
  { name: '使用者 Person', class: 'bi-person' },
  { name: '設定 Gear', class: 'bi-gear' },
  { name: '文件 File', class: 'bi-file-text' },
  { name: '資料夾 Folder', class: 'bi-folder' },
  { name: '愛心 Heart', class: 'bi-heart' },
  { name: '星星 Star', class: 'bi-star' },
  { name: '鈴鐺 Bell', class: 'bi-bell' },
  { name: '聊天 Chat', class: 'bi-chat' },
  { name: '搜尋 Search', class: 'bi-search' },
]

export const featuredEmojis = [
  { name: '微笑', emoji: '😊', category: '表情' },
  { name: '大笑', emoji: '😂', category: '表情' },
  { name: '愛心眼', emoji: '😍', category: '表情' },
  { name: '讚', emoji: '👍', category: '手勢' },
  { name: '愛心', emoji: '❤', category: '符號' },
  { name: '火', emoji: '🔥', category: '符號' },
  { name: '星星', emoji: '⭐', category: '符號' },
  { name: '聚會', emoji: '🎉', category: '物品' },
  { name: '彩虹', emoji: '🌈', category: '符號' },
  { name: '太陽', emoji: '☀', category: '符號' },
]