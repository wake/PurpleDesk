// Emoji 漸進式載入管理系統
// 提供分類載入、搜尋索引、快取機制

// 載入優先級定義
export const emojiLoadingPriority = {
  immediate: ['smileys-emotion'],     // 立即載入
  high: ['people-body', 'animals-nature'], // 高優先級
  medium: ['food-drink', 'travel-places', 'activities', 'objects'], // 中優先級  
  low: ['symbols', 'flags'] // 低優先級
}

// 分類資訊映射
export const emojiCategoryMap = {
  'smileys-emotion': { name: '表情與情緒', description: '各種臉部表情、情緒狀態、愛心等', priority: 'immediate' },
  'people-body': { name: '人物與身體', description: '人物、身體部位、手勢、職業、服裝等', priority: 'high' },
  'animals-nature': { name: '動物與自然', description: '動物、植物、天氣、自然現象等', priority: 'high' },
  'food-drink': { name: '食物與飲料', description: '水果、蔬菜、主食、甜點、飲料等', priority: 'medium' },
  'travel-places': { name: '旅遊與地點', description: '建築物、交通工具、地標、自然地點等', priority: 'medium' },
  'activities': { name: '活動', description: '運動、音樂、遊戲、慶祝活動等', priority: 'medium' },
  'objects': { name: '物品', description: '電子產品、文具、工具、家具等', priority: 'medium' },
  'symbols': { name: '符號', description: '數學、音樂、警告、宗教、星座等符號', priority: 'low' },
  'flags': { name: '國旗', description: '世界各國國旗與特殊旗幟', priority: 'low' }
}

// Emoji 快取
const emojiCache = new Map()
const emojiLoadingPromises = new Map()
const emojiSearchIndex = new Map()

// 動態載入分類
async function loadEmojiCategory(categoryId) {
  if (emojiCache.has(categoryId)) {
    return emojiCache.get(categoryId)
  }
  
  if (emojiLoadingPromises.has(categoryId)) {
    return emojiLoadingPromises.get(categoryId)
  }
  
  const loadPromise = (async () => {
    try {
      let categoryModule
      
      switch (categoryId) {
        case 'smileys-emotion':
          categoryModule = await import('./smileys-emotion.js')
          break
        case 'people-body':
          categoryModule = await import('./people-body.js')
          break
        case 'animals-nature':
          categoryModule = await import('./animals-nature.js')
          break
        case 'food-drink':
          categoryModule = await import('./other-categories.js')
          return categoryModule.foodDrinkEmojis
        case 'travel-places':
          categoryModule = await import('./other-categories.js')
          return categoryModule.travelPlacesEmojis
        case 'activities':
          categoryModule = await import('./other-categories.js')
          return categoryModule.activitiesEmojis
        case 'objects':
          categoryModule = await import('./other-categories.js')
          return categoryModule.objectsEmojis
        case 'symbols':
          categoryModule = await import('./other-categories.js')
          return categoryModule.symbolsEmojis
        case 'flags':
          categoryModule = await import('./other-categories.js')
          return categoryModule.flagsEmojis
        default:
          throw new Error(`Unknown emoji category: ${categoryId}`)
      }
      
      const emojis = categoryModule[Object.keys(categoryModule)[0]] // 取得第一個匯出的陣列
      emojiCache.set(categoryId, emojis)
      
      // 建立搜尋索引
      emojis.forEach(emoji => {
        const searchKey = emoji.name.toLowerCase()
        if (!emojiSearchIndex.has(searchKey)) {
          emojiSearchIndex.set(searchKey, [])
        }
        emojiSearchIndex.get(searchKey).push({ ...emoji, category: categoryId })
        
        // 建立關鍵字索引
        const keywords = emoji.name.toLowerCase().split(/\s+/)
        keywords.forEach(keyword => {
          if (!emojiSearchIndex.has(keyword)) {
            emojiSearchIndex.set(keyword, [])
          }
          emojiSearchIndex.get(keyword).push({ ...emoji, category: categoryId })
        })
      })
      
      emojiLoadingPromises.delete(categoryId)
      return emojis
    } catch (error) {
      emojiLoadingPromises.delete(categoryId)
      console.error(`Failed to load emoji category ${categoryId}:`, error)
      throw error
    }
  })()
  
  emojiLoadingPromises.set(categoryId, loadPromise)
  return loadPromise
}

// 載入所有 emoji（按優先級順序）
export async function loadAllEmojis() {
  const allCategories = Object.keys(emojiCategoryMap)
  
  // 按優先級順序載入
  for (const priority of ['immediate', 'high', 'medium', 'low']) {
    const categories = allCategories.filter(cat => emojiCategoryMap[cat].priority === priority)
    
    // 並行載入同優先級的分類
    const loadPromises = categories.map(cat => loadEmojiCategory(cat))
    await Promise.all(loadPromises)
    
    // 在載入高優先級後給瀏覽器一些時間處理
    if (priority === 'immediate' || priority === 'high') {
      await new Promise(resolve => setTimeout(resolve, 50))
    }
  }
}

// 漸進式載入（按需載入）
export async function loadEmojisByPriority() {
  // 立即載入最重要的分類
  await Promise.all(emojiLoadingPriority.immediate.map(cat => loadEmojiCategory(cat)))
  
  // 延遲載入其他分類
  requestIdleCallback(async () => {
    await Promise.all(emojiLoadingPriority.high.map(cat => loadEmojiCategory(cat)))
    
    requestIdleCallback(async () => {
      await Promise.all(emojiLoadingPriority.medium.map(cat => loadEmojiCategory(cat)))
      
      requestIdleCallback(async () => {
        await Promise.all(emojiLoadingPriority.low.map(cat => loadEmojiCategory(cat)))
      })
    })
  })
}

