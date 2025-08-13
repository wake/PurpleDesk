// 整合的圖標管理系統
// 統一管理 Bootstrap Icons 和 Emoji 的載入、搜尋、快取

import bsIconsManager from './icons/index.js'
import emojiManager from './emojis/index.js'

// 整合的管理器類
class IconManager {
  constructor() {
    this.initialized = false
    this.loadingStats = {
      icons: { total: 0, loaded: 0, loading: 0 },
      emojis: { total: 0, loaded: 0, loading: 0 }
    }
  }

  // 初始化系統
  async initialize() {
    if (this.initialized) return
    
    // if (process.env.NODE_ENV === 'development') {
    //   console.log('🚀 IconManager: 開始初始化圖標管理系統')
    // }
    
    try {
      // 使用 allSettled 來避免單一失敗導致全部失敗
      const results = await Promise.allSettled([
        bsIconsManager.preloadPopularCategories(),
        emojiManager.preloadPopularEmojiCategories()
      ])
      
      // 檢查結果
      results.forEach((result, index) => {
        if (result.status === 'rejected') {
          const systemName = index === 0 ? 'Bootstrap Icons' : 'Emojis'
          console.error(`⚠️ IconManager: ${systemName} 初始化失敗:`, result.reason)
        }
      })
      
      // 開始漸進式載入（即使部分初始化失敗）
      if (typeof bsIconsManager.loadIconsByPriority === 'function') {
        bsIconsManager.loadIconsByPriority()
      }
      if (typeof emojiManager.loadEmojisByPriority === 'function') {
        emojiManager.loadEmojisByPriority()
      }
      
      this.initialized = true
      // if (process.env.NODE_ENV === 'development') {
      //   console.log('✅ IconManager: 初始化完成（可能有部分錯誤）')
      // }
      
      // 定期更新載入統計
      this.startStatsTracking()
      
    } catch (error) {
      console.error('❌ IconManager: 初始化失敗', error)
      // 不再抛出錯誤，讓系統繼續運作
      this.initialized = true // 標記為已初始化，避免重複嘗試
    }
  }

  // 開始統計追蹤
  startStatsTracking() {
    const updateStats = () => {
      try {
        if (typeof bsIconsManager.getLoadingStatus === 'function') {
          this.loadingStats.icons = bsIconsManager.getLoadingStatus()
        }
        if (typeof emojiManager.getEmojiLoadingStatus === 'function') {
          this.loadingStats.emojis = emojiManager.getEmojiLoadingStatus()
        }
        
        const iconProgress = this.loadingStats.icons.progress || 0
        const emojiProgress = this.loadingStats.emojis.progress || 0
        const totalProgress = Math.round((iconProgress + emojiProgress) / 2)
        
        if (totalProgress < 100) {
          setTimeout(updateStats, 1000) // 每秒更新一次
        } else {
          // if (process.env.NODE_ENV === 'development') {
          //   console.log('🎯 IconManager: 所有圖標載入完成')
          // }
          this.logFinalStats()
        }
      } catch (error) {
        console.error('Stats tracking error:', error)
      }
    }
    
    updateStats()
  }

  // 搜尋圖標（同時搜尋 BS Icons 和 Emoji）
  async searchIcons(query) {
    if (!query || query.trim().length === 0) {
      return { icons: [], emojis: [], total: 0 }
    }

    try {
      const [icons, emojis] = await Promise.all([
        bsIconsManager.searchIcons(query),
        emojiManager.searchEmojis(query)
      ])

      return {
        icons,
        emojis,
        total: icons.length + emojis.length,
        query: query.trim()
      }
    } catch (error) {
      console.error('搜尋圖標失敗:', error)
      return { icons: [], emojis: [], total: 0, error: error.message }
    }
  }

  // 取得分類圖標
  async getIconsByCategory(type, categoryId) {
    try {
      if (type === 'icons') {
        return await bsIconsManager.getIconsByCategory(categoryId)
      } else if (type === 'emojis') {
        return await emojiManager.getEmojisByCategory(categoryId)
      } else {
        throw new Error(`Unknown icon type: ${type}`)
      }
    } catch (error) {
      console.error(`取得 ${type} 分類 ${categoryId} 失敗:`, error)
      return []
    }
  }

  // 取得所有已載入的圖標
  getAllLoadedIcons() {
    return {
      icons: bsIconsManager.getAllLoadedIcons(),
      emojis: emojiManager.getAllLoadedEmojis()
    }
  }

  // 取得熱門圖標
  getPopularIcons(limit = 20) {
    return {
      emojis: emojiManager.getPopularEmojis(limit),
      icons: this.getPopularBSIcons(limit)
    }
  }

  // 取得熱門 BS Icons (基於常用程度)
  getPopularBSIcons(limit = 20) {
    const popularClasses = [
      'bi-house', 'bi-person', 'bi-gear', 'bi-search', 'bi-bell', 'bi-heart',
      'bi-star', 'bi-check', 'bi-x', 'bi-plus', 'bi-dash', 'bi-arrow-right',
      'bi-arrow-left', 'bi-arrow-up', 'bi-arrow-down', 'bi-envelope',
      'bi-chat', 'bi-phone', 'bi-camera', 'bi-file'
    ]

    const allIcons = bsIconsManager.getAllLoadedIcons()
    const popular = allIcons.filter(icon => 
      popularClasses.some(cls => icon.class.includes(cls))
    )

    return popular.slice(0, limit)
  }

