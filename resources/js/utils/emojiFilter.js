/**
 * Emoji 相容性過濾模組
 * 基於用戶確認結果自動生成 (2025-08-13T18:24:54.395Z)
 * 
 * 統計資料:
 * - 總測試: 383 個 emoji
 * - 實際問題: 57 個 (14.9%)
 * - 預測準確度: 100.0%
 */

// 確認有問題的 emoji 黑名單 (57 個)
export const PROBLEMATIC_EMOJIS = new Set([
  "🇨🇶",
  "🫩",
  "🫆",
  "🪾",
  "🫜",
  "🪉",
  "🪏",
  "🫟",
  "🚶‍♀️‍➡️",
  "🚶‍♂️‍➡️",
  "🧎‍♀️‍➡️",
  "🧎‍♂️‍➡️",
  "🏃‍♀️‍➡️",
  "🏃‍♂️‍➡️",
  "🧑‍🦯‍➡️",
  "👨‍🦯‍➡️",
  "👩‍🦯‍➡️",
  "🧑‍🦼‍➡️",
  "👨‍🦼‍➡️",
  "👩‍🦼‍➡️",
  "🧑‍🦽‍➡️",
  "👨‍🦽‍➡️",
  "👩‍🦽‍➡️",
  "🧑‍🧑‍🧒‍🧒",
  "🙂‍↔️",
  "🙂‍↕️",
  "🚶‍➡️",
  "🧎‍➡️",
  "🏃‍➡️",
  "🧑‍🧑‍🧒",
  "🧑‍🧒‍🧒",
  "⛓️‍💥",
  "🧑‍🧒",
  "🐦‍🔥",
  "🍋‍🟩",
  "🍄‍🟫",
  "🐦‍⬛",
  "🫨",
  "🩷",
  "🩵",
  "🩶",
  "🫷",
  "🫸",
  "🫎",
  "🫏",
  "🪽",
  "🪿",
  "🪼",
  "🪻",
  "🫚",
  "🫛",
  "🪭",
  "🪮",
  "🪇",
  "🪈",
  "🪯",
  "🛜"
]);

// 版本過濾規則
export const VERSION_RULES = {
  "15": "block",
  "16": "block",
  "15.1": "block"
};

// 風險因子規則
export const FACTOR_RULES = {
  "FLAG_SEQUENCE": "high_risk"
};

/**
 * 檢查 emoji 是否應該過濾
 * @param {Object} emojiData - emoji 資料對象
 * @param {string} emojiData.emoji - emoji 字符
 * @param {number} emojiData.version - Unicode 版本
 * @param {Array} emojiData.factors - 風險因子
 * @returns {Object} - {shouldFilter: boolean, reason: string, riskLevel: string}
 */
export function shouldFilterEmoji(emojiData) {
    // 檢查黑名單
    if (PROBLEMATIC_EMOJIS.has(emojiData.emoji)) {
        return {
            shouldFilter: true,
            reason: '用戶確認有顯示問題',
            riskLevel: 'high'
        };
    }
    
    // 檢查版本規則
    if (emojiData.version && VERSION_RULES[emojiData.version]) {
        const rule = VERSION_RULES[emojiData.version];
        if (rule === 'block') {
            return {
                shouldFilter: true,
                reason: `Unicode ${emojiData.version} 高問題率版本`,
                riskLevel: 'high'
            };
        } else if (rule === 'high_risk') {
            return {
                shouldFilter: false,
                reason: `Unicode ${emojiData.version} 中風險版本`,
                riskLevel: 'medium'
            };
        }
    }
    
    // 檢查因子規則
    if (emojiData.factors) {
        for (const factor of emojiData.factors) {
            if (FACTOR_RULES[factor] === 'high_risk') {
                return {
                    shouldFilter: false,
                    reason: `包含高風險因子: ${factor}`,
                    riskLevel: 'medium'
                };
            }
        }
    }
    
    return {
        shouldFilter: false,
        reason: '通過所有檢查',
        riskLevel: 'low'
    };
}

/**
 * 過濾 emoji 陣列，移除有問題的 emoji
 * @param {Array} emojis - emoji 陣列
 * @returns {Array} - 過濾後的 emoji 陣列
 */
export function filterEmojis(emojis) {
    return emojis.filter(emoji => {
        const emojiStr = typeof emoji === 'string' ? emoji : emoji.emoji;
        return !PROBLEMATIC_EMOJIS.has(emojiStr);
    });
}

/**
 * 統計資訊
 */
export const FILTER_STATS = {
  "totalTested": 383,
  "actualProblems": 57,
  "problemRate": 14.9,
  "predictionAccuracy": 100,
  "generatedAt": "2025-08-13T18:24:54.396Z"
};
