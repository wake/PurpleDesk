/**
 * IconDisplay 組件的全域尺寸配置
 * 
 * 根據開發工具測試結果設定的最佳尺寸比例
 * 基準容器尺寸：80x80px (md size)
 */

export const ICON_DISPLAY_CONFIG = {
  global: {
    // CJK 文字配置（中文、日文、韓文）
    textCJK: {
      fontSize: '1.1rem',      // 適合中文字體顯示
      marginTop: '0.05em'      // 微調垂直對齊
    },
    
    // 拉丁文字配置（英文、歐洲語系）
    textLatin: {
      fontSize: '1.4rem',      // 拉丁字母需要較大尺寸
      marginTop: '0.05em'      // 微調垂直對齊
    },
    
    // Emoji 配置
    emoji: {
      fontSize: '1.7rem',      // Emoji 需要較大尺寸以保持清晰
      marginTop: '0.1em'       // Emoji 需要更多垂直調整
    },
    
    // 圖標配置（Hero Icons、Bootstrap Icons）
    icon: {
      size: '1.8rem'           // 圖標尺寸，Hero Icons 使用 width/height
    }
  },
  
  // 尺寸特殊配置（如果某個尺寸需要特殊調整）
  sizeSpecific: {
    'xs': {
      textCJK: { fontSize: '0.55rem' },
      textLatin: { fontSize: '0.7rem' },
      emoji: { fontSize: '0.85rem' },
      icon: { size: '0.9rem' }
    },
    'sm': {
      textCJK: { fontSize: '0.7rem' },
      textLatin: { fontSize: '0.9rem' },
      emoji: { fontSize: '1.1rem' },
      icon: { size: '1.15rem' }
    },
    'lg': {
      textCJK: { fontSize: '1.3rem' },
      textLatin: { fontSize: '1.65rem' },
      emoji: { fontSize: '2.0rem' },
      icon: { size: '2.1rem' }
    },
    'xl': {
      textCJK: { fontSize: '1.65rem' },
      textLatin: { fontSize: '2.1rem' },
      emoji: { fontSize: '2.55rem' },
      icon: { size: '2.7rem' }
    }
  }
}

/**
 * 獲取指定尺寸的配置
 * @param {string} size - 尺寸名稱 (xs, sm, md, lg, xl 等)
 * @param {string} type - 內容類型 (textCJK, textLatin, emoji, icon)
 * @returns {object} 配置物件
 */
export function getIconDisplayConfig(size, type) {
  // 先檢查是否有尺寸特殊配置
  const sizeSpecific = ICON_DISPLAY_CONFIG.sizeSpecific[size]
  if (sizeSpecific && sizeSpecific[type]) {
    return {
      ...ICON_DISPLAY_CONFIG.global[type],
      ...sizeSpecific[type]
    }
  }
  
  // 使用全域配置
  return ICON_DISPLAY_CONFIG.global[type] || {}
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