  // 取得載入狀態
  getLoadingStatus() {
    return {
      ...this.loadingStats,
      overall: {
        total: this.loadingStats.icons.total + this.loadingStats.emojis.total,
        loaded: this.loadingStats.icons.loaded + this.loadingStats.emojis.loaded,
        loading: this.loadingStats.icons.loading + this.loadingStats.emojis.loading,
        progress: Math.round(
          (this.loadingStats.icons.progress + this.loadingStats.emojis.progress) / 2
        )
      }
    }
  }

  // 取得記憶體使用統計
  getMemoryStats() {
    const iconStats = bsIconsManager.getMemoryStats()
    
    // EmojiManager 可能還沒有實作 getMemoryStats，使用預設值
    let emojiStats = {
      loadedCategories: 0,
      totalEmojis: 0,
      searchIndexSize: 0,
      estimatedMemoryKB: 0
    }
    
    if (typeof emojiManager.getMemoryStats === 'function') {
      emojiStats = emojiManager.getMemoryStats()
    } else {
      // 使用載入狀態來估算
      const loadingStatus = emojiManager.getEmojiLoadingStatus()
      emojiStats = {
        loadedCategories: loadingStatus.loaded,
        totalEmojis: loadingStatus.loaded * 200, // 估算每個分類約 200 個 emoji
        searchIndexSize: loadingStatus.loaded * 100,
        estimatedMemoryKB: loadingStatus.loaded * 50
      }
    }

    return {
      icons: iconStats,
      emojis: emojiStats,
      total: {
        loadedCategories: iconStats.loadedCategories + emojiStats.loadedCategories,
        totalIcons: iconStats.totalIcons + (emojiStats.totalEmojis || 0),
        searchIndexSize: iconStats.searchIndexSize + (emojiStats.searchIndexSize || 0),
        estimatedMemoryKB: iconStats.estimatedMemoryKB + (emojiStats.estimatedMemoryKB || 0)
      }
    }
  }

  // 取得分類映射
  getCategoryMaps() {
    return {
      icons: bsIconsManager.categoryMap,
      emojis: emojiManager.getCategoriesInfo ? 
        emojiManager.getCategoriesInfo().reduce((map, cat) => {
          map[cat.id] = { name: cat.name, icon: cat.icon, count: cat.count };
          return map;
        }, {}) : {}
    }
  }

  // 清除所有快取
  clearAllCache() {
    bsIconsManager.clearCache()
    emojiManager.clearEmojiCache()
    this.initialized = false
    if (process.env.NODE_ENV === 'development') {
      console.log('🧹 IconManager: 已清除所有快取')
    }
  }

  // 強制載入所有圖標
  async loadAllIcons() {
    if (process.env.NODE_ENV === 'development') {
      console.log('📦 IconManager: 開始載入所有圖標...')
    }
    
    try {
      await Promise.all([
        bsIconsManager.loadAllIcons(),
        emojiManager.loadAllEmojis()
      ])
      
      if (process.env.NODE_ENV === 'development') {
        console.log('✅ IconManager: 所有圖標載入完成')
      }
      this.logFinalStats()
    } catch (error) {
      console.error('❌ IconManager: 載入所有圖標失敗', error)
      throw error
    }
  }

  // 記錄最終統計
  logFinalStats() {
    // if (process.env.NODE_ENV !== 'development') return
    // 
    // const memStats = this.getMemoryStats()
    // const loadStats = this.getLoadingStatus()
    // 
    // console.log('📊 IconManager 最終統計:')
    // console.log(`   • 總圖標數: ${memStats.total.totalIcons.toLocaleString()} 個`)
    // console.log(`   • BS Icons: ${memStats.icons.totalIcons.toLocaleString()} 個 (${memStats.icons.loadedCategories} 分類)`)
    // console.log(`   • Emojis: ${memStats.emojis.totalEmojis.toLocaleString()} 個 (${memStats.emojis.loadedCategories} 分類)`)
    // console.log(`   • 搜尋索引大小: ${memStats.total.searchIndexSize.toLocaleString()} 項`)
    // console.log(`   • 預估記憶體使用: ${memStats.total.estimatedMemoryKB.toLocaleString()} KB`)
    // console.log(`   • 載入進度: ${loadStats.overall.progress}%`)
  }

  // 檢查是否已初始化
  isInitialized() {
    return this.initialized
  }

  // 取得版本資訊
  getVersion() {
    return {
      version: '1.0.0',
      buildDate: new Date().toISOString().split('T')[0],
      features: [
        'Bootstrap Icons 分類載入',
        'Emoji 分類載入', 
        '漸進式載入',
        '搜尋索引',
        '記憶體優化',
        '快取機制'
      ]
    }
  }
}

// 創建全域實例
const iconManager = new IconManager()

// 自動初始化
if (typeof window !== 'undefined') {
  // 當 DOM 準備好時初始化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      iconManager.initialize().catch(console.error)
    })
  } else {
    iconManager.initialize().catch(console.error)
  }
}

export default iconManager