<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h1 class="text-2xl font-bold text-gray-900">Icon 尺寸調整工具</h1>
          <p class="mt-2 text-sm text-gray-600">
            直觀調整所有 icon 類型在各種尺寸下的顯示效果
          </p>
        </div>

        <div class="p-6 space-y-8">
          
          <!-- 載入狀態 -->
          <div v-if="loading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>

          <div v-else>
            <!-- 全域基礎設定 -->
            <section class="bg-blue-50 border border-blue-200 p-6 rounded-lg">
              <h2 class="text-lg font-semibold text-blue-900 mb-4">全域基礎設定 (影響所有類型)</h2>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-blue-800 mb-2">CJK 文字基礎大小 (rem)</label>
                  <input 
                    v-model.number="config.global.textCJK.fontSize" 
                    type="number" 
                    min="0.5" 
                    max="4" 
                    step="0.125"
                    class="w-full rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500"
                  >
                  <p class="text-xs text-blue-600 mt-1">建議: 1.1rem</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-blue-800 mb-2">拉丁語系基礎大小 (rem)</label>
                  <input 
                    v-model.number="config.global.textLatin.fontSize" 
                    type="number" 
                    min="0.5" 
                    max="4" 
                    step="0.125"
                    class="w-full rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500"
                  >
                  <p class="text-xs text-blue-600 mt-1">建議: 1.4rem</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-blue-800 mb-2">Emoji 基礎大小 (rem)</label>
                  <input 
                    v-model.number="config.global.emoji.fontSize" 
                    type="number" 
                    min="1" 
                    max="5" 
                    step="0.25" 
                    class="w-full rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500"
                  >
                  <p class="text-xs text-blue-600 mt-1">建議: 1.7rem</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-blue-800 mb-2">Icon 基礎大小 (rem)</label>
                  <input 
                    v-model.number="config.global.icon.size" 
                    type="number" 
                    min="1" 
                    max="5" 
                    step="0.25" 
                    class="w-full rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500"
                  >
                  <p class="text-xs text-blue-600 mt-1">建議: 1.8rem</p>
                </div>
              </div>
            </section>
            
            <!-- 各類型完整測試與調整 -->
            <div class="space-y-8">
              
              <!-- CJK 文字類型 -->
              <section class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4">
                  <h3 class="text-lg font-medium text-gray-900">CJK 文字類型 (中日韓，最多2字)</h3>
                </div>
                <div class="p-6 space-y-6">
                  
                  <!-- CJK 文字類型的尺寸調整 -->
                  <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-3">
                      <h4 class="font-medium text-green-800">CJK 文字專用尺寸調整 (rem)</h4>
                      <div class="flex space-x-2">
                        <button 
                          @click="toggleAllSizes('textCJK', !isAllSelected('textCJK'))"
                          class="px-3 py-1 text-xs font-medium rounded border"
                          :class="isAllSelected('textCJK') ? 
                            'bg-green-600 text-white border-green-600' : 
                            'bg-white text-green-700 border-green-300 hover:bg-green-50'"
                        >
                          {{ isAllSelected('textCJK') ? '全清' : '全選' }}
                        </button>
                        <button 
                          @click="syncSelectedSizes('textCJK')"
                          :disabled="!isAnySelected('textCJK')"
                          class="px-3 py-1 text-xs font-medium rounded border"
                          :class="isAnySelected('textCJK') ? 
                            'bg-green-600 text-white border-green-600 hover:bg-green-700' : 
                            'bg-gray-300 text-gray-500 border-gray-300 cursor-not-allowed'"
                        >
                          同步選中項目
                        </button>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                      <div v-for="size in allSizes" :key="size" class="space-y-1">
                        <div class="flex items-center space-x-1">
                          <input 
                            type="checkbox"
                            v-model="selectedSizes.textCJK[size]"
                            class="w-3 h-3 text-green-600 border-green-300 rounded focus:ring-green-500"
                          >
                          <label class="block text-xs font-medium text-green-700">{{ size }}</label>
                        </div>
                        <input 
                          v-model.number="getTextCJKConfig(size).fontSize" 
                          type="number" 
                          min="0.5" 
                          max="4" 
                          step="0.125"
                          @input="updateTextCJKConfig(size, $event.target.value)"
                          class="w-full text-xs rounded border-green-300 focus:border-green-500 focus:ring-green-500"
                        >
                      </div>
                    </div>
                  </div>
                  
                  <!-- CJK 文字測試矩陣 -->
                  <div class="space-y-4">
                    <div v-for="textExample in cjkTextExamples" :key="textExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ textExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <TestIconDisplay 
                            :icon-data="textExample.data" 
                            :size="size" 
                            :test-config="getComputedConfig(size, 'textCJK')"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                          <p class="text-xs text-green-600">{{ getTextCJKConfig(size).fontSize }}rem</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
              
              <!-- 拉丁語系文字類型 -->
              <section class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4">
                  <h3 class="text-lg font-medium text-gray-900">拉丁語系文字類型 (最多2字)</h3>
                </div>
                <div class="p-6 space-y-6">
                  
                  <!-- 拉丁語系文字類型的尺寸調整 -->
                  <div class="bg-teal-50 border border-teal-200 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-3">
                      <h4 class="font-medium text-teal-800">拉丁語系文字專用尺寸調整 (rem)</h4>
                      <div class="flex space-x-2">
                        <button 
                          @click="toggleAllSizes('textLatin', !isAllSelected('textLatin'))"
                          class="px-3 py-1 text-xs font-medium rounded border"
                          :class="isAllSelected('textLatin') ? 
                            'bg-teal-600 text-white border-teal-600' : 
                            'bg-white text-teal-700 border-teal-300 hover:bg-teal-50'"
                        >
                          {{ isAllSelected('textLatin') ? '全清' : '全選' }}
                        </button>
                        <button 
                          @click="syncSelectedSizes('textLatin')"
                          :disabled="!isAnySelected('textLatin')"
                          class="px-3 py-1 text-xs font-medium rounded border"
                          :class="isAnySelected('textLatin') ? 
                            'bg-teal-600 text-white border-teal-600 hover:bg-teal-700' : 
                            'bg-gray-300 text-gray-500 border-gray-300 cursor-not-allowed'"
                        >
                          同步選中項目
                        </button>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                      <div v-for="size in allSizes" :key="size" class="space-y-1">
                        <div class="flex items-center space-x-1">
                          <input 
                            type="checkbox"
                            v-model="selectedSizes.textLatin[size]"
                            class="w-3 h-3 text-teal-600 border-teal-300 rounded focus:ring-teal-500"
                          >
                          <label class="block text-xs font-medium text-teal-700">{{ size }}</label>
                        </div>
                        <input 
                          v-model.number="getTextLatinConfig(size).fontSize" 
                          type="number" 
                          min="0.5" 
                          max="4" 
                          step="0.125"
                          @input="updateTextLatinConfig(size, $event.target.value)"
                          class="w-full text-xs rounded border-teal-300 focus:border-teal-500 focus:ring-teal-500"
                        >
                      </div>
                    </div>
                  </div>
                  
                  <!-- 拉丁語系文字測試矩陣 -->
                  <div class="space-y-4">
                    <div v-for="textExample in latinTextExamples" :key="textExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ textExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <TestIconDisplay 
                            :icon-data="textExample.data" 
                            :size="size" 
                            :test-config="getComputedConfig(size, 'textLatin')"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                          <p class="text-xs text-teal-600">{{ getTextLatinConfig(size).fontSize }}rem</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
              
              <!-- Emoji 類型 -->
              <section class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4">
                  <h3 class="text-lg font-medium text-gray-900">Emoji 類型</h3>
                </div>
                <div class="p-6 space-y-6">
                  
                  <!-- Emoji 類型的尺寸調整 -->
                  <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-3">
                      <h4 class="font-medium text-yellow-800">Emoji 專用尺寸調整 (rem)</h4>
                      <div class="flex space-x-2">
                        <button 
                          @click="toggleAllSizes('emoji', !isAllSelected('emoji'))"
                          class="px-3 py-1 text-xs font-medium rounded border"
                          :class="isAllSelected('emoji') ? 
                            'bg-yellow-600 text-white border-yellow-600' : 
                            'bg-white text-yellow-700 border-yellow-300 hover:bg-yellow-50'"
                        >
                          {{ isAllSelected('emoji') ? '全清' : '全選' }}
                        </button>
                        <button 
                          @click="syncSelectedSizes('emoji')"
                          :disabled="!isAnySelected('emoji')"
                          class="px-3 py-1 text-xs font-medium rounded border"
                          :class="isAnySelected('emoji') ? 
                            'bg-yellow-600 text-white border-yellow-600 hover:bg-yellow-700' : 
                            'bg-gray-300 text-gray-500 border-gray-300 cursor-not-allowed'"
                        >
                          同步選中項目
                        </button>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                      <div v-for="size in allSizes" :key="size" class="space-y-1">
                        <div class="flex items-center space-x-1">
                          <input 
                            type="checkbox"
                            v-model="selectedSizes.emoji[size]"
                            class="w-3 h-3 text-yellow-600 border-yellow-300 rounded focus:ring-yellow-500"
                          >
                          <label class="block text-xs font-medium text-yellow-700">{{ size }}</label>
                        </div>
                        <input 
                          v-model.number="getEmojiConfig(size).fontSize" 
                          type="number" 
                          min="0.5" 
                          max="6" 
                          step="0.25" 
                          @input="updateEmojiConfig(size, $event.target.value)"
                          class="w-full text-xs rounded border-yellow-300 focus:border-yellow-500 focus:ring-yellow-500"
                        >
                      </div>
                    </div>
                  </div>
                  
                  <!-- Emoji 測試矩陣 -->
                  <div class="space-y-4">
                    <div v-for="emojiExample in emojiExamples" :key="emojiExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ emojiExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <TestIconDisplay 
                            :icon-data="emojiExample.data" 
                            :size="size" 
                            :test-config="getComputedConfig(size, 'emoji')"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                          <p class="text-xs text-yellow-600">{{ getEmojiConfig(size).fontSize }}rem</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
              
              <!-- Hero Icons 類型 -->
              <section class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4">
                  <h3 class="text-lg font-medium text-gray-900">Hero Icons</h3>
                </div>
                <div class="p-6 space-y-6">
                  
                  <!-- Hero Icons 類型的尺寸調整 -->
                  <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-3">
                      <h4 class="font-medium text-indigo-800">Hero Icons 專用尺寸調整 (rem)</h4>
                      <div class="flex space-x-2">
                        <button 
                          @click="toggleAllSizes('heroIcon', !isAllSelected('heroIcon'))"
                          class="px-3 py-1 text-xs font-medium rounded border"
                          :class="isAllSelected('heroIcon') ? 
                            'bg-indigo-600 text-white border-indigo-600' : 
                            'bg-white text-indigo-700 border-indigo-300 hover:bg-indigo-50'"
                        >
                          {{ isAllSelected('heroIcon') ? '全清' : '全選' }}
                        </button>
                        <button 
                          @click="syncSelectedSizes('heroIcon')"
                          :disabled="!isAnySelected('heroIcon')"
                          class="px-3 py-1 text-xs font-medium rounded border"
                          :class="isAnySelected('heroIcon') ? 
                            'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700' : 
                            'bg-gray-300 text-gray-500 border-gray-300 cursor-not-allowed'"
                        >
                          同步選中項目
                        </button>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                      <div v-for="size in allSizes" :key="size" class="space-y-1">
                        <div class="flex items-center space-x-1">
                          <input 
                            type="checkbox"
                            v-model="selectedSizes.heroIcon[size]"
                            class="w-3 h-3 text-indigo-600 border-indigo-300 rounded focus:ring-indigo-500"
                          >
                          <label class="block text-xs font-medium text-indigo-700">{{ size }}</label>
                        </div>
                        <input 
                          v-model.number="getHeroIconConfig(size).size" 
                          type="number" 
                          min="0.5" 
                          max="6" 
                          step="0.25" 
                          @input="updateHeroIconConfig(size, $event.target.value)"
                          class="w-full text-xs rounded border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                      </div>
                    </div>
                  </div>
                  
                  <!-- Hero Icons 測試矩陣 -->
                  <div class="space-y-4">
                    <div v-for="heroExample in heroExamples" :key="heroExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ heroExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <TestIconDisplay 
                            :icon-data="heroExample.data" 
                            :size="size" 
                            :test-config="getComputedConfig(size, 'hero_icon')"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                          <p class="text-xs text-indigo-600">{{ getHeroIconConfig(size).size }}rem</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
              
              <!-- Bootstrap Icons 類型 -->
              <section class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4">
                  <h3 class="text-lg font-medium text-gray-900">Bootstrap Icons</h3>
                </div>
                <div class="p-6 space-y-6">
                  
                  <!-- Bootstrap Icons 類型的尺寸調整 -->
                  <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-3">
                      <h4 class="font-medium text-purple-800">Bootstrap Icons 專用尺寸調整 (rem)</h4>
                      <div class="flex space-x-2">
                        <button 
                          @click="toggleAllSizes('bootstrapIcon', !isAllSelected('bootstrapIcon'))"
                          class="px-3 py-1 text-xs font-medium rounded border"
                          :class="isAllSelected('bootstrapIcon') ? 
                            'bg-purple-600 text-white border-purple-600' : 
                            'bg-white text-purple-700 border-purple-300 hover:bg-purple-50'"
                        >
                          {{ isAllSelected('bootstrapIcon') ? '全清' : '全選' }}
                        </button>
                        <button 
                          @click="syncSelectedSizes('bootstrapIcon')"
                          :disabled="!isAnySelected('bootstrapIcon')"
                          class="px-3 py-1 text-xs font-medium rounded border"
                          :class="isAnySelected('bootstrapIcon') ? 
                            'bg-purple-600 text-white border-purple-600 hover:bg-purple-700' : 
                            'bg-gray-300 text-gray-500 border-gray-300 cursor-not-allowed'"
                        >
                          同步選中項目
                        </button>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                      <div v-for="size in allSizes" :key="size" class="space-y-1">
                        <div class="flex items-center space-x-1">
                          <input 
                            type="checkbox"
                            v-model="selectedSizes.bootstrapIcon[size]"
                            class="w-3 h-3 text-purple-600 border-purple-300 rounded focus:ring-purple-500"
                          >
                          <label class="block text-xs font-medium text-purple-700">{{ size }}</label>
                        </div>
                        <input 
                          v-model.number="getIconConfig(size).size" 
                          type="number" 
                          min="0.5" 
                          max="6" 
                          step="0.25" 
                          @input="updateIconConfig(size, $event.target.value)"
                          class="w-full text-xs rounded border-purple-300 focus:border-purple-500 focus:ring-purple-500"
                        >
                      </div>
                    </div>
                  </div>
                  
                  <!-- Bootstrap Icons 測試矩陣 -->
                  <div class="space-y-4">
                    <div v-for="bsExample in bsExamples" :key="bsExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ bsExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <TestIconDisplay 
                            :icon-data="bsExample.data" 
                            :size="size" 
                            :test-config="getComputedConfig(size, 'bootstrap_icon')"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                          <p class="text-xs text-purple-600">{{ getIconConfig(size).size }}rem</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
              
              <!-- 圖片類型 -->
              <section class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4">
                  <h3 class="text-lg font-medium text-gray-900">圖片類型</h3>
                </div>
                <div class="p-6">
                  <div class="space-y-3">
                    <h4 class="text-sm font-medium text-gray-700">示例圖片 (自動縮放)</h4>
                    <div class="flex flex-wrap gap-3 items-end">
                      <div v-for="size in allSizes" :key="size" class="text-center">
                        <TestIconDisplay 
                          :icon-data="imageExample.data" 
                          :size="size"
                        />
                        <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                        <p class="text-xs text-gray-400">自動</p>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>
            
            <!-- 動作按鈕 -->
            <section class="bg-gray-50 border border-gray-200 p-6 rounded-lg">
              <div class="flex space-x-4">
                <button 
                  @click="resetToDefaults" 
                  class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  重設為預設值
                </button>
                <button 
                  @click="exportConfig" 
                  class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700"
                >
                  匯出完整配置
                </button>
                <button 
                  @click="clearCustomConfigs" 
                  class="px-6 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-md hover:bg-red-50"
                >
                  清除自訂配置
                </button>
              </div>
            </section>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, reactive } from 'vue'
