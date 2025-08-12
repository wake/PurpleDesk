<template>
  <div class="icon-picker" ref="iconPickerRef">
    <!-- 圖標預覽按鈕 -->
    <button
      v-if="!hidePreview"
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
      <span v-else-if="selectedIcon && iconType === 'initials'" class="text-xs font-semibold text-gray-600">
        {{ selectedIcon }}
      </span>
      <img 
        v-else-if="selectedIcon && iconType === 'upload'" 
        :src="selectedIcon"
        class="w-full h-full object-cover rounded"
      />
      <span v-else class="text-gray-400 text-xs">圖標</span>
    </button>
    
    <!-- 圖標選擇面板 -->
    <Teleport to="body">
      <div 
        v-if="isOpen" 
        ref="iconPanel"
        class="fixed z-[9999] px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-xl w-96"
        :style="panelPosition"
        @click.stop
      >
        <!-- 頂部標籤切換 -->
        <div class="flex border-b border-gray-200 mb-4">
          <button
            @click="activeTab = 'initials'"
            :class="activeTab === 'initials' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700'"
            class="px-2 me-3 pt-1 pb-2 text-sm font-medium transition-colors"
          >
            字母
          </button>
          <button
            @click="activeTab = 'emoji'"
            :class="activeTab === 'emoji' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700'"
            class="px-2 me-3 pt-1 pb-2 text-sm font-medium transition-colors"
          >
            Emoji
          </button>
          <button
            @click="activeTab = 'icons'"
            :class="activeTab === 'icons' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700'"
            class="px-2 me-3 pt-1 pb-2 text-sm font-medium transition-colors"
          >
            Icons
          </button>
          <button
            @click="activeTab = 'upload'"
            :class="activeTab === 'upload' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700'"
            class="px-2 me-3 pt-1 pb-2 text-sm font-medium transition-colors"
          >
            Upload
          </button>
          <div class="ml-auto flex items-center">
            <!-- 背景顏色選擇器按鈕 -->
            <button
              @click="openColorPicker"
              class="p-0 me-3 pt-1 pb-2 text-sm transition-colors"
              :style="{ color: backgroundColor || '#6366f1' }"
              title="選擇背景顏色"
            >
              <i class="bi bi-eyedropper"></i>
            </button>
            <!-- Reset Icon 按鈕 -->
            <button
              @click="clearIcon"
              :disabled="!selectedIcon"
              :class="selectedIcon ? 'text-gray-500 hover:text-gray-700' : 'text-gray-400 cursor-not-allowed'"
              class="p-0 me-3 pt-1 pb-2 text-sm transition-colors"
              title="Reset Icon"
            >
              <i class="bi bi-arrow-counterclockwise"></i>
            </button>
          </div>
        </div>

        <!-- 搜尋與選擇器區域 -->
        <div v-if="activeTab !== 'upload' && activeTab !== 'initials'" class="mb-4">
          <div class="flex space-x-2">
            <!-- 搜尋欄位 -->
            <div class="relative flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Filter..."
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
            <!-- 功能按鈕組 -->
            <div class="flex space-x-2">
              <!-- Heroicon 樣式選擇器 -->
              <HeroiconStyleSelector
                v-if="activeTab === 'icons'"
                v-model="selectedHeroiconStyle"
                @update:modelValue="handleHeroiconStyleChange"
              />
              <!-- 膚色選擇器 -->
              <SkinToneSelector
                v-if="activeTab === 'emoji'"
                v-model="selectedSkinTone"
                @update:modelValue="handleSkinToneChange"
              />
            </div>
          </div>
        </div>

        <!-- 內容區域 -->
        <div>
          <!-- 字母標籤頁 -->
          <div 
            v-if="activeTab === 'initials'"
            class="space-y-4"
          >
            <!-- 字母輸入區 -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">輸入字母或文字</label>
              <input
                v-model="customInitials"
                type="text"
                maxlength="3"
                placeholder="最多3個字元 (如: AB)"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                @input="handleInitialsInput"
              />
              <p class="mt-1 text-xs text-gray-500">輸入 1-3 個字元作為圖標顯示</p>
            </div>
            
            <!-- 預覽區 -->
            <div class="flex items-center justify-center py-4">
              <div 
                class="w-24 h-24 rounded-full flex items-center justify-center text-white font-semibold text-3xl"
                :style="{ backgroundColor: backgroundColor || '#6366f1' }"
              >
                {{ customInitials || 'AB' }}
              </div>
            </div>
            
            <!-- 應用按鈕 -->
            <button
              @click="applyInitials"
              :disabled="!customInitials"
              :class="customInitials ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
              class="w-full py-2 px-4 rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              應用字母
            </button>
          </div>
          
          <!-- Emoji 標籤頁 -->
          <div 
            v-if="activeTab === 'emoji'"
          >
            <!-- Emoji 網格 -->
            <div class="h-48 border border-gray-100 rounded-md bg-gray-50 p-2">
              <VirtualScroll
                :items="filteredEmojis"
                :items-per-row="10"
                :row-height="36"
                :container-height="176"
                :buffer="2"
              >
                <template #row="{ items }">
                  <template v-for="emoji in items" :key="emoji ? emoji.name : Math.random()">
                    <button
                      v-if="emoji"
                      @click="selectIcon(getEmojiWithSkinTone(emoji), 'emoji')"
                      :class="selectedIcon === getEmojiWithSkinTone(emoji) ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-100'"
                      class="emoji-button p-1 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                      :title="emoji.name"
                    >
                      <span class="text-xl">{{ getEmojiWithSkinTone(emoji) }}</span>
                    </button>
                    <div v-else class="p-1"></div>
                  </template>
                </template>
              </VirtualScroll>
            </div>
          </div>

          <!-- Icons 標籤頁 (只有 Heroicons) -->
          <div 
            v-else-if="activeTab === 'icons'"
          >
            <!-- 圖標網格 -->
            <div class="h-48 border border-gray-100 rounded-md bg-gray-50 p-2">
              <VirtualScroll
                :items="filteredHeroicons"
                :items-per-row="10"
                :row-height="36"
                :container-height="176"
                :buffer="2"
              >
                <template #row="{ items }">
                  <button
                    v-for="icon in items"
                    :key="icon.name"
                    @click="selectIcon(icon.component, 'heroicons')"
                    :class="isIconSelected(icon.component) ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-100'"
                    class="icon-button p-1.5 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                    :title="icon.name"
                  >
                    <component 
                      :is="getHeroiconComponent(icon.component)" 
                      class="w-5 h-5 mx-auto text-gray-600" 
                    />
                  </button>
                </template>
              </VirtualScroll>
            </div>
          </div>
          
          <!-- Upload 標籤頁 -->
          <div 
            v-else-if="activeTab === 'upload'"
            @click="triggerFileUpload"
            @dragover.prevent="handleDragOver"
            @dragleave.prevent="handleDragLeave"
            @drop.prevent="handleDrop"
            :class="isDragging ? 'border-primary-400 bg-primary-50' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'"
            class="h-48 flex flex-col items-center justify-center border-2 border-dashed rounded-md transition-colors cursor-pointer"
          >
            <div class="text-center pointer-events-none space-y-3">
              <i class="bi bi-cloud-arrow-up-fill text-4xl text-gray-400"></i>
              <div>
                <p class="text-sm font-medium text-gray-700">Upload an image</p>
                <p class="text-xs text-gray-500 mt-1">or drag and drop</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 搜尋結果為空的提示 -->
        <div v-if="isSearchEmpty" class="text-center py-8 text-gray-500">
          <p class="text-sm">找不到符合的圖標</p>
          <p class="text-xs text-gray-400 mt-1">請嘗試其他關鍵字</p>
        </div>
      </div>
    </Teleport>
    
    <!-- 隱藏的檔案輸入 -->
    <input
      ref="fileInput"
      type="file"
      accept="image/*"
      @change="handleFileUpload"
      class="hidden"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { bootstrapIcons, emojis } from '../../utils/iconSets.js'
