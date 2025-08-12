// 整合的圖標管理系統 - 向下相容層
import iconManager from './iconManager.js'
import { allBootstrapIcons } from './allBootstrapIcons.js'
import allEmojis, { ensureEmojisLoaded, getLoadedEmojis as getAllEmojis } from './allEmojis.js'

// 向下相容的同步匯出
export const bootstrapIcons = allBootstrapIcons
export const emojis = allEmojis  // 使用新的完整 emoji 系統

// 新的非同步 API
export async function getBootstrapIcons() {
  await iconManager.loadAllIcons()
  return iconManager.getAllLoadedIcons().icons
}

export async function getEmojis() {
  // 使用新的 allEmojis 系統
  return await ensureEmojisLoaded()
}

// 同步版本（使用已載入的圖標）
export function getLoadedBootstrapIcons() {
  return iconManager.getAllLoadedIcons().icons
}

export function getLoadedEmojis() {
  // 使用新的 allEmojis 系統
  return getAllEmojis()
}

// 匯出新的管理系統
export { iconManager }

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