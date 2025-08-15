# Icon/Avatar 系統規範

## 預設圖像標準

### 1. 組織預設圖像
- **圖標**: Hero Icon v1 `office-building` (outline)
- **背景色**: 淡紫色 (`#faf5ff` purple-50)
- **圖標色**: 深紫色 (`#7c3aed` purple-600)
- **使用時機**: 組織未設定自訂 Logo 時

### 2. 團隊預設圖像  
- **圖標**: Bootstrap Icon `bi-people`
- **背景色**: 淡藍色 (`#dbeafe` 或 `blue-100`)
- **圖標色**: 深藍色 (`#2563eb` 或 `blue-600`)
- **使用時機**: 團隊未設定自訂圖像時

### 3. 個人頭像預設
- **內容**: 文字/字母 (姓名首字母或自訂文字)
- **背景色**: `bg-primary-500` (`#6366f1` 或 primary 主色)
- **文字色**: 白色 (`#ffffff`)
- **使用時機**: 用戶未上傳頭像或未設定自訂圖標時

## 自訂圖像規範

### 4. 非預設頭像規則
- **背景色**: 可任意設定，但必須來自 IconPicker ColorPicker 預設顏色
- **內容顏色**: 僅限白色或深色
  - 白色: `#ffffff`
  - 深色: `#000000`, `#374151` (gray-700), `#1f2937` (gray-800)
- **對比度**: 確保背景與內容有足夠對比度以保證可讀性

### 5. 可用背景顏色 (IconPicker ColorPicker 預設色)
```javascript
// 從 IconPicker 中提取的標準顏色
const ALLOWED_BACKGROUND_COLORS = [
  '#ef4444', // red-500
  '#f97316', // orange-500  
  '#eab308', // yellow-500
  '#22c55e', // green-500
  '#06b6d4', // cyan-500
  '#3b82f6', // blue-500
  '#6366f1', // indigo-500 (primary)
  '#8b5cf6', // violet-500
  '#d946ef', // fuchsia-500
  '#ec4899', // pink-500
  '#6b7280', // gray-500
  '#000000', // black
  // 淡色版本
  '#fee2e2', // red-100
  '#fed7aa', // orange-100
  '#fef3c7', // yellow-100
  '#d1fae5', // green-100
  '#cffafe', // cyan-100
  '#dbeafe', // blue-100
  '#e0e7ff', // indigo-100
  '#ede9fe', // violet-100
  '#fae8ff', // fuchsia-100
  '#fce7f3', // pink-100
  '#f3f4f6'  // gray-100
]
```

## 重置行為規範

### 6. 頭像重置規則
當用戶選擇「重置頭像」時，系統應恢復到對應類型的預設設定：

- **組織重置** → Hero Icon office-building + 淡紫背景 + 深紫圖標
- **團隊重置** → Bootstrap Icon bi-people + 淡藍背景 + 深藍圖標  
- **個人重置** → 姓名首字母文字 + primary-500背景 + 白色文字

## 假資料規範

### 7. 測試資料要求
- 所有假資料必須符合上述顏色規範
- 非預設頭像的背景色必須來自允許的顏色清單
- 文字/圖標顏色必須是白色或深色
- 至少包含各種類型的範例組合

### 8. 實作檢查清單
- [ ] 組織預設圖像更新
- [ ] 團隊預設圖像更新  
- [ ] 個人頭像預設更新
- [ ] 假資料顏色驗證
- [ ] 重置功能實作
- [ ] ColorPicker 顏色限制

---

建立日期: 2025-08-15  
最後更新: 2025-08-15