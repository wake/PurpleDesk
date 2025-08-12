/**
 * Emoji 膚色工具函數
 * 處理 emoji 的膚色變化支援
 */

// 膚色修飾符 Unicode 範圍
export const SKIN_TONE_MODIFIERS = {
  '🏻': '\u{1F3FB}', // 淺膚色
  '🏼': '\u{1F3FC}', // 中淺膚色
  '🏽': '\u{1F3FD}', // 中膚色
  '🏾': '\u{1F3FE}', // 中深膚色
  '🏿': '\u{1F3FF}', // 深膚色
}

// 支援膚色變化的 emoji 基礎字符（Unicode 範圍）
// 根據 Unicode 標準，這些是支援 Emoji Modifier (skin tone) 的字符
const SKIN_TONE_COMPATIBLE_RANGES = [
  // 手勢和身體部位
  [0x261D, 0x261D], // ☝ 指向上
  [0x26F9, 0x26F9], // ⛹ 運動員
  [0x270A, 0x270D], // ✊✋✌✍ 手勢
  [0x1F385, 0x1F385], // 🎅 聖誕老人
  [0x1F3C2, 0x1F3C4], // 🏂🏃🏄 運動
  [0x1F3C7, 0x1F3C7], // 🏇 騎馬
  [0x1F3CA, 0x1F3CC], // 🏊🏋🏌 運動
  [0x1F442, 0x1F443], // 👂👃 耳朵鼻子
  [0x1F446, 0x1F450], // 👆-👐 手勢
  [0x1F466, 0x1F478], // 👦-👸 人物
  [0x1F47C, 0x1F47C], // 👼 天使
  [0x1F481, 0x1F483], // 💁💂💃 人物動作
  [0x1F485, 0x1F487], // 💅💆💇 美容
  [0x1F48F, 0x1F48F], // 💏 親吻
  [0x1F491, 0x1F491], // 💑 情侶
  [0x1F4AA, 0x1F4AA], // 💪 肌肉
  [0x1F574, 0x1F575], // 🕴🕵 偵探
  [0x1F57A, 0x1F57A], // 🕺 跳舞男
  [0x1F590, 0x1F590], // 🖐 手掌
  [0x1F595, 0x1F596], // 🖕🖖 手勢
  [0x1F645, 0x1F647], // 🙅🙆🙇 姿勢
  [0x1F64B, 0x1F64F], // 🙋-🙏 手勢
  [0x1F6A3, 0x1F6A3], // 🚣 划船
  [0x1F6B4, 0x1F6B6], // 🚴🚵🚶 騎車走路
  [0x1F6C0, 0x1F6C0], // 🛀 洗澡
  [0x1F6CC, 0x1F6CC], // 🛌 睡覺
  [0x1F90C, 0x1F90C], // 🤌 捏手指
  [0x1F90F, 0x1F90F], // 🤏 捏
  [0x1F918, 0x1F91F], // 🤘-🤟 手勢
  [0x1F926, 0x1F926], // 🤦 扶額
  [0x1F930, 0x1F939], // 🤰-🤹 孕婦雜技
  [0x1F93C, 0x1F93E], // 🤼🤽🤾 運動
  [0x1F977, 0x1F977], // 🥷 忍者
  [0x1F9B5, 0x1F9B6], // 🦵🦶 腿腳
  [0x1F9B8, 0x1F9B9], // 🦸🦹 超級英雄
  [0x1F9BB, 0x1F9BB], // 🦻 耳朵
  [0x1F9CD, 0x1F9CF], // 🧍🧎🧏 站立
  [0x1F9D1, 0x1F9DD], // 🧑-🧝 人物
  [0x1FAC3, 0x1FAC5], // 🫃🫄🫅 孕婦王室
  [0x1FAF0, 0x1FAF8], // 🫰-🫸 手勢
]

/**
 * 檢查 emoji 是否支援膚色變化
 * @param {string} emoji - 要檢查的 emoji
 * @returns {boolean} 是否支援膚色
 */
export function supportsSkinTone(emoji) {
  if (!emoji) return false
  
  // 移除已存在的膚色修飾符
  const cleanEmoji = removeSkinTone(emoji)
  
  // 獲取第一個碼點
  const codePoint = cleanEmoji.codePointAt(0)
  if (!codePoint) return false
  
  // 檢查是否在支援範圍內
  for (const [start, end] of SKIN_TONE_COMPATIBLE_RANGES) {
    if (codePoint >= start && codePoint <= end) {
      return true
    }
  }
  
  return false
}

/**
 * 移除 emoji 中的膚色修飾符
 * @param {string} emoji - 原始 emoji
 * @returns {string} 移除膚色後的 emoji
 */