import { applySkinTone, supportsSkinTone, removeSkinTone, getCurrentSkinTone } from '../../utils/emojiSkinTone.js'
import heroiconsOutline from '../../utils/heroicons/allHeroicons.js'
import VirtualScroll from './VirtualScroll.vue'
import SkinToneSelector from './SkinToneSelector.vue'
import HeroiconStyleSelector from './HeroiconStyleSelector.vue'
// 動態導入所有 Heroicons
import * as HeroiconsOutline from '@heroicons/vue/outline'
import * as HeroiconsSolid from '@heroicons/vue/solid'

export default {
  name: 'IconPicker',
  components: {
    VirtualScroll,
    SkinToneSelector,
    HeroiconStyleSelector,
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
    },
    backgroundColor: {
      type: String,
      default: '#6366f1'
    },
    hidePreview: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue', 'update:iconType', 'file-selected', 'color-picker-click', 'close'],
  setup(props, { emit }) {
    const isOpen = ref(false)
    const iconPanel = ref(null)
    const iconPickerRef = ref(null)
    const searchQuery = ref('')
    const activeTab = ref('initials') // 預設為字母頁簽
    const panelPosition = ref({ top: '0px', left: '0px' })
    const selectedIcon = ref(props.modelValue)
    const iconType = ref(props.iconType || '')
    const emojisLoaded = ref(false)
    const selectedSkinTone = ref('') // 預設膚色
    const selectedHeroiconStyle = ref('outline') // 預設 Heroicon 樣式
    const fileInput = ref(null)
    const uploadedImage = ref(null)
    const isDragging = ref(false)
    const backgroundColor = ref(props.backgroundColor || '#6366f1')
    const customInitials = ref('') // 字母模式的輸入值
    
    // 監聽 props 變化
    watch(() => props.backgroundColor, (newVal) => {
      if (newVal) {
        backgroundColor.value = newVal
      }
    })
    
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
    
    const calculatePosition = () => {
      let targetElement = iconPickerRef.value
      
      // 如果 hidePreview 為 true，嘗試找到父元素作為定位參考
      if (props.hidePreview && iconPickerRef.value) {
        // 尋找最近的有實際尺寸的父元素（通常是 ImageSelector 的預覽區域）
        let parent = iconPickerRef.value.parentElement
        while (parent) {
          const rect = parent.getBoundingClientRect()
          if (rect.width > 0 && rect.height > 0) {
            targetElement = parent
            break
          }
          parent = parent.parentElement
        }
      }
      
      if (!targetElement) return
      
      const rect = targetElement.getBoundingClientRect()
      const viewportHeight = window.innerHeight
      const viewportWidth = window.innerWidth
      
      // 彈窗預設尺寸（調整為 384px = w-96）
      const panelWidth = 384
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
        if (iconType.value === 'initials') {
          activeTab.value = 'initials'
          // 如果有選中的字母，設定到輸入框
          if (selectedIcon.value) {
            customInitials.value = selectedIcon.value
          }
        } else if (iconType.value === 'heroicons') {
          activeTab.value = 'icons'
        } else if (iconType.value === 'upload') {
          activeTab.value = 'upload'
        } else if (iconType.value === 'emoji') {
          activeTab.value = 'emoji'
          // 如果當前選中的是 emoji，檢測它的膚色
          if (selectedIcon.value) {
            const detectedSkinTone = getCurrentSkinTone(selectedIcon.value)
            selectedSkinTone.value = detectedSkinTone
          }
        } else {
          // 預設顯示字母頁簽
          activeTab.value = 'initials'
        }
        await nextTick()
        calculatePosition()
      }
    }
    
    const closePicker = () => {
      isOpen.value = false
      emit('close')
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
      
      // 直接應用選擇的圖標
      emit('update:modelValue', iconValue)
      emit('update:iconType', type)
      closePicker()
    }
    
    const clearIcon = () => {
      if (!selectedIcon.value) return // 如果沒有圖標，不執行任何操作
      
      selectedIcon.value = ''
      iconType.value = ''
      uploadedImage.value = null
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
      processFile(file)
    }
    
    // 處理檔案處理邏輯
    const processFile = (file) => {
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
      
      // 創建預覽 URL
      const reader = new FileReader()
      reader.onload = (e) => {
        uploadedImage.value = e.target.result
        selectedIcon.value = e.target.result
        iconType.value = 'upload'
        
        // 直接應用上傳的圖片
        emit('update:modelValue', e.target.result)
        emit('update:iconType', 'upload')
        emit('file-selected', file)
        closePicker()
      }
      reader.readAsDataURL(file)
      
      // 清空輸入以便下次使用
      if (fileInput.value) {
        fileInput.value.value = ''
      }
    }
    
    // 處理拖曳進入
    const handleDragOver = () => {
      isDragging.value = true
    }
    
    // 處理拖曳離開
    const handleDragLeave = () => {
      isDragging.value = false
    }
    
    // 處理拖放
    const handleDrop = (event) => {
      isDragging.value = false
      
      const files = event.dataTransfer?.files
      if (files && files.length > 0) {
        processFile(files[0])
      }
    }
    
    // 開啟顏色選擇器（通知父組件）
    const openColorPicker = () => {
      emit('color-picker-click')
    }
    
    // 處理字母輸入
    const handleInitialsInput = () => {
      // 限制為3個字元，自動大寫
      if (customInitials.value) {
        customInitials.value = customInitials.value.toUpperCase().slice(0, 3)
      }
    }
    
    // 應用字母作為圖標
    const applyInitials = () => {
      if (!customInitials.value) return
      
      selectedIcon.value = customInitials.value
      iconType.value = 'initials'
      
      // 發送更新
      emit('update:modelValue', customInitials.value)
      emit('update:iconType', 'initials')
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
      
      if (activeTab.value === 'icons') {
        return filteredHeroicons.value.length === 0
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
      calculatePosition,
      selectedIcon,
      iconType,
      heroicons,
      emojis: emojis,
      emojisLoaded,
      filteredHeroicons,
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
      fileInput,
      triggerFileUpload,
      handleFileUpload,
      uploadedImage,
      isDragging,
      handleDragOver,
      handleDragLeave,
      handleDrop,
      backgroundColor,
      openColorPicker,
      customInitials,
      handleInitialsInput,
      applyInitials,
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
.emoji-button,
.icon-button {
  width: 30px;
  height: 30px;
}
</style>