import { useRouter } from 'vue-router'
import IconDisplay from '@/components/common/IconDisplay.vue'
import TestIconDisplay from '@/components/dev-tool/TestIconDisplay.vue'
import axios from 'axios'

export default {
  name: 'IconSizeTestSimple',
  components: {
    IconDisplay,
    TestIconDisplay
  },
  setup() {
    const loading = ref(true)
    const users = ref([])
    const organizations = ref([])
    const teams = ref([])
    const router = useRouter()
    
    // 所有可用尺寸
    const allSizes = ['4', '5', '6', '8', '10', '12', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl']
    
    // 勾選狀態管理
    const selectedSizes = reactive({
      textCJK: {},
      textLatin: {},
      emoji: {},
      heroIcon: {},
      bootstrapIcon: {}
    })
    
    // 初始化勾選狀態
    allSizes.forEach(size => {
      selectedSizes.textCJK[size] = false
      selectedSizes.textLatin[size] = false
      selectedSizes.emoji[size] = false
      selectedSizes.heroIcon[size] = false
      selectedSizes.bootstrapIcon[size] = false
    })
    
    // 配置系統
    const config = reactive({
      // 全域基礎設定
      global: {
        textCJK: { fontSize: 1.1 },
        textLatin: { fontSize: 1.4 },
        emoji: { fontSize: 1.7 },
        icon: { size: 1.8 }
      },
      // 各尺寸的 CJK 文字調整
      textCJKBySize: {
        "4": { fontSize: 0.3 },
        "5": { fontSize: 0.4 },
        "6": { fontSize: 0.5 },
        "8": { fontSize: 0.7 },
        "10": { fontSize: 0.9 },
        "12": { fontSize: 1.1 },
        "xs": { fontSize: 0.5 },
        "sm": { fontSize: 0.7 },
        "md": { fontSize: 0.9 },
        "lg": { fontSize: 1.1 },
        "xl": { fontSize: 1.5 },
        "2xl": { fontSize: 1.9 },
        "3xl": { fontSize: 2.2 }
      },
      // 各尺寸的拉丁文字調整
      textLatinBySize: {
        "4": { fontSize: 0.4 },
        "5": { fontSize: 0.5 },
        "6": { fontSize: 0.6 },
        "8": { fontSize: 0.9 },
        "10": { fontSize: 1.1 },
        "12": { fontSize: 1.4 },
        "xs": { fontSize: 0.6 },
        "sm": { fontSize: 0.9 },
        "md": { fontSize: 1.1 },
        "lg": { fontSize: 1.4 },
        "xl": { fontSize: 2 },
        "2xl": { fontSize: 2.4 },
        "3xl": { fontSize: 3 }
      },
      // 各尺寸的 emoji 調整
      emojiBySize: {
        "4": { fontSize: 0.4 },
        "5": { fontSize: 0.5 },
        "6": { fontSize: 0.8 },
        "8": { fontSize: 1.1 },
        "10": { fontSize: 1.3 },
        "12": { fontSize: 1.7 },
        "xs": { fontSize: 0.8 },
        "sm": { fontSize: 1.1 },
        "md": { fontSize: 1.3 },
        "lg": { fontSize: 1.7 },
        "xl": { fontSize: 2.4 },
        "2xl": { fontSize: 2.9 },
        "3xl": { fontSize: 3.6 }
      },
      // 各尺寸的 hero icon 調整
      heroIconBySize: {
        "4": { size: 0.5 },
        "5": { size: 0.6 },
        "6": { size: 0.8 },
        "8": { size: 1.1 },
        "10": { size: 1.4 },
        "12": { size: 1.8 },
        "xs": { size: 0.8 },
        "sm": { size: 1.1 },
        "md": { size: 1.4 },
        "lg": { size: 1.8 },
        "xl": { size: 2.3 },
        "2xl": { size: 2.8 },
        "3xl": { size: 3.6 }
      },
      // 各尺寸的 bootstrap icon 調整
      iconBySize: {
        "4": { size: 0.5 },
        "5": { size: 0.6 },
        "6": { size: 0.8 },
        "8": { size: 1 },
        "10": { size: 1.3 },
        "12": { size: 1.8 },
        "xs": { size: 0.8 },
        "sm": { size: 1 },
        "md": { size: 1.3 },
        "lg": { size: 1.8 },
        "xl": { size: 2.2 },
        "2xl": { size: 2.6 },
        "3xl": { size: 3.4 }
      }
    })
    
    // CJK 測試範例資料
    const cjkTextExamples = [
      {
        key: 'chinese_1',
        name: '中文 1 字',
        data: { type: 'text', text: '李', backgroundColor: '#f0fdf4', textColor: '#374151' }
      },
      {
        key: 'chinese_2', 
        name: '中文 2 字',
        data: { type: 'text', text: '李華', backgroundColor: '#fef2f2', textColor: '#374151' }
      },
      {
        key: 'japanese_1',
        name: '日文 1 字',
        data: { type: 'text', text: '田', backgroundColor: '#eff6ff', textColor: '#374151' }
      },
      {
        key: 'japanese_2',
        name: '日文 2 字',
        data: { type: 'text', text: '田中', backgroundColor: '#faf5ff', textColor: '#374151' }
      },
      {
        key: 'korean_1',
        name: '韓文 1 字',
        data: { type: 'text', text: '김', backgroundColor: '#fff7ed', textColor: '#374151' }
      }
    ]
    
    // 拉丁語系測試範例資料
    const latinTextExamples = [
      {
        key: 'english_1',
        name: '英文 1 字母',
        data: { type: 'text', text: 'A', backgroundColor: '#f0f9ff', textColor: '#374151' }
      },
      {
        key: 'english_2',
        name: '英文 2 字母',
        data: { type: 'text', text: 'AB', backgroundColor: '#f0fdf4', textColor: '#374151' }
      },
      {
        key: 'french',
        name: '法文字母',
        data: { type: 'text', text: 'É', backgroundColor: '#fef2f2', textColor: '#374151' }
      },
      {
        key: 'german',
        name: '德文字母',
        data: { type: 'text', text: 'Ü', backgroundColor: '#fffbeb', textColor: '#374151' }
      },
      {
        key: 'spanish',
        name: '西班牙文',
        data: { type: 'text', text: 'Ñ', backgroundColor: '#f7fee7', textColor: '#374151' }
      }
    ]
    
    const emojiExamples = [
      {
        key: 'simple',
        name: '簡單 Emoji',
        data: { type: 'emoji', emoji: '😀', backgroundColor: '#fef2f2' }
      },
      {
        key: 'complex',
        name: '複雜 Emoji',
        data: { type: 'emoji', emoji: '👨‍💻', backgroundColor: '#f0f9ff' }
      },
      {
        key: 'skin_tone',
        name: '膚色修飾',
        data: { type: 'emoji', emoji: '👋🏻', backgroundColor: '#fff7ed' }
      }
    ]
    
    const heroExamples = [
      {
        key: 'outline',
        name: 'User Outline (簡單圖示)',
        data: { type: 'hero_icon', icon: 'user', backgroundColor: '#f0f9ff', iconColor: '#0ea5e9', style: 'outline' }
      },
      {
        key: 'solid',
        name: 'Heart Solid (實心圖示)',
        data: { type: 'hero_icon', icon: 'heart', backgroundColor: '#fdf2f8', iconColor: '#ec4899', style: 'solid' }
      }
    ]
    
    const bsExamples = [
      {
        key: 'outline',
        name: 'Star Outline',
        data: { type: 'bootstrap_icon', icon: 'star', backgroundColor: '#fffbeb', iconColor: '#f59e0b', style: 'outline' }
      },
      {
        key: 'fill',
        name: 'Lightning Fill',
        data: { type: 'bootstrap_icon', icon: 'lightning-fill', backgroundColor: '#f7fee7', iconColor: '#84cc16', style: 'fill' }
      }
    ]
    
    const imageExample = {
      name: '圖片類型',
      data: { type: 'image', path: 'https://via.placeholder.com/80x80/6366f1/ffffff?text=IMG' }
    }
    
    // 取得 CJK 文字配置
    const getTextCJKConfig = (size) => {
      if (!config.textCJKBySize[size]) {
        config.textCJKBySize[size] = { fontSize: config.global.textCJK.fontSize }
      }
      return config.textCJKBySize[size]
    }
    
    // 取得拉丁語系文字配置
    const getTextLatinConfig = (size) => {
      if (!config.textLatinBySize[size]) {
        config.textLatinBySize[size] = { fontSize: config.global.textLatin.fontSize }
      }
      return config.textLatinBySize[size]
    }
    
    // 取得 Emoji 配置
    const getEmojiConfig = (size) => {
      if (!config.emojiBySize[size]) {
        config.emojiBySize[size] = { fontSize: config.global.emoji.fontSize }
      }
      return config.emojiBySize[size]
    }
    
    // 取得 Hero Icon 配置
    const getHeroIconConfig = (size) => {
      if (!config.heroIconBySize[size]) {
        config.heroIconBySize[size] = { size: config.global.icon.size }
      }
      return config.heroIconBySize[size]
    }
    
    // 取得 Bootstrap Icon 配置
    const getIconConfig = (size) => {
      if (!config.iconBySize[size]) {
        config.iconBySize[size] = { size: config.global.icon.size }
      }
      return config.iconBySize[size]
    }
    
    // 更新配置
    const updateTextCJKConfig = (size, value) => {
      getTextCJKConfig(size).fontSize = Number(value)
    }
    
    const updateTextLatinConfig = (size, value) => {
      getTextLatinConfig(size).fontSize = Number(value)
    }
    
    const updateEmojiConfig = (size, value) => {
      getEmojiConfig(size).fontSize = Number(value)
    }
    
    const updateHeroIconConfig = (size, value) => {
      getHeroIconConfig(size).size = Number(value)
    }
    
    const updateIconConfig = (size, value) => {
      getIconConfig(size).size = Number(value)
    }
    
    // 取得計算後的配置 (用於 TestIconDisplay)
    const getComputedConfig = (size, type) => {
      const result = {
        text: { fontSize: `${config.global.textCJK.fontSize}rem` },
        emoji: { fontSize: `${config.global.emoji.fontSize}rem` },
        icon: { size: `${config.global.icon.size}rem` }
      }
      
      // 覆蓋特定配置
      if (type === 'textCJK' && config.textCJKBySize[size]) {
        result.text.fontSize = `${config.textCJKBySize[size].fontSize}rem`
      } else if (type === 'textLatin' && config.textLatinBySize[size]) {
        result.text.fontSize = `${config.textLatinBySize[size].fontSize}rem`
      } else if (type === 'emoji' && config.emojiBySize[size]) {
        result.emoji.fontSize = `${config.emojiBySize[size].fontSize}rem`
      } else if (type === 'hero_icon' && config.heroIconBySize[size]) {
        result.icon.size = `${config.heroIconBySize[size].size}rem`
      } else if (type === 'bootstrap_icon' && config.iconBySize[size]) {
        result.icon.size = `${config.iconBySize[size].size}rem`
      }
      
      return result
    }
    
    const loadData = async () => {
      // 模擬載入
      loading.value = false
    }
    
    const resetToDefaults = () => {
      config.global.textCJK.fontSize = 1.1
      config.global.textLatin.fontSize = 1.4
      config.global.emoji.fontSize = 1.7
      config.global.icon.size = 1.8
      
      // 重置到預調整的預設配置
      config.textCJKBySize = {
        "4": { fontSize: 0.3 },
        "5": { fontSize: 0.4 },
        "6": { fontSize: 0.5 },
        "8": { fontSize: 0.7 },
        "10": { fontSize: 0.9 },
        "12": { fontSize: 1.1 },
        "xs": { fontSize: 0.5 },
        "sm": { fontSize: 0.7 },
        "md": { fontSize: 0.9 },
        "lg": { fontSize: 1.1 },
        "xl": { fontSize: 1.5 },
        "2xl": { fontSize: 1.9 },
        "3xl": { fontSize: 2.2 }
      }
      
      config.textLatinBySize = {
        "4": { fontSize: 0.4 },
        "5": { fontSize: 0.5 },
        "6": { fontSize: 0.6 },
        "8": { fontSize: 0.9 },
        "10": { fontSize: 1.1 },
        "12": { fontSize: 1.4 },
        "xs": { fontSize: 0.6 },
        "sm": { fontSize: 0.9 },
        "md": { fontSize: 1.1 },
        "lg": { fontSize: 1.4 },
        "xl": { fontSize: 2 },
        "2xl": { fontSize: 2.4 },
        "3xl": { fontSize: 3 }
      }
      
      config.emojiBySize = {
        "4": { fontSize: 0.4 },
        "5": { fontSize: 0.5 },
        "6": { fontSize: 0.8 },
        "8": { fontSize: 1.1 },
        "10": { fontSize: 1.3 },
        "12": { fontSize: 1.7 },
        "xs": { fontSize: 0.8 },
        "sm": { fontSize: 1.1 },
        "md": { fontSize: 1.3 },
        "lg": { fontSize: 1.7 },
        "xl": { fontSize: 2.4 },
        "2xl": { fontSize: 2.9 },
        "3xl": { fontSize: 3.6 }
      }
      
      config.heroIconBySize = {
        "4": { size: 0.5 },
        "5": { size: 0.6 },
        "6": { size: 0.8 },
        "8": { size: 1.1 },
        "10": { size: 1.4 },
        "12": { size: 1.8 },
        "xs": { size: 0.8 },
        "sm": { size: 1.1 },
        "md": { size: 1.4 },
        "lg": { size: 1.8 },
        "xl": { size: 2.3 },
        "2xl": { size: 2.8 },
        "3xl": { size: 3.6 }
      }
      
      config.iconBySize = {
        "4": { size: 0.5 },
        "5": { size: 0.6 },
        "6": { size: 0.8 },
        "8": { size: 1 },
        "10": { size: 1.3 },
        "12": { size: 1.8 },
        "xs": { size: 0.8 },
        "sm": { size: 1 },
        "md": { size: 1.3 },
        "lg": { size: 1.8 },
        "xl": { size: 2.2 },
        "2xl": { size: 2.6 },
        "3xl": { size: 3.4 }
      }
    }
    
    const clearCustomConfigs = () => {
      config.textCJKBySize = {}
      config.textLatinBySize = {}
      config.emojiBySize = {}
      config.heroIconBySize = {}
      config.iconBySize = {}
    }
    
    // 勾選功能
    const toggleAllSizes = (type, checked) => {
      allSizes.forEach(size => {
        selectedSizes[type][size] = checked
      })
    }
    
    const isAllSelected = (type) => {
      return allSizes.every(size => selectedSizes[type][size])
    }
    
    const isAnySelected = (type) => {
      return allSizes.some(size => selectedSizes[type][size])
    }
    
    // 同步功能
    const syncSelectedSizes = (type) => {
      const selectedSizesList = allSizes.filter(size => selectedSizes[type][size])
      
      if (selectedSizesList.length === 0) {
        alert('請先勾選要同步的尺寸')
        return
      }
      
      let baseValue
      let configBySize
      let globalKey
      
      switch (type) {
        case 'textCJK':
          baseValue = config.global.textCJK.fontSize
          configBySize = config.textCJKBySize
          globalKey = 'textCJK'
          break
        case 'textLatin':
          baseValue = config.global.textLatin.fontSize
          configBySize = config.textLatinBySize
          globalKey = 'textLatin'
          break
        case 'emoji':
          baseValue = config.global.emoji.fontSize
          configBySize = config.emojiBySize
          globalKey = 'emoji'
          break
        case 'heroIcon':
          baseValue = config.global.icon.size
          configBySize = config.heroIconBySize
          globalKey = 'icon'
          break
        case 'bootstrapIcon':
          baseValue = config.global.icon.size
          configBySize = config.iconBySize
          globalKey = 'icon'
          break
      }
      
      selectedSizesList.forEach(size => {
        if (!configBySize[size]) {
          configBySize[size] = {}
        }
        if (type === 'textCJK' || type === 'textLatin') {
          configBySize[size].fontSize = baseValue
        } else {
          configBySize[size].size = baseValue
        }
      })
      
      console.log(`已同步 ${selectedSizesList.length} 個尺寸到全域基礎設定值: ${baseValue}`)
    }
    
    const exportConfig = () => {
      const exportData = {
        timestamp: new Date().toISOString(),
        baseContainer: '80x80px',
        
        // 全域設定
        global: { ...config.global },
        
        // 各尺寸特定設定
        textCJKBySize: { ...config.textCJKBySize },
        textLatinBySize: { ...config.textLatinBySize },
        emojiBySize: { ...config.emojiBySize },
        heroIconBySize: { ...config.heroIconBySize },
        iconBySize: { ...config.iconBySize },
        
        // 計算結果
        computed: {}
      }
      
      // 計算所有尺寸的最終值
      allSizes.forEach(size => {
        exportData.computed[size] = {
          textCJK: getComputedConfig(size, 'textCJK'),
          textLatin: getComputedConfig(size, 'textLatin'),
          emoji: getComputedConfig(size, 'emoji'),
          bootstrap_icon: getComputedConfig(size, 'bootstrap_icon'),
          hero_icon: getComputedConfig(size, 'hero_icon')
        }
      })
      
      console.log('=== Icon 尺寸配置 ===')
      console.log(JSON.stringify(exportData, null, 2))
      alert('配置已匯出到 Console，請檢查瀏覽器開發者工具')
    }
    
    onMounted(() => {
      loadData()
    })
    
    return {
      loading,
      users,
      organizations,
      teams,
      allSizes,
      config,
      selectedSizes,
      cjkTextExamples,
      latinTextExamples,
      emojiExamples,
      heroExamples,
      bsExamples,
      imageExample,
      getTextCJKConfig,
      getTextLatinConfig,
      getEmojiConfig,
      getHeroIconConfig,
      getIconConfig,
      updateTextCJKConfig,
      updateTextLatinConfig,
      updateEmojiConfig,
      updateHeroIconConfig,
      updateIconConfig,
      getComputedConfig,
      resetToDefaults,
      clearCustomConfigs,
      exportConfig,
      toggleAllSizes,
      isAllSelected,
      isAnySelected,
      syncSelectedSizes
    }
  }
}
</script>