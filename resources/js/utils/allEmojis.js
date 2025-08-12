/**
 * 完整 Emoji 集合匯出
 * 從新的 Unicode 16.0 emoji 系統載入所有 emoji
 * 提供同步的 emoji 陣列供 IconPicker 使用
 */

import emojiManager from './emojis/index.js';

// 膚色修飾符的 Unicode 範圍
const SKIN_TONE_REGEX = /[\u{1F3FB}-\u{1F3FF}]/gu;

// 建立用於同步存取的 emoji 陣列
let allEmojisCache = [];
let isLoading = false;
let loadPromise = null;

/**
 * 載入所有 emoji 分類並整合成單一陣列
 */
async function loadAllEmojis() {
  if (allEmojisCache.length > 0) {
    return allEmojisCache;
  }

  if (isLoading && loadPromise) {
    return loadPromise;
  }

  isLoading = true;
  loadPromise = (async () => {
    try {
      const categories = emojiManager.getCategoriesInfo();
      const allEmojis = [];

      // 載入所有分類
      for (const category of categories) {
        try {
          const emojis = await emojiManager.getEmojisByCategory(category.id);
          // 將每個 emoji 加入陣列，保留分類資訊
          // 過濾掉包含膚色修飾符的變體，只保留基礎 emoji
          emojis.forEach(emoji => {
            // 檢查是否包含膚色修飾符
            if (!SKIN_TONE_REGEX.test(emoji.emoji)) {
              allEmojis.push({
                emoji: emoji.emoji,
                name: emoji.name,
                category: category.name,
                categoryId: category.id,
                subgroup: emoji.subgroup
              });
            }
          });
        } catch (error) {
          console.warn(`載入 ${category.name} 分類失敗:`, error);
        }
      }

      allEmojisCache = allEmojis;
      console.log(`成功載入 ${allEmojis.length} 個 emoji`);
      return allEmojis;
    } catch (error) {
      console.error('載入 emoji 失敗:', error);
      // 如果載入失敗，返回空陣列
      return [];
    } finally {
      isLoading = false;
    }
  })();

  return loadPromise;
}

/**
 * 取得已載入的 emoji（同步）
 * 如果還沒載入，會返回空陣列並觸發背景載入
 */
export function getLoadedEmojis() {
  if (allEmojisCache.length === 0 && !isLoading) {
    // 觸發背景載入
    loadAllEmojis();
  }
  return allEmojisCache;
}

/**
 * 確保 emoji 已載入（非同步）
 */
export async function ensureEmojisLoaded() {
  return await loadAllEmojis();
}

// 預設匯出為 emoji 陣列（會觸發載入）
const allEmojis = [];

// 建立 Proxy 來延遲載入，但確保初始時返回有效陣列
export default new Proxy(allEmojis, {
  get(target, prop) {
    // 確保快取已初始化
    if (allEmojisCache.length === 0 && !isLoading) {
      // 觸發背景載入
      loadAllEmojis();
      // 初始時返回空陣列，避免 undefined 錯誤
      if (prop === 'length') return 0;
      if (prop === 'filter' || prop === 'map' || prop === 'forEach' || prop === 'find' || prop === 'slice') {
        return function(...args) {
          return [][prop](...args);
        };
      }
      if (typeof prop === 'string' && !isNaN(prop)) {
        return undefined;
      }
      return target[prop];
    }
    
    // 快取已載入，使用快取內容
    if (prop === 'length') {
      return allEmojisCache.length;
    }
    
    if (typeof prop === 'string' && !isNaN(prop)) {
      return allEmojisCache[prop];
    }
    
    if (typeof allEmojisCache[prop] === 'function') {
      return function(...args) {
        return allEmojisCache[prop](...args);
      };
    }
    
    // 處理 Symbol.iterator 等特殊屬性
    if (prop === Symbol.iterator) {
      return allEmojisCache[Symbol.iterator].bind(allEmojisCache);
    }
    
    return allEmojisCache[prop];
  },
  
  has(target, prop) {
    if (allEmojisCache.length === 0) return false;
    return prop in allEmojisCache;
  },
  
  ownKeys(target) {
    if (allEmojisCache.length === 0) return [];
    return Object.keys(allEmojisCache);
  },
  
  getOwnPropertyDescriptor(target, prop) {
    if (allEmojisCache.length === 0) return undefined;
    return Object.getOwnPropertyDescriptor(allEmojisCache, prop);
  }
});

// 頁面載入時自動開始載入
if (typeof window !== 'undefined') {
  // 延遲載入，避免阻塞初始化
  setTimeout(() => {
    loadAllEmojis();
  }, 100);
}
