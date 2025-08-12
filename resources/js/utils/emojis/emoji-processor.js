/**
 * Emoji Processor
 * 處理 emoji 顯示問題，移除不必要的修飾符和處理 ZWJ 序列
 */

/**
 * 移除單獨的變化選擇器和不可見字符
 * VS15 (U+FE0E) - 文字樣式
 * VS16 (U+FE0F) - emoji 樣式
 * ZWJ (U+200D) - 零寬度連接符
 */
export function cleanEmoji(emoji) {
  if (!emoji) return '';
  
  // 保留完整的 ZWJ 序列（如組合 emoji）
  // 但移除孤立的不可見字符
  const cleaned = emoji
    // 移除單獨的變化選擇器（不在 ZWJ 序列中的）
    .replace(/(?<![\u200D])\uFE0F/g, '')
    .replace(/(?<![\u200D])\uFE0E/g, '')
    // 移除末尾的孤立 ZWJ
    .replace(/\u200D$/g, '');
  
  return cleaned;
}

/**
 * 檢測是否為 ZWJ 序列 emoji
 */
export function hasZWJSequence(emoji) {
  return emoji.includes('\u200D');
}

/**
 * 檢測是否包含變化選擇器
 */
export function hasVariationSelector(emoji) {
  return emoji.includes('\uFE0F') || emoji.includes('\uFE0E');
}

/**
 * 檢測是否為組合 emoji（包含修飾符）
 */
export function isCompositeEmoji(emoji) {
  // 檢查是否包含膚色修飾符
  const skinToneModifiers = /[\u{1F3FB}-\u{1F3FF}]/u;
  // 檢查是否包含性別符號
  const genderSymbols = /[\u{2640}\u{2642}]/u;
  // 檢查是否包含方向箭頭
  const directionalArrows = /[\u{2194}-\u{2199}\u{21A9}-\u{21AA}]/u;
  
  return skinToneModifiers.test(emoji) || 
         genderSymbols.test(emoji) || 
         directionalArrows.test(emoji) ||
         hasZWJSequence(emoji);
}

/**
 * 將 emoji 轉換為安全顯示格式
 * 對於複雜的組合 emoji，返回基礎 emoji
 */
export function toSafeEmoji(emoji) {
  if (!emoji) return '';
  
  // 如果是簡單 emoji，只需清理變化選擇器
  if (!isCompositeEmoji(emoji)) {
    return cleanEmoji(emoji);
  }
  
  // 對於包含 ZWJ 的組合 emoji
  if (hasZWJSequence(emoji)) {
    // 嘗試保留完整序列，但清理不必要的修飾符
    const cleaned = cleanEmoji(emoji);
    
    // 如果清理後仍然有問題，提取第一個基礎 emoji
    if (cleaned.includes('\u200D')) {
      // 某些系統可能不支援複雜 ZWJ 序列
      // 可選：返回第一個基礎 emoji
      const firstEmoji = cleaned.split('\u200D')[0];
      return cleanEmoji(firstEmoji);
    }
    
    return cleaned;
  }
  
  // 對於包含膚色修飾符的 emoji，保留完整序列
  const skinToneMatch = emoji.match(/([\u{1F3FB}-\u{1F3FF}])/u);
  if (skinToneMatch) {
    return cleanEmoji(emoji);
  }
  
  // 其他情況，清理並返回
  return cleanEmoji(emoji);
}

/**
 * 批量處理 emoji 列表
 */
export function processEmojiList(emojiList) {
  return emojiList.map(item => ({
    ...item,
    emoji: toSafeEmoji(item.emoji),
    originalEmoji: item.emoji // 保留原始版本以供參考
  }));
}

/**
 * 檢測系統是否支援完整 emoji 顯示
 */
export function supportsFullEmoji() {
  // 檢測是否為較新的瀏覽器
  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');
  
  if (!ctx) return false;
  
  ctx.font = '1px sans-serif';
  
  // 測試一個複雜的 ZWJ emoji
  const testEmoji = '👨‍👩‍👧‍👦'; // 家庭 emoji
  const width = ctx.measureText(testEmoji).width;
  
  // 如果寬度異常大，說明不支援 ZWJ 序列
  return width < 10;
}

/**
 * 根據系統支援程度選擇處理策略
 */
export function getEmojiProcessor() {
  const fullSupport = supportsFullEmoji();
  
  return {
    process: fullSupport ? cleanEmoji : toSafeEmoji,
    supportsZWJ: fullSupport,
    supportsSkinTone: true, // 大多數現代系統都支援膚色
    supportsGender: fullSupport
  };
}

/**
 * 用於 Vue 組件的 emoji 處理混入
 */
export const emojiProcessorMixin = {
  methods: {
    processEmoji(emoji) {
      return toSafeEmoji(emoji);
    },
    
    cleanEmoji(emoji) {
      return cleanEmoji(emoji);
    },
    
    isComplexEmoji(emoji) {
      return isCompositeEmoji(emoji);
    }
  }
};

// 導出處理器實例
export const emojiProcessor = getEmojiProcessor();