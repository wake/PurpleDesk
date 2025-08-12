<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>圖標系統測試頁面</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .category-section {
            margin: 2rem 0;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
        }
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .icon-item {
            text-align: center;
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .icon-item:hover {
            background: #f3f4f6;
            transform: scale(1.05);
        }
        .emoji-item {
            font-size: 2rem;
        }
        .loading {
            color: #6b7280;
            font-style: italic;
        }
        .stats {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: white;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">圖標系統測試頁面</h1>
        
        <div class="stats" id="stats">
            <h3 class="font-semibold mb-2">載入狀態</h3>
            <div id="loading-status" class="text-sm text-gray-600">
                <div>BS Icons: <span id="bs-status">初始化中...</span></div>
                <div>Emoji: <span id="emoji-status">初始化中...</span></div>
            </div>
        </div>

        <!-- Bootstrap Icons 測試 -->
        <div class="category-section">
            <h2 class="text-2xl font-semibold text-gray-800">Bootstrap Icons</h2>
            <div class="mt-4">
                <button onclick="testBSIcons()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    載入 Bootstrap Icons
                </button>
                <button onclick="loadBSCategory('general')" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 ml-2">
                    載入 General 分類
                </button>
                <button onclick="loadBSCategory('ui')" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 ml-2">
                    載入 UI 分類
                </button>
            </div>
            <div id="bs-icons" class="icon-grid"></div>
        </div>

        <!-- Emoji 測試 -->
        <div class="category-section">
            <h2 class="text-2xl font-semibold text-gray-800">Emoji</h2>
            <div class="mt-4">
                <button onclick="testEmojis()" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                    載入 Emoji
                </button>
                <button onclick="loadEmojiCategory('smileys_emotion')" class="px-4 py-2 bg-pink-500 text-white rounded hover:bg-pink-600 ml-2">
                    載入表情符號
                </button>
                <button onclick="loadEmojiCategory('flags')" class="px-4 py-2 bg-pink-500 text-white rounded hover:bg-pink-600 ml-2">
                    載入旗幟
                </button>
                <button onclick="loadEmojiCategory('people_body')" class="px-4 py-2 bg-pink-500 text-white rounded hover:bg-pink-600 ml-2">
                    載入人物
                </button>
            </div>
            <div id="emoji-list" class="icon-grid"></div>
        </div>

        <!-- 搜尋測試 -->
        <div class="category-section">
            <h2 class="text-2xl font-semibold text-gray-800">搜尋測試</h2>
            <div class="mt-4">
                <input type="text" id="search-input" placeholder="輸入搜尋關鍵字..." 
                    class="px-4 py-2 border rounded-lg mr-2" style="width: 300px;">
                <button onclick="searchIcons()" class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">
                    搜尋
                </button>
            </div>
            <div id="search-results" class="mt-4"></div>
        </div>
    </div>

    <script type="module">
        import iconManager from '/resources/js/utils/iconManager.js';
        import bsIconsManager from '/resources/js/utils/icons/index.js';
        import emojiManager from '/resources/js/utils/emojis/index.js';

        // 設為全域以便按鈕調用
        window.iconManager = iconManager;
        window.bsIconsManager = bsIconsManager;
        window.emojiManager = emojiManager;

        // 初始化
        async function init() {
            try {
                console.log('開始初始化圖標管理系統...');
                await iconManager.initialize();
                console.log('圖標管理系統初始化完成');
                
                // 更新狀態
                updateStatus();
                
                // 設定定時更新
                setInterval(updateStatus, 1000);
            } catch (error) {
                console.error('初始化失敗:', error);
            }
        }

        // 更新載入狀態
        function updateStatus() {
            const bsStatus = bsIconsManager.getLoadingStatus();
            const emojiStatus = emojiManager.getEmojiLoadingStatus();
            
            document.getElementById('bs-status').textContent = 
                `${bsStatus.loaded}/${bsStatus.total} (${bsStatus.progress}%)`;
            
            document.getElementById('emoji-status').textContent = 
                `${emojiStatus.loaded}/${emojiStatus.total} (${emojiStatus.progress}%)`;
        }

        // 測試 Bootstrap Icons
        window.testBSIcons = async function() {
            const container = document.getElementById('bs-icons');
            container.innerHTML = '<div class="loading">載入中...</div>';
            
            try {
                const icons = await bsIconsManager.getAllLoadedIcons();
                container.innerHTML = '';
                
                icons.slice(0, 50).forEach(icon => {
                    const div = document.createElement('div');
                    div.className = 'icon-item';
                    div.innerHTML = `
                        <i class="bi ${icon.class} text-2xl"></i>
                        <div class="text-xs mt-1">${icon.name}</div>
                    `;
                    container.appendChild(div);
                });
                
                console.log(`載入了 ${icons.length} 個 Bootstrap Icons`);
            } catch (error) {
                container.innerHTML = `<div class="text-red-500">載入失敗: ${error.message}</div>`;
                console.error('載入 BS Icons 失敗:', error);
            }
        };

        // 載入特定 BS Icons 分類
        window.loadBSCategory = async function(category) {
            const container = document.getElementById('bs-icons');
            container.innerHTML = '<div class="loading">載入中...</div>';
            
            try {
                const icons = await bsIconsManager.getIconsByCategory(category);
                container.innerHTML = '';
                
                icons.forEach(icon => {
                    const div = document.createElement('div');
                    div.className = 'icon-item';
                    div.innerHTML = `
                        <i class="bi ${icon.class} text-2xl"></i>
                        <div class="text-xs mt-1">${icon.name}</div>
                    `;
                    container.appendChild(div);
                });
                
                console.log(`載入了 ${category} 分類的 ${icons.length} 個圖標`);
            } catch (error) {
                container.innerHTML = `<div class="text-red-500">載入失敗: ${error.message}</div>`;
                console.error(`載入 ${category} 分類失敗:`, error);
            }
        };

        // 測試 Emoji
        window.testEmojis = async function() {
            const container = document.getElementById('emoji-list');
            container.innerHTML = '<div class="loading">載入中...</div>';
            
            try {
                // 獲取所有已載入的分類資訊
                const categories = emojiManager.getCategoriesInfo();
                container.innerHTML = '';
                
                // 顯示每個分類的資訊
                for (const category of categories) {
                    const div = document.createElement('div');
                    div.className = 'icon-item emoji-item';
                    div.innerHTML = `
                        <div>${category.icon}</div>
                        <div class="text-xs mt-1">${category.name}</div>
                        <div class="text-xs text-gray-500">${category.count} emojis</div>
                    `;
                    div.onclick = () => loadEmojiCategory(category.id);
                    container.appendChild(div);
                }
                
                console.log('顯示 Emoji 分類資訊');
            } catch (error) {
                container.innerHTML = `<div class="text-red-500">載入失敗: ${error.message}</div>`;
                console.error('載入 Emoji 失敗:', error);
            }
        };

        // 載入特定 Emoji 分類
        window.loadEmojiCategory = async function(category) {
            const container = document.getElementById('emoji-list');
            container.innerHTML = '<div class="loading">載入中...</div>';
            
            try {
                const emojis = await emojiManager.getEmojisByCategory(category);
                container.innerHTML = '';
                
                emojis.slice(0, 100).forEach(emoji => {
                    const div = document.createElement('div');
                    div.className = 'icon-item emoji-item';
                    div.innerHTML = `
                        <div>${emoji.emoji}</div>
                        <div class="text-xs mt-1">${emoji.name}</div>
                    `;
                    container.appendChild(div);
                });
                
                console.log(`載入了 ${category} 分類的 ${emojis.length} 個 emoji`);
            } catch (error) {
                container.innerHTML = `<div class="text-red-500">載入失敗: ${error.message}</div>`;
                console.error(`載入 ${category} 分類失敗:`, error);
            }
        };

        // 搜尋功能
        window.searchIcons = async function() {
            const query = document.getElementById('search-input').value;
            const container = document.getElementById('search-results');
            
            if (!query) {
                container.innerHTML = '<div class="text-gray-500">請輸入搜尋關鍵字</div>';
                return;
            }
            
            container.innerHTML = '<div class="loading">搜尋中...</div>';
            
            try {
                const results = await iconManager.searchIcons(query);
                container.innerHTML = '';
                
                if (results.total === 0) {
                    container.innerHTML = '<div class="text-gray-500">沒有找到結果</div>';
                    return;
                }
                
                const resultDiv = document.createElement('div');
                resultDiv.innerHTML = `
                    <h3 class="font-semibold mb-2">找到 ${results.total} 個結果</h3>
                    <div class="icon-grid">
                `;
                
                // 顯示 BS Icons 結果
                results.icons.slice(0, 20).forEach(icon => {
                    const div = document.createElement('div');
                    div.className = 'icon-item';
                    div.innerHTML = `
                        <i class="bi ${icon.class} text-2xl"></i>
                        <div class="text-xs mt-1">${icon.name}</div>
                    `;
                    resultDiv.appendChild(div);
                });
                
                // 顯示 Emoji 結果
                results.emojis.slice(0, 20).forEach(emoji => {
                    const div = document.createElement('div');
                    div.className = 'icon-item emoji-item';
                    div.innerHTML = `
                        <div>${emoji.emoji}</div>
                        <div class="text-xs mt-1">${emoji.name}</div>
                    `;
                    resultDiv.appendChild(div);
                });
                
                container.appendChild(resultDiv);
            } catch (error) {
                container.innerHTML = `<div class="text-red-500">搜尋失敗: ${error.message}</div>`;
                console.error('搜尋失敗:', error);
            }
        };

        // 頁面載入時初始化
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>