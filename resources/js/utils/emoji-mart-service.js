/**
 * Emoji Mart Service
 * 使用 @emoji-mart/data 提供 emoji 資料，但保持現有 UI 風格
 */

import data from '@emoji-mart/data'

class EmojiMartService {
  constructor() {
    this.data = null
    this.categories = null
    this.emojis = null
    this.searchIndex = null
    this.initialized = false
  }

  /**
   * 初始化 emoji-mart 資料
   */
  async init() {
    if (this.initialized) return

    try {
      // 載入 emoji-mart 資料
      this.data = data
      this.categories = data.categories || []
      this.emojis = data.emojis || {}
      
      // 建立搜尋索引
      this.buildSearchIndex()
      
      this.initialized = true
      console.log('✅ EmojiMart 資料初始化完成', {
        categories: this.categories.length,
        emojis: Object.keys(this.emojis).length
      })
    } catch (error) {
      console.error('❌ EmojiMart 初始化失敗:', error)
      throw error
    }
  }

  /**
   * 建立搜尋索引
   */
  buildSearchIndex() {
    this.searchIndex = new Map()
    
    Object.entries(this.emojis).forEach(([id, emoji]) => {
      // 索引 emoji 的所有可搜尋內容
      const searchTerms = [
        id,
        emoji.name,
        ...(emoji.keywords || []),
        ...(emoji.emoticons || [])
      ].filter(Boolean).map(term => term.toLowerCase())
      
      this.searchIndex.set(id, {
        ...emoji,
        searchTerms,
        native: emoji.skins?.[0]?.native || emoji.native || ''
      })
    })
  }

  /**
   * 取得所有分類
   */
  getCategories() {
    if (!this.initialized) {
      console.warn('EmojiMart 尚未初始化')
      return []
    }
    
    return this.categories.map(cat => ({
      id: cat.id,
      name: this.getCategoryName(cat.id),
      emojis: cat.emojis || []
    }))
  }

  /**
   * 取得分類的本地化名稱
   */
  getCategoryName(categoryId) {
    const names = {
      'people': '人物',
      'nature': '自然',
      'foods': '食物',
      'activity': '活動',
      'places': '地點',
      'objects': '物品',
      'symbols': '符號',
      'flags': '旗幟',
      'frequent': '常用',
      'search': '搜尋結果'
    }
    return names[categoryId] || categoryId
  }

  /**
   * 取得特定分類的 emoji
   */
  getCategoryEmojis(categoryId) {
    if (!this.initialized) return []
    
    const category = this.categories.find(cat => cat.id === categoryId)
    if (!category) return []
    
    return category.emojis.map(emojiId => {
      const emoji = this.emojis[emojiId]
      if (!emoji) return null
      
      return {
        id: emojiId,
        name: emoji.name,
        native: emoji.skins?.[0]?.native || emoji.native || '',
        keywords: emoji.keywords || []
      }
    }).filter(Boolean)
  }

  /**
   * 取得所有 emoji（平面化）
   */
  getAllEmojis() {
    if (!this.initialized) return []
    
    const allEmojis = []
    
    this.categories.forEach(category => {
      if (category.id === 'frequent' || category.id === 'search') return
      
      category.emojis.forEach(emojiId => {
        const emoji = this.emojis[emojiId]
        if (!emoji) return
        
        allEmojis.push({
          id: emojiId,
          name: emoji.name,
          native: emoji.skins?.[0]?.native || emoji.native || '',
          category: category.id,
          categoryName: this.getCategoryName(category.id),
          keywords: emoji.keywords || []
        })
      })
    })
    
    return allEmojis
  }

  /**
   * 搜尋 emoji
   */
  searchEmojis(query) {
    if (!this.initialized || !query) return []
    
    const searchTerm = query.toLowerCase().trim()
    const results = []
    
    for (const [id, emoji] of this.searchIndex.entries()) {
      // 檢查是否符合搜尋條件
      const matches = emoji.searchTerms.some(term => 
        term.includes(searchTerm)
      )
      
      if (matches) {
        results.push({
          id,
          name: emoji.name,
          native: emoji.native,
          score: this.calculateSearchScore(searchTerm, emoji.searchTerms)
        })
      }
    }
    
    // 根據相關性排序
    results.sort((a, b) => b.score - a.score)
    
    // 限制回傳數量
    return results.slice(0, 50)
  }

  /**
   * 計算搜尋分數
   */
  calculateSearchScore(query, searchTerms) {
    let maxScore = 0
    
    searchTerms.forEach(term => {
      let score = 0
      
      // 完全匹配
      if (term === query) {
        score = 100
      }
      // 開頭匹配
      else if (term.startsWith(query)) {
        score = 50
      }
      // 包含匹配
      else if (term.includes(query)) {
        score = 20
      }
      
      maxScore = Math.max(maxScore, score)
    })
    
    return maxScore
  }

  /**
   * 取得 emoji 的所有膚色變化
   */
  getEmojiVariations(emojiId) {
    if (!this.initialized) return []
    
    const emoji = this.emojis[emojiId]
    if (!emoji || !emoji.skins) return []
    
    return emoji.skins.map((skin, index) => ({
      native: skin.native,
      tone: index === 0 ? 'default' : `tone-${index}`
    }))
  }

  /**
   * 處理 emoji 以確保正確顯示
   * 移除可能造成顯示問題的字符
   */
  sanitizeEmoji(emoji) {
    if (!emoji) return ''
    
    // 移除變化選擇器（VS15、VS16）
    // 但保留 ZWJ 序列的完整性
    return emoji
      .replace(/\uFE0F(?!\u200D)/g, '') // 移除非 ZWJ 後的 VS16
      .replace(/\uFE0E(?!\u200D)/g, '') // 移除非 ZWJ 後的 VS15
  }

  /**
   * 檢查是否支援原生 emoji
   */
  supportsNativeEmoji() {
    // 簡單的檢測方法
    const canvas = document.createElement('canvas')
    const ctx = canvas.getContext('2d')
    
    if (!ctx) return false
    
    ctx.font = '1px serif'
    // 測試一個複雜的 emoji
    const testEmoji = '👨‍👩‍👧‍👦'
    const width = ctx.measureText(testEmoji).width
    
    // 如果寬度異常大，表示不支援
    return width < 10
  }
}

// 建立單例
const emojiMartService = new EmojiMartService()

export default emojiMartService