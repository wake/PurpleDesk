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

// 清除快取函數（用於重新載入）
export function clearEmojiCache() {
  allEmojisCache = [];
  isLoading = false;
  loadPromise = null;
}

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
          // 過濾 emoji：只保留基礎版本（無膚色修飾符）
          const baseEmojis = new Map(); // 使用 Map 來避免重複的基礎 emoji
          
          emojis.forEach(emoji => {
            // 移除膚色修飾符，取得基礎 emoji
            const baseEmoji = emoji.emoji.replace(SKIN_TONE_REGEX, '');
            
            // 只保留第一個遇到的基礎版本（通常是無膚色修飾符的）
            if (!baseEmojis.has(baseEmoji)) {
              // 優先使用原本沒有膚色修飾符的版本
              if (!SKIN_TONE_REGEX.test(emoji.emoji)) {
                baseEmojis.set(baseEmoji, {
                  emoji: emoji.emoji,
                  name: emoji.name,
                  category: category.name,
                  categoryId: category.id,
                  subgroup: emoji.subgroup
                });
              } else {
                // 如果原始就有膚色修飾符，創建基礎版本
                baseEmojis.set(baseEmoji, {
                  emoji: baseEmoji,
                  name: emoji.name.replace(/: (light|medium-light|medium|medium-dark|dark) skin tone/, ''),
                  category: category.name,
                  categoryId: category.id,
                  subgroup: emoji.subgroup
                });
              }
            }
          });
          
          // 將基礎 emoji 加入結果陣列
          allEmojis.push(...baseEmojis.values());
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
