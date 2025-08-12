<template>
  <div class="p-8 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Emoji Mart 測試頁面</h1>
    
    <div class="space-y-6">
      <!-- 測試區塊 1: 標準選擇器 -->
      <div class="border rounded-lg p-4">
        <h2 class="text-lg font-semibold mb-3">標準 Emoji 選擇器</h2>
        <div class="flex items-center gap-4">
          <label class="text-sm text-gray-600">選擇圖標：</label>
          <IconPicker 
            v-model="selectedEmoji1" 
            v-model:iconType="iconType1"
          />
          <div v-if="selectedEmoji1" class="text-sm">
            選中: <span class="text-xl">{{ selectedEmoji1 }}</span> 
            (類型: {{ iconType1 }})
          </div>
        </div>
      </div>

      <!-- 測試區塊 2: 直接開啟 Emoji+ 頁籤 -->
      <div class="border rounded-lg p-4">
        <h2 class="text-lg font-semibold mb-3">Emoji+ 頁籤測試</h2>
        <div class="flex items-center gap-4">
          <label class="text-sm text-gray-600">Emoji+ 圖標：</label>
          <IconPicker 
            v-model="selectedEmoji2" 
            v-model:iconType="iconType2"
            :iconType="'emojiMart'"
          />
          <div v-if="selectedEmoji2" class="text-sm">
            選中: <span class="text-xl">{{ selectedEmoji2 }}</span>
          </div>
        </div>
      </div>

      <!-- 測試區塊 3: 比較顯示效果 -->
      <div class="border rounded-lg p-4">
        <h2 class="text-lg font-semibold mb-3">Emoji 顯示效果比較</h2>
        
        <div class="grid grid-cols-2 gap-4">
          <!-- 原始 Emoji 系統 -->
          <div>
            <h3 class="font-medium mb-2">原始 Emoji 系統</h3>
            <div class="bg-gray-50 p-3 rounded">
              <div class="grid grid-cols-6 gap-2">
                <span title="smiling face">☺️</span>
                <span title="face in clouds">😶‍🌫️</span>
                <span title="face exhaling">😮‍💨</span>
                <span title="head shaking horizontally">🙂‍↔️</span>
                <span title="head shaking vertically">🙂‍↕️</span>
                <span title="hand with fingers splayed">🖐️</span>
                <span title="victory hand">✌️</span>
                <span title="index pointing up">☝️</span>
                <span title="person walking">🚶</span>
                <span title="person walking facing right">🚶‍➡️</span>
              </div>
            </div>
          </div>

          <!-- Emoji Mart 系統 -->
          <div>
            <h3 class="font-medium mb-2">Emoji Mart 系統</h3>
            <div class="bg-gray-50 p-3 rounded">
              <div class="grid grid-cols-6 gap-2" id="emojiMartDisplay">
                <!-- 將由 JavaScript 填充 -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 測試區塊 4: 搜尋功能測試 -->
      <div class="border rounded-lg p-4">
        <h2 class="text-lg font-semibold mb-3">搜尋功能測試</h2>
        <div class="space-y-3">
          <input 
            v-model="searchQuery"
            type="text"
            placeholder="輸入關鍵字測試搜尋..."
            class="w-full px-3 py-2 border rounded-md"
            @input="testSearch"
          />
          <div v-if="searchResults.length > 0" class="bg-gray-50 p-3 rounded">
            <div class="text-sm text-gray-600 mb-2">
              找到 {{ searchResults.length }} 個結果：
            </div>
            <div class="flex flex-wrap gap-2">
              <span 
                v-for="result in searchResults" 
                :key="result.id"
                class="text-xl cursor-pointer hover:bg-gray-200 p-1 rounded"
                :title="result.name"
              >
                {{ result.native }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- 統計資訊 -->
      <div class="border rounded-lg p-4 bg-blue-50">
        <h2 class="text-lg font-semibold mb-3">統計資訊</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <span class="font-medium">Emoji Mart 初始化狀態：</span>
            <span :class="emojiMartReady ? 'text-green-600' : 'text-red-600'">
              {{ emojiMartReady ? '已就緒' : '未初始化' }}
            </span>
          </div>
          <div v-if="emojiMartStats">
            <span class="font-medium">總 Emoji 數量：</span>
            {{ emojiMartStats.total }}
          </div>
          <div v-if="emojiMartStats">
            <span class="font-medium">分類數量：</span>
            {{ emojiMartStats.categories }}
          </div>
          <div v-if="emojiMartStats">
            <span class="font-medium">支援原生顯示：</span>
            <span :class="emojiMartStats.nativeSupport ? 'text-green-600' : 'text-orange-600'">
              {{ emojiMartStats.nativeSupport ? '是' : '否' }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import IconPicker from '../common/IconPicker.vue'
import emojiMartService from '../../utils/emoji-mart-service.js'

export default {
  name: 'EmojiMartTest',
  components: {
    IconPicker
  },
  setup() {
    const selectedEmoji1 = ref('')
    const selectedEmoji2 = ref('')
    const iconType1 = ref('')
    const iconType2 = ref('emojiMart')
    const searchQuery = ref('')
    const searchResults = ref([])
    const emojiMartReady = ref(false)
    const emojiMartStats = ref(null)

    const testSearch = async () => {
      if (!emojiMartReady.value) {
        await emojiMartService.init()
        emojiMartReady.value = true
      }
      
      if (searchQuery.value) {
        searchResults.value = emojiMartService.searchEmojis(searchQuery.value)
      } else {
        searchResults.value = []
      }
    }

    const loadEmojiMartExamples = async () => {
      await emojiMartService.init()
      emojiMartReady.value = true
      
      // 取得統計資訊
      const allEmojis = emojiMartService.getAllEmojis()
      const categories = emojiMartService.getCategories()
      
      emojiMartStats.value = {
        total: allEmojis.length,
        categories: categories.length,
        nativeSupport: emojiMartService.supportsNativeEmoji()
      }
      
      // 顯示處理過的 emoji
      const examples = [
        '☺️', '😶‍🌫️', '😮‍💨', '🙂‍↔️', '🙂‍↕️',
        '🖐️', '✌️', '☝️', '🚶', '🚶‍➡️'
      ]
      
      const container = document.getElementById('emojiMartDisplay')
      if (container) {
        container.innerHTML = examples
          .map(emoji => {
            const sanitized = emojiMartService.sanitizeEmoji(emoji)
            return `<span title="processed">${sanitized}</span>`
          })
          .join('')
      }
    }

    onMounted(() => {
      loadEmojiMartExamples()
    })

    return {
      selectedEmoji1,
      selectedEmoji2,
      iconType1,
      iconType2,
      searchQuery,
      searchResults,
      emojiMartReady,
      emojiMartStats,
      testSearch
    }
  }
}
</script>