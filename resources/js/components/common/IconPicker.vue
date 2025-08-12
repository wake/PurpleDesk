<template>
  <div class="icon-picker" ref="iconPickerRef">
    <!-- 圖標預覽按鈕 -->
    <button
      type="button"
      @click="togglePicker"
      class="w-8 h-8 rounded border-2 border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors bg-white flex items-center justify-center"
    >
      <!-- 顯示選中的圖標 -->
      <component 
        v-if="selectedIcon && iconType === 'heroicons'" 
        :is="getDisplayIcon(selectedIcon)" 
        class="w-5 h-5 text-gray-600" 
      />
      <i 
        v-else-if="selectedIcon && iconType === 'bootstrap'" 
        :class="['bi', selectedIcon]"
        class="text-gray-600 text-sm"
      />
      <span v-else-if="selectedIcon && iconType === 'emoji'" class="text-sm">
        {{ selectedIcon }}
      </span>
      <span v-else class="text-gray-400 text-xs">圖標</span>
    </button>
    
    <!-- 圖標選擇面板 -->
    <Teleport to="body">
      <div 
        v-if="isOpen" 
        ref="iconPanel"
        class="fixed z-[9999] p-4 bg-white border border-gray-200 rounded-lg shadow-xl w-80"
        :style="panelPosition"
        @click.stop
      >
        <!-- 標籤頁 -->
        <div class="mb-4">
          <div class="flex items-center space-x-2">
            <div class="flex-1 flex space-x-1 bg-gray-100 rounded-md p-1">
              <button
                @click="activeTab = 'heroicons'"
                :class="activeTab === 'heroicons' ? 'bg-white shadow-sm' : 'hover:bg-gray-50'"
                class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 rounded transition-colors"
              >
                Heroicons
              </button>
              <button
                @click="activeTab = 'bootstrap'"
                :class="activeTab === 'bootstrap' ? 'bg-white shadow-sm' : 'hover:bg-gray-50'"
                class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 rounded transition-colors"
              >
                Bootstrap
              </button>
              <button
                @click="activeTab = 'emoji'"
                :class="activeTab === 'emoji' ? 'bg-white shadow-sm' : 'hover:bg-gray-50'"
                class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 rounded transition-colors"
              >
                表情符號
              </button>
            </div>
            <!-- 上傳按鈕 -->
            <button
              @click="triggerFileUpload"
              class="p-2 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
              title="上傳圖片"
            >
              <CloudUploadIcon class="w-5 h-5 text-gray-600" />
            </button>
            <!-- 隱藏的檔案輸入 -->
            <input
              ref="fileInput"
              type="file"
              accept="image/*"
              @change="handleFileUpload"
              class="hidden"
            />
          </div>
        </div>

        <!-- 搜尋與選擇器區域 -->
        <div class="mb-4">
          <div class="flex space-x-2">
            <!-- 搜尋欄位 -->
            <div class="relative flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="搜尋圖標名稱..."
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
              <button
                v-if="searchQuery"
                @click="clearSearch"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 hover:text-gray-600"
              >
                ×
              </button>
            </div>
            <!-- Heroicon 樣式選擇器（僅在 heroicons 標籤頁顯示） -->
            <HeroiconStyleSelector
              v-if="activeTab === 'heroicons'"
              v-model="selectedHeroiconStyle"
              @update:modelValue="handleHeroiconStyleChange"
            />
            <!-- Bootstrap Icons 變體選擇器（僅在 bootstrap 標籤頁顯示） -->
            <BSIconVariantSelector
              v-if="activeTab === 'bootstrap'"
              v-model="selectedBSVariant"
              @update:modelValue="handleBSVariantChange"
            />
            <!-- 膚色選擇器（僅在 emoji 標籤頁顯示） -->
            <SkinToneSelector
              v-if="activeTab === 'emoji'"
              v-model="selectedSkinTone"
              @update:modelValue="handleSkinToneChange"
            />
          </div>
        </div>

        <!-- 圖標內容區域 -->
        <div class="border border-gray-100 rounded-md bg-gray-50">
          <!-- Heroicons 標籤頁 (使用虛擬滾動) -->
          <div 
            v-if="activeTab === 'heroicons'"
            class="h-48 overflow-y-auto"
          >
            <VirtualScroll
              :items="filteredHeroicons"
              :items-per-row="6"
              :row-height="40"
              :container-height="192"
              :buffer="2"
            >
              <template #row="{ items }">
                <button
                  v-for="icon in items"
                  :key="icon.name"
                  @click="selectIcon(icon.component, 'heroicons')"
                  :class="isIconSelected(icon.component) ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
                  class="p-2 rounded border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                  :title="icon.name"
                >
                  <component :is="getHeroiconComponent(icon.component)" class="w-5 h-5 mx-auto text-gray-600" />
                </button>
              </template>
            </VirtualScroll>
          </div>

          <!-- Bootstrap Icons 標籤頁 -->
          <div 
            v-else-if="activeTab === 'bootstrap'"
            class="h-48 overflow-y-auto"
          >
            <VirtualScroll
              :items="filteredBootstrapIcons"
              :items-per-row="6"
              :row-height="40"
              :container-height="192"
              :buffer="2"
            >
              <template #row="{ items }">
                <button
                  v-for="icon in items"
                  :key="icon.name"
                  @click="selectIcon(icon.class, 'bootstrap')"
                  :class="selectedIcon === icon.class ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
                  class="p-2 rounded border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                  :title="icon.name"
                >
                  <i :class="`bi ${icon.class} text-gray-600`" class="w-5 h-5 flex items-center justify-center" style="font-size: 1.25rem; line-height: 1;"></i>
                </button>
              </template>
            </VirtualScroll>
          </div>

          <!-- 表情符號標籤頁 -->
          <div 
            v-else-if="activeTab === 'emoji'"
            class="h-48 overflow-y-auto"
          >
            <VirtualScroll
              :items="filteredEmojis"
              :items-per-row="6"
              :row-height="40"
              :container-height="192"
              :buffer="2"
            >
              <template #row="{ items }">
                <template v-for="emoji in items" :key="emoji ? emoji.name : Math.random()">
                  <button
                    v-if="emoji"
                    @click="selectIcon(getEmojiWithSkinTone(emoji), 'emoji')"
                    :class="selectedIcon === getEmojiWithSkinTone(emoji) ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
                    class="p-2 rounded border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                    :title="emoji.name"
                  >
                    <span class="w-5 h-5 flex items-center justify-center" style="font-size: 1.25rem; line-height: 1;">{{ getEmojiWithSkinTone(emoji) }}</span>
                  </button>
                  <div v-else class="p-2"></div>
                </template>
              </template>
            </VirtualScroll>
          </div>
        </div>

        <!-- 搜尋結果為空的提示 -->
        <div v-if="isSearchEmpty" class="text-center py-8 text-gray-500">
          <p class="text-sm">找不到符合的圖標</p>
          <p class="text-xs text-gray-400 mt-1">請嘗試其他關鍵字</p>
        </div>

        <!-- 底部按鈕 -->
        <div class="flex space-x-2 mt-4 pt-3 border-t border-gray-200">
          <button
            @click="clearIcon"
            class="flex-1 px-3 py-2 text-sm bg-red-50 hover:bg-red-100 text-red-600 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-red-500"
          >
            清除圖標
          </button>
          <button
            @click="closePicker"
            class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
            取消
          </button>
        </div>
      </div>
    </Teleport>
    
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { CloudUploadIcon } from '@heroicons/vue/outline'
import { bootstrapIcons, emojis } from '../../utils/iconSets.js'
import { applySkinTone, supportsSkinTone, removeSkinTone, getCurrentSkinTone } from '../../utils/emojiSkinTone.js'
import heroiconsOutline from '../../utils/heroicons/allHeroicons.js'
import VirtualScroll from './VirtualScroll.vue'
import SkinToneSelector from './SkinToneSelector.vue'
import HeroiconStyleSelector from './HeroiconStyleSelector.vue'
import BSIconVariantSelector from './BSIconVariantSelector.vue'
// 動態導入所有 Heroicons
import * as HeroiconsOutline from '@heroicons/vue/outline'
import * as HeroiconsSolid from '@heroicons/vue/solid'

