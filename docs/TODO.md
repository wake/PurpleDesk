# PurpleDesk 專案開發待辦清單

## 📋 專案執行總覽

本文件記錄 PurpleDesk 專案管理系統的開發執行計畫，遵循 TDD 開發方法論，採用迭代式開發。

## ✅ 已完成項目

### 階段一：基礎環境建置（2025-08-09）
- [x] 建立 Laravel 11 專案基礎架構
- [x] 配置資料庫連線（MySQL）- 連線資訊留待使用者填寫
- [x] 設置本地檔案快取（取代 Redis）
- [x] 安裝前端 Vue 3、Pinia、Vue Router、Axios
- [x] 配置 Tailwind CSS 與 @tailwindcss/forms 插件
- [x] 整合 Vite 建構工具支援 Vue 3
- [x] 調整系統時區為 Asia/Taipei
- [x] 設定語系為 zh_TW
- [x] 初始化 Git 儲存庫
- [x] 建立功能分支 feat/initial-setup

## 📝 待辦項目

### 階段二：核心功能開發
- [ ] 使用者認證系統
  - [ ] 實作註冊功能（TDD）
  - [ ] 實作登入/登出功能（TDD）
  - [ ] 實作密碼重設功能（TDD）
  - [ ] 整合 Laravel Sanctum API 認證
  
- [ ] 專案管理模組
  - [ ] 建立專案 Model 與 Migration（TDD）
  - [ ] 實作專案 CRUD API（TDD）
  - [ ] 建立專案列表頁面（Vue）
  - [ ] 建立專案詳情頁面（Vue）
  
- [ ] 任務管理模組
  - [ ] 建立任務 Model 與 Migration（TDD）
  - [ ] 實作任務 CRUD API（TDD）
  - [ ] 實作任務狀態管理（TDD）
  - [ ] 建立任務看板介面（Vue）
  
- [ ] 檔案管理模組
  - [ ] 建立檔案 Model 與 Migration（TDD）
  - [ ] 實作檔案上傳/下載 API（TDD）
  - [ ] 實作版本控制功能（TDD）
  - [ ] 建立檔案瀏覽介面（Vue）

### 階段三：進階功能
- [ ] 團隊協作功能
  - [ ] 建立團隊與成員管理（TDD）
  - [ ] 實作權限控制系統（TDD）
  - [ ] 建立團隊邀請機制（TDD）
  
- [ ] 即時通訊功能
  - [ ] 整合 Laravel Echo/Pusher
  - [ ] 實作即時通知系統
  - [ ] 實作協作編輯功能
  
- [ ] 報表與分析
  - [ ] 建立專案進度報表
  - [ ] 實作任務統計分析
  - [ ] 整合圖表顯示（Chart.js/ECharts）

## 🔧 技術債務與優化
- [ ] 撰寫 API 文件
- [ ] 優化資料庫查詢效能
- [ ] 實作快取策略
- [ ] 增加整合測試覆蓋率
- [ ] 實作 CI/CD 流程

## 📌 注意事項
1. 每個功能開發前先撰寫測試（TDD）
2. 每次提交前確保所有測試通過
3. 使用語意化版本控制提交訊息
4. 定期更新此文件追蹤進度
