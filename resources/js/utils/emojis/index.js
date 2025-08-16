/**
 * Emoji Manager - 使用 API 版本
 * 所有 emoji 資料從後端 API 載入
 */

// 直接匯出 api-manager 作為預設
export { default } from './api-manager.js';

// 匯出所有必要的介面
export { 
  EMOJI_CATEGORIES, 
  EMOJI_CATEGORY_INFO 
} from './api-manager.js';