export function removeSkinTone(emoji) {
  if (!emoji) return ''
  
  // 移除所有膚色修飾符 (U+1F3FB to U+1F3FF)
  return emoji.replace(/[\u{1F3FB}-\u{1F3FF}]/gu, '')
}

/**
 * 為 emoji 添加膚色修飾符
 * @param {string} emoji - 原始 emoji
 * @param {string} skinTone - 膚色修飾符（如 '🏻'）
 * @returns {string} 帶膚色的 emoji
 */
export function applySkinTone(emoji, skinTone) {
  if (!emoji) return ''
  
  // 如果沒有指定膚色，返回原始 emoji（移除現有膚色）
  if (!skinTone) {
    return removeSkinTone(emoji)
  }
  
  // 先移除現有的膚色
  const cleanEmoji = removeSkinTone(emoji)
  
  // 檢查是否支援膚色
  if (!supportsSkinTone(cleanEmoji)) {
    return cleanEmoji
  }
  
  // 添加新的膚色修飾符
  // 對於複合 emoji（如家庭），需要更複雜的處理
  // 這裡簡單處理單個字符的情況
  
  // 處理 ZWJ 序列（零寬連接符）
  if (cleanEmoji.includes('\u200D')) {
    // 複雜 emoji，每個人物部分都需要添加膚色
    const parts = cleanEmoji.split('\u200D')
    const modifiedParts = parts.map(part => {
      if (supportsSkinTone(part)) {
        return part + skinTone
      }
      return part
    })
    return modifiedParts.join('\u200D')
  }
  
  // 簡單 emoji，直接添加膚色
  return cleanEmoji + skinTone
}

/**
 * 獲取 emoji 的基礎形式（不含膚色）
 * @param {string} emoji - 原始 emoji
 * @returns {string} 基礎 emoji
 */
export function getBaseEmoji(emoji) {
  return removeSkinTone(emoji)
}

/**
 * 獲取 emoji 當前的膚色
 * @param {string} emoji - emoji
 * @returns {string} 膚色修飾符，如果沒有則返回空字串
 */
export function getCurrentSkinTone(emoji) {
  if (!emoji) return ''
  
  // 匹配膚色修飾符
  const match = emoji.match(/[\u{1F3FB}-\u{1F3FF}]/gu)
  return match ? match[0] : ''
}

/**
 * 檢查 emoji 數據是否可能支援膚色
 * 基於名稱的快速檢查
 * @param {object} emojiData - emoji 數據對象
 * @returns {boolean} 是否可能支援膚色
 */
export function mightSupportSkinTone(emojiData) {
  if (!emojiData || !emojiData.name) return false
  
  const name = emojiData.name.toLowerCase()
  
  // 關鍵詞列表，表示可能支援膚色的 emoji
  const keywords = [
    'hand', 'finger', 'fist', 'clap', 'wave', 'ok', 'peace', 'thumbs',
    'person', 'people', 'man', 'woman', 'boy', 'girl', 'baby', 'child',
    'face', 'head', 'nose', 'ear', 'muscle', 'leg', 'foot', 'feet',
    'dancer', 'dancing', 'running', 'walking', 'swimming', 'climbing',
    'superhero', 'villain', 'mage', 'fairy', 'vampire', 'mermaid',
    'elf', 'genie', 'zombie', 'detective', 'guard', 'construction',
    'prince', 'princess', 'bride', 'pregnant', 'breast-feeding',
    'angel', 'santa', 'mrs. claus', 'student', 'teacher', 'judge',
    'farmer', 'cook', 'mechanic', 'worker', 'scientist', 'technologist',
    'singer', 'artist', 'pilot', 'astronaut', 'firefighter', 'police',
    'skin tone', // 直接包含膚色的
  ]
  
  return keywords.some(keyword => name.includes(keyword))
}

/**
 * 從 emoji 數據生成所有膚色變體
 * @param {object} emojiData - emoji 數據對象
 * @returns {array} 所有膚色變體的數組
 */
export function generateSkinToneVariants(emojiData) {
  if (!emojiData || !emojiData.emoji) return []
  
  const baseEmoji = removeSkinTone(emojiData.emoji)
  
  if (!supportsSkinTone(baseEmoji)) {
    return [emojiData]
  }
  
  const variants = [
    { ...emojiData, emoji: baseEmoji, skinTone: '', name: `${emojiData.name} (預設)` }
  ]
  
  const toneNames = {
    '🏻': '淺膚色',
    '🏼': '中淺膚色',
    '🏽': '中膚色',
    '🏾': '中深膚色',
    '🏿': '深膚色',
  }
  
  for (const [tone, name] of Object.entries(toneNames)) {
    variants.push({
      ...emojiData,
      emoji: applySkinTone(baseEmoji, tone),
      skinTone: tone,
      name: `${emojiData.name} (${name})`
    })
  }
  
  return variants
}