export default {
  name: 'IconPicker',
  components: {
    VirtualScroll,
    SkinToneSelector,
    HeroiconStyleSelector,
    BSIconVariantSelector,
    CloudUploadIcon,
    // 註冊所有 Heroicons (Outline 和 Solid)
    ...HeroiconsOutline,
    ...HeroiconsSolid
  },
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    iconType: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue', 'update:iconType', 'file-selected'],
  setup(props, { emit }) {
    const isOpen = ref(false)
    const iconPanel = ref(null)
    const iconPickerRef = ref(null)
    const searchQuery = ref('')
    const activeTab = ref('heroicons')
    const panelPosition = ref({ top: '0px', left: '0px' })
    const selectedIcon = ref(props.modelValue)
    const iconType = ref(props.iconType || 'heroicons')
    const emojisLoaded = ref(false)
    const selectedSkinTone = ref('') // 預設膚色
    const selectedHeroiconStyle = ref('outline') // 預設 Heroicon 樣式
    const selectedBSVariant = ref('auto') // 預設 Bootstrap Icons 變體
    const fileInput = ref(null)
    
    // 監聽 props 變化
    watch(() => props.modelValue, (newVal) => {
      selectedIcon.value = newVal
      // 如果是 emoji，檢測膚色
      if (newVal && props.iconType === 'emoji') {
        const detectedSkinTone = getCurrentSkinTone(newVal)
        selectedSkinTone.value = detectedSkinTone
      }
      // 如果是 heroicons，檢測樣式
      if (newVal && props.iconType === 'heroicons' && newVal.includes(':')) {
        const [style, iconName] = newVal.split(':')
        if (style === 'solid' || style === 'outline') {
          selectedHeroiconStyle.value = style
          selectedIcon.value = newVal
        }
      }
    })
    
    watch(() => props.iconType, (newVal) => {
      if (newVal) {
        iconType.value = newVal
      }
    })
    
    // 使用完整的 Heroicons 圖標清單 (230個圖標)
    const heroicons = heroiconsOutline
    
    // 儲存 Heroicons 組件的引用
    const HeroiconsComponents = {
      outline: HeroiconsOutline,
      solid: HeroiconsSolid
    }
    
    // 使用完整的圖標清單
    // bootstrapIcons 和 emojis 從 iconSets.js 導入
    
    const calculatePosition = () => {
      if (!iconPickerRef.value) return
      
      const rect = iconPickerRef.value.getBoundingClientRect()
      const viewportHeight = window.innerHeight
      const viewportWidth = window.innerWidth
      
      // 彈窗預設尺寸
      const panelWidth = 320
      const panelHeight = 400
      
      let top = rect.bottom + 5
      let left = rect.left
      
      // 優先顯示在下方，只有在下方空間真的不足時才顯示在上方
      const spaceBelow = viewportHeight - rect.bottom
      const spaceAbove = rect.top
      
      if (spaceBelow < panelHeight && spaceAbove > spaceBelow) {
        // 只有當上方空間比下方多時才顯示在上方
        top = rect.top - panelHeight - 5
      } else if (spaceBelow < panelHeight) {
        // 如果下方空間不足但仍要顯示在下方，調整高度
        top = rect.bottom + 5
      }
      
      // 檢查是否超出視窗右邊
      if (left + panelWidth > viewportWidth) {
        left = viewportWidth - panelWidth - 10
      }
      
      // 檢查是否超出視窗左邊
      if (left < 10) {
        left = 10
      }
      
      panelPosition.value = {
        top: `${top}px`,
        left: `${left}px`
      }
    }
    
    const togglePicker = async () => {
      isOpen.value = !isOpen.value
      if (isOpen.value) {
        // 打開時根據當前 iconType 設定正確的標籤頁
        if (iconType.value === 'bootstrap') {
          activeTab.value = 'bootstrap'
        } else if (iconType.value === 'emoji') {
          activeTab.value = 'emoji'
          // 如果當前選中的是 emoji，檢測它的膚色
          if (selectedIcon.value) {
            const detectedSkinTone = getCurrentSkinTone(selectedIcon.value)
            selectedSkinTone.value = detectedSkinTone
          }
        } else {
          activeTab.value = 'heroicons'
        }
        await nextTick()
        calculatePosition()
      }
    }
    
    const closePicker = () => {
      isOpen.value = false
    }
    
    const selectIcon = (icon, type) => {
      // 如果是 Heroicons，儲存樣式資訊
      let iconValue = icon
      if (type === 'heroicons') {
        // 在圖標名稱前加上樣式前綴
        iconValue = `${selectedHeroiconStyle.value}:${icon}`
      }
      selectedIcon.value = iconValue
      iconType.value = type
      emit('update:modelValue', iconValue)
      emit('update:iconType', type)
      closePicker()
    }
    
    const clearIcon = () => {
      selectedIcon.value = ''
      iconType.value = ''
      emit('update:modelValue', '')
      emit('update:iconType', '')
      closePicker()
    }
    
    const clearSearch = () => {
      searchQuery.value = ''
    }
    
    // 處理膚色變更
    const handleSkinToneChange = (tone) => {
      selectedSkinTone.value = tone
    }
    
    // 獲取帶有膚色的 emoji
    const getEmojiWithSkinTone = (emojiData) => {
      if (!emojiData || !emojiData.emoji) return ''
      
      // 使用專門的膚色工具函數
      return applySkinTone(emojiData.emoji, selectedSkinTone.value)
    }
    
    // 獲取 Heroicon 組件（根據樣式選擇）
    const getHeroiconComponent = (componentName) => {
      const components = selectedHeroiconStyle.value === 'solid' 
        ? HeroiconsSolid 
        : HeroiconsOutline
      return components[componentName] || HeroiconsOutline[componentName]
    }
    
    // 處理 Heroicon 樣式變更
    const handleHeroiconStyleChange = (style) => {
      selectedHeroiconStyle.value = style
    }
    
    // 處理 Bootstrap Icons 變體變更
    const handleBSVariantChange = (variant) => {
      selectedBSVariant.value = variant
    }
    
    // 觸發檔案上傳
    const triggerFileUpload = () => {
      if (fileInput.value) {
        fileInput.value.click()
      }
    }
    
    // 處理檔案上傳
    const handleFileUpload = (event) => {
      const file = event.target.files?.[0]
      if (!file) return
      
      // 驗證檔案類型
      if (!file.type.startsWith('image/')) {
        alert('請選擇圖片檔案')
        return
      }
      
      // 驗證檔案大小 (2MB)
      const maxSize = 2 * 1024 * 1024
      if (file.size > maxSize) {
        alert('檔案大小不能超過 2MB')
        return
      }
      
      // 發送檔案給父組件
      emit('file-selected', file)
      
      // 清空輸入以便下次使用
      if (fileInput.value) {
        fileInput.value.value = ''
      }
      
      // 關閉選擇器
      closePicker()
    }
    
    // 當切換到 emoji 標籤頁時，檢測當前選中 emoji 的膚色
    watch(activeTab, (newTab) => {
      if (newTab === 'emoji' && selectedIcon.value && iconType.value === 'emoji') {
        const detectedSkinTone = getCurrentSkinTone(selectedIcon.value)
        selectedSkinTone.value = detectedSkinTone
      }
    })
    
    // 篩選後的圖標列表
    const filteredHeroicons = computed(() => {
      if (!searchQuery.value) return heroicons
      const query = searchQuery.value.toLowerCase()
      return heroicons.filter(icon => 
        icon.name.toLowerCase().includes(query)
      )
    })
    
    const filteredBootstrapIcons = computed(() => {
      let icons = bootstrapIcons
      
      // 根據選擇的變體過濾圖標
      if (selectedBSVariant.value === 'fill') {
        icons = icons.filter(icon => icon.class.endsWith('-fill'))
      } else if (selectedBSVariant.value === 'standard') {
        icons = icons.filter(icon => !icon.class.endsWith('-fill'))
      }
      // 'auto' 模式顯示所有圖標
      
      // 搜尋過濾
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        icons = icons.filter(icon => 
          icon.name.toLowerCase().includes(query)
        )
      }
      
      return icons
    })
    
    const filteredEmojis = computed(() => {
      // 確保 emojis 是陣列，處理 Proxy 情況
      const emojiArray = Array.isArray(emojis) ? emojis : []
      if (!searchQuery.value) return emojiArray
      const query = searchQuery.value.toLowerCase()
      return emojiArray.filter(emoji => 
        emoji && emoji.name && emoji.name.toLowerCase().includes(query)
      )
    })
    
    // 檢查搜尋結果是否為空
    const isSearchEmpty = computed(() => {
      if (!searchQuery.value) return false
      
      if (activeTab.value === 'heroicons') {
        return filteredHeroicons.value.length === 0
      } else if (activeTab.value === 'bootstrap') {
        return filteredBootstrapIcons.value.length === 0
      } else if (activeTab.value === 'emoji') {
        return filteredEmojis.value.length === 0
      }
      
      return false
    })
    
    // 點擊外部關閉
    const handleClickOutside = (event) => {
      if (iconPanel.value && !iconPanel.value.contains(event.target) &&
          iconPickerRef.value && !iconPickerRef.value.contains(event.target)) {
        closePicker()
      }
    }
    
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
      window.addEventListener('resize', () => {
        if (isOpen.value) calculatePosition()
      })
      window.addEventListener('scroll', () => {
        if (isOpen.value) calculatePosition()
      })
      
      // 觸發 emoji 載入（如果還沒載入）
      if (Array.isArray(emojis) && emojis.length === 0) {
        // Proxy 會自動觸發載入
        console.log('Triggering emoji loading...')
        // 存取 length 屬性會觸發載入
        const emojiCount = emojis.length
        // 一秒後強制更新
        setTimeout(() => {
          emojisLoaded.value = true
        }, 2000)
      }
    })
    
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
      window.removeEventListener('resize', calculatePosition)
      window.removeEventListener('scroll', calculatePosition)
    })
    
    return {
      isOpen,
      iconPanel,
      iconPickerRef,
      searchQuery,
      activeTab,
      panelPosition,
      selectedIcon,
      iconType,
      heroicons,
      bootstrapIcons: bootstrapIcons,
      emojis: emojis,
      emojisLoaded,
      filteredHeroicons,
      filteredBootstrapIcons,
      filteredEmojis,
      isSearchEmpty,
      togglePicker,
      closePicker,
      selectIcon,
      clearIcon,
      clearSearch,
      selectedSkinTone,
      handleSkinToneChange,
      getEmojiWithSkinTone,
      getHeroiconComponent,
      selectedHeroiconStyle,
      handleHeroiconStyleChange,
      selectedBSVariant,
      handleBSVariantChange,
      fileInput,
      triggerFileUpload,
      handleFileUpload,
      getDisplayIcon: (icon) => {
        // 如果圖標包含樣式前綴，移除它
        if (icon && icon.includes(':')) {
          return icon.split(':')[1]
        }
        return icon
      },
      isIconSelected: (iconComponent) => {
        // 檢查是否選中（忽略樣式前綴）
        const currentIcon = selectedIcon.value && selectedIcon.value.includes(':') 
          ? selectedIcon.value.split(':')[1] 
          : selectedIcon.value
        return currentIcon === iconComponent
      }
    }
  }
}
</script>

<style scoped>
.icon-picker {
  @apply relative inline-block;
}
</style>