// 取得分類 emoji
export async function getEmojisByCategory(categoryId) {
  return await loadEmojiCategory(categoryId)
}

// 取得所有已載入的 emoji
export function getAllLoadedEmojis() {
  const allEmojis = []
  for (const [categoryId, emojis] of emojiCache) {
    allEmojis.push(...emojis.map(emoji => ({ ...emoji, category: categoryId })))
  }
  return allEmojis
}

// 搜尋 emoji
export async function searchEmojis(query) {
  if (!query || query.trim().length === 0) {
    return []
  }
  
  // 確保所有分類都已載入以進行搜尋
  if (emojiCache.size < Object.keys(emojiCategoryMap).length) {
    await loadAllEmojis()
  }
  
  const searchTerm = query.toLowerCase().trim()
  const results = new Set()
  
  // 精確匹配
  if (emojiSearchIndex.has(searchTerm)) {
    emojiSearchIndex.get(searchTerm).forEach(emoji => results.add(JSON.stringify(emoji)))
  }
  
  // 部分匹配
  for (const [key, emojis] of emojiSearchIndex) {
    if (key.includes(searchTerm)) {
      emojis.forEach(emoji => results.add(JSON.stringify(emoji)))
    }
  }
  
  return Array.from(results).map(json => JSON.parse(json))
}

// 檢查分類是否已載入
export function isEmojiCategoryLoaded(categoryId) {
  return emojiCache.has(categoryId)
}

// 取得載入狀態
export function getEmojiLoadingStatus() {
  const totalCategories = Object.keys(emojiCategoryMap).length
  const loadedCategories = emojiCache.size
  const loadingCategories = emojiLoadingPromises.size
  
  return {
    total: totalCategories,
    loaded: loadedCategories,
    loading: loadingCategories,
    progress: Math.round((loadedCategories / totalCategories) * 100)
  }
}

// 預載入熱門分類
export function preloadPopularEmojiCategories() {
  const popular = ['smileys-emotion', 'people-body', 'animals-nature']
  return Promise.all(popular.map(cat => loadEmojiCategory(cat)))
}

// 清除快取
export function clearEmojiCache() {
  emojiCache.clear()
  emojiLoadingPromises.clear()
  emojiSearchIndex.clear()
}

// 取得記憶體使用統計
export function getEmojiMemoryStats() {
  let totalEmojis = 0
  for (const emojis of emojiCache.values()) {
    totalEmojis += emojis.length
  }
  
  return {
    loadedCategories: emojiCache.size,
    totalEmojis,
    searchIndexSize: emojiSearchIndex.size,
    estimatedMemoryKB: Math.round((totalEmojis * 82 + emojiSearchIndex.size * 50) / 1024)
  }
}

// 統計不同分類的 emoji 數量
export function getEmojiCategoryStats() {
  const stats = {}
  for (const [categoryId, emojis] of emojiCache) {
    stats[categoryId] = {
      name: emojiCategoryMap[categoryId]?.name || categoryId,
      count: emojis.length,
      priority: emojiCategoryMap[categoryId]?.priority || 'unknown'
    }
  }
  return stats
}

// 取得熱門 emoji (基於頻率或使用統計)
export function getPopularEmojis(limit = 50) {
  // 這裡可以實作基於使用頻率的邏輯，目前返回最常用的 emoji
  const popularList = [
    '😀', '😂', '❤️', '😍', '😊', '👍', '🔥', '✨', '🎉', '💕',
    '😭', '🙏', '😘', '🥰', '😎', '🤔', '👏', '🙄', '😴', '🤗',
    '🎂', '🎈', '🎁', '🌟', '⭐', '💫', '💖', '💗', '💝', '💯',
    '🚀', '🎯', '🏆', '💪', '👑', '🔔', '📢', '⚡', '🌈', '🌸',
    '🎵', '🎶', '🍕', '🍔', '🍰', '☕', '🍺', '🎮', '📱', '💻'
  ]
  
  const allEmojis = getAllLoadedEmojis()
  const popular = allEmojis.filter(emoji => popularList.includes(emoji.emoji))
  
  return popular.slice(0, limit)
}

// 自動初始化
let emojiAutoInitialized = false

export function autoInitializeEmojis() {
  if (emojiAutoInitialized) return
  emojiAutoInitialized = true
  
  // 當 DOM 準備好時開始漸進式載入
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadEmojisByPriority)
  } else {
    loadEmojisByPriority()
  }
}

// 如果在瀏覽器環境中，自動初始化
if (typeof window !== 'undefined') {
  autoInitializeEmojis()
}

// 預設匯出主要功能
export default {
  loadAllEmojis,
  loadEmojisByPriority,
  getEmojisByCategory,
  getAllLoadedEmojis,
  searchEmojis,
  isEmojiCategoryLoaded,
  getEmojiLoadingStatus,
  preloadPopularEmojiCategories,
  clearEmojiCache,
  getEmojiMemoryStats,
  getEmojiCategoryStats,
  getPopularEmojis,
  emojiCategoryMap,
  emojiLoadingPriority
}