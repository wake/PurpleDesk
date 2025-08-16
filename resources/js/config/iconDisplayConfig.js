/**
 * IconDisplay 組件的全域尺寸配置
 * 
 * 根據開發工具測試結果設定的最佳尺寸比例
 * 基準容器尺寸：80x80px (md size)
 */

export const ICON_DISPLAY_CONFIG = {
  // 全域基礎配置
  global: {
    textCJK: { fontSize: '1.1rem', marginTop: '0.05em' },
    textLatin: { fontSize: '1.4rem', marginTop: '0.05em' },
    emoji: { fontSize: '1.7rem', marginTop: '0.1em' },
    icon: { size: '1.8rem' }
  },

  // 按尺寸分類的詳細配置
  textCJKBySize: {
    '4': { fontSize: '0.3rem' },
    '5': { fontSize: '0.4rem' },
    '6': { fontSize: '0.5rem' },
    '8': { fontSize: '0.7rem' },
    '10': { fontSize: '0.9rem' },
    '12': { fontSize: '1.1rem' },
    'xs': { fontSize: '0.5rem' },
    'sm': { fontSize: '0.7rem' },
    'md': { fontSize: '0.9rem' },
    'lg': { fontSize: '1.1rem' },
    'xl': { fontSize: '1.5rem' },
    '2xl': { fontSize: '1.9rem' },
    '3xl': { fontSize: '2.2rem' }
  },

  textLatinBySize: {
    '4': { fontSize: '0.4rem' },
    '5': { fontSize: '0.5rem' },
    '6': { fontSize: '0.6rem' },
    '8': { fontSize: '0.9rem' },
    '10': { fontSize: '1.1rem' },
    '12': { fontSize: '1.4rem' },
    'xs': { fontSize: '0.6rem' },
    'sm': { fontSize: '0.9rem' },
    'md': { fontSize: '1.1rem' },
    'lg': { fontSize: '1.4rem' },
    'xl': { fontSize: '2rem' },
    '2xl': { fontSize: '2.4rem' },
    '3xl': { fontSize: '3rem' }
  },

  emojiBySize: {
    '4': { fontSize: '0.4rem' },
    '5': { fontSize: '0.5rem' },
    '6': { fontSize: '0.8rem' },
    '8': { fontSize: '1.1rem' },
    '10': { fontSize: '1.3rem' },
    '12': { fontSize: '1.7rem' },
    'xs': { fontSize: '0.8rem' },
    'sm': { fontSize: '1.1rem' },
    'md': { fontSize: '1.3rem' },
    'lg': { fontSize: '1.7rem' },
    'xl': { fontSize: '2.4rem' },
    '2xl': { fontSize: '2.9rem' },
    '3xl': { fontSize: '3.6rem' }
  },

  iconBySize: {
    '4': { size: '0.5rem' },
    '5': { size: '0.6rem' },
    '6': { size: '0.8rem' },
    '8': { size: '1rem' },
    '10': { size: '1.3rem' },
    '12': { size: '1.8rem' },
    'xs': { size: '0.8rem' },
    'sm': { size: '1rem' },
    'md': { size: '1.3rem' },
    'lg': { size: '1.8rem' },
    'xl': { size: '2.2rem' },
    '2xl': { size: '2.6rem' },
    '3xl': { size: '3.4rem' }
  },

  heroIconBySize: {
    '4': { size: '0.5rem' },
    '5': { size: '0.6rem' },
    '6': { size: '0.8rem' },
    '8': { size: '1.1rem' },
    '10': { size: '1.4rem' },
    '12': { size: '1.8rem' },
    'xs': { size: '0.8rem' },
    'sm': { size: '1.1rem' },
    'md': { size: '1.4rem' },
    'lg': { size: '1.8rem' },
    'xl': { size: '2.3rem' },
    '2xl': { size: '2.8rem' },
    '3xl': { size: '3.6rem' }
  }
}

/**
 * 獲取指定尺寸和類型的配置
 * @param {string} size - 尺寸名稱 (4, 5, 6, 8, 10, 12, xs, sm, md, lg, xl, 2xl, 3xl)
 * @param {string} contentType - 內容類型 (text, emoji, bootstrap_icon, hero_icon)
 * @param {string} text - 文字內容（用於 CJK/Latin 判斷）
 * @returns {object} 配置物件
 */
export function getIconDisplayConfig(size, contentType, text = '') {
  const baseConfig = {
    marginTop: '0.05em'
  }

  switch (contentType) {
    case 'text':
      const textType = isCJKText(text) ? 'textCJKBySize' : 'textLatinBySize'
      const textConfig = ICON_DISPLAY_CONFIG[textType][size]
      return {
        ...baseConfig,
        ...textConfig,
        marginTop: '0.05em'
      }

    case 'emoji':
      const emojiConfig = ICON_DISPLAY_CONFIG.emojiBySize[size]
      return {
        ...baseConfig,
        ...emojiConfig,
        marginTop: '0.1em'
      }

    case 'hero_icon':
      const heroConfig = ICON_DISPLAY_CONFIG.heroIconBySize[size]
      return {
        ...baseConfig,
        ...heroConfig
      }

    case 'bootstrap_icon':
      const iconConfig = ICON_DISPLAY_CONFIG.iconBySize[size]
      return {
        ...baseConfig,
        ...iconConfig
      }

    default:
      // 回退到全域配置
      return ICON_DISPLAY_CONFIG.global[contentType] || baseConfig
  }
}

/**
 * 判斷文字是否為 CJK 字符
 * @param {string} text - 要判斷的文字
 * @returns {boolean} 是否包含 CJK 字符
 */
export function isCJKText(text) {
  if (!text) return false
  
  // CJK 字符範圍的正則表達式
  const cjkRegex = /[\u4e00-\u9fff\u3040-\u309f\u30a0-\u30ff\uac00-\ud7af]/
  return cjkRegex.test(text)
}

/**
 * 根據文字內容自動選擇文字類型
 * @param {string} text - 文字內容
 * @returns {string} 文字類型 ('textCJK' | 'textLatin')
 */
export function getTextType(text) {
  return isCJKText(text) ? 'textCJK' : 'textLatin'
}