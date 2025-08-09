# PurpleDesk 專案管理系統 - 產品需求文件 (PRD)

建立日期：2025-08-08
最後更新：2025-08-09
文件版本：1.1  

### 1.1 技術架構

#### 後端
- **框架**：Laravel 11
- **語言**：PHP 8.2+
- **資料庫**：MySQL 8.0
- **快取**：File Cache（本地檔案快取）
- **佇列**：Laravel Queue
- **檔案儲存**：Local Storage
- **認證**：Laravel Sanctum
- **即時通訊**：Laravel Echo Server / Pusher
- **WebSocket**：Laravel WebSockets / Socket.io

#### 前端
- **框架**：Vue 3
- **建構工具**：Vite
- **UI 框架**：Tailwind / Tailwind Components
- **狀態管理**：Pinia
- **路由**：Vue Router 4
- **HTTP 客戶端**：Axios
- **圖表**：Chart.js / ECharts
- **即時通訊客戶端**：Laravel Echo (JavaScript)
- **Socket 客戶端**：Socket.io-client

### 1.2 系統架構

```
┌─────────────────────────────────────────────────────┐
│                    前端應用 (Vue 3)                   │
├─────────────────────────────────────────────────────┤
│                    API Gateway                       │
├─────────────────────────────────────────────────────┤
│                  後端服務 (Laravel)                   │
├──────────────┬──────────────┬───────────────────────┤
│   MySQL      │  File Cache  │   File Storage        │
└──────────────┴──────────────┴───────────────────────┘
```

## 2. 專案現況

### 2.1 已完成項目
- ✅ Laravel 11 框架安裝與配置
- ✅ MySQL 資料庫配置（連線資訊待填寫）
- ✅ 本地檔案快取系統設置
- ✅ Vue 3 前端框架整合
- ✅ Pinia 狀態管理安裝
- ✅ Vue Router 4 路由系統安裝
- ✅ Axios HTTP 客戶端安裝
- ✅ Tailwind CSS 與表單插件配置
- ✅ Vite 建構工具整合
- ✅ 系統語系設定（zh_TW）
- ✅ 時區設定（Asia/Taipei）

## 3. 開發規範

### 3.1 程式碼規範
- 遵循 PSR-12 編碼標準（PHP）
- 使用 ESLint 與 Prettier（JavaScript）
- 採用 TDD 測試驅動開發

### 3.2 Git 工作流程
- 主分支：main
- 功能分支：feat/功能名稱
- 修正分支：fix/問題描述
- 每次提交使用語意化提交訊息

### 3.3 測試要求
- 單元測試覆蓋率 > 80%
- 所有 API 端點需有整合測試
- 關鍵業務邏輯需有端對端測試