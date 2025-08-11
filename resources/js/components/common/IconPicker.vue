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
        :is="selectedIcon" 
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
          <div class="flex space-x-1 bg-gray-100 rounded-md p-1">
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
        </div>

        <!-- 搜尋欄位 -->
        <div class="mb-4">
          <div class="relative">
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
        </div>

        <!-- 圖標內容區域 -->
        <div class="border border-gray-100 rounded-md bg-gray-50">
          <!-- Heroicons 標籤頁 -->
          <div v-if="activeTab === 'heroicons'" class="grid grid-cols-6 gap-2 p-2 h-48 overflow-y-auto">
            <button
              v-for="icon in filteredHeroicons"
              :key="icon.name"
              @click="selectIcon(icon.component, 'heroicons')"
              :class="selectedIcon === icon.component ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
              class="p-2 rounded border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
              :title="icon.name"
            >
              <component :is="icon.component" class="w-5 h-5 mx-auto text-gray-600" />
            </button>
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
                <button
                  v-for="emoji in items"
                  :key="emoji.name"
                  @click="selectIcon(emoji.emoji, 'emoji')"
                  :class="selectedIcon === emoji.emoji ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
                  class="p-2 rounded border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                  :title="emoji.name"
                >
                  <span class="w-5 h-5 flex items-center justify-center" style="font-size: 1.25rem; line-height: 1;">{{ emoji.emoji }}</span>
                </button>
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
import { bootstrapIcons, emojis } from '../../utils/iconSets.js'
import VirtualScroll from './VirtualScroll.vue'
// Heroicons imports
import { 
  HomeIcon, 
  UserIcon, 
  CogIcon, 
  DocumentIcon, 
  FolderIcon, 
  HeartIcon, 
  StarIcon, 
  BellIcon, 
  ChatIcon, 
  PlusIcon, 
  MinusIcon, 
  XIcon,
  MailIcon,
  PhoneIcon,
  CalendarIcon,
  ClockIcon,
  SearchIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  DownloadIcon,
  UploadIcon,
  ShareIcon,
  BookmarkIcon,
  FlagIcon,
  GiftIcon,
  LightBulbIcon,
  FireIcon,
  ShieldCheckIcon,
  ExclamationIcon
} from '@heroicons/vue/outline'

export default {
  name: 'IconPicker',
  components: {
    VirtualScroll,
    HomeIcon, UserIcon, CogIcon, DocumentIcon, FolderIcon, HeartIcon, StarIcon, BellIcon, ChatIcon, PlusIcon, MinusIcon, XIcon,
    MailIcon, PhoneIcon, CalendarIcon, ClockIcon, SearchIcon, EyeIcon, PencilIcon, TrashIcon, DownloadIcon, UploadIcon,
    ShareIcon, BookmarkIcon, FlagIcon, GiftIcon, LightBulbIcon, FireIcon, ShieldCheckIcon, ExclamationIcon
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
  emits: ['update:modelValue', 'update:iconType'],
  setup(props, { emit }) {
    const isOpen = ref(false)
    const iconPanel = ref(null)
    const iconPickerRef = ref(null)
    const searchQuery = ref('')
    const activeTab = ref('heroicons')
    const panelPosition = ref({ top: '0px', left: '0px' })
    const selectedIcon = ref(props.modelValue)
    const iconType = ref(props.iconType || 'heroicons')
    
    // 監聽 props 變化
    watch(() => props.modelValue, (newVal) => {
      selectedIcon.value = newVal
    })
    
    watch(() => props.iconType, (newVal) => {
      if (newVal) {
        iconType.value = newVal
      }
    })
    
    // Heroicons 圖標清單
    const heroicons = [
      { name: '首頁 Home', component: 'HomeIcon' },
      { name: '使用者 User', component: 'UserIcon' },
      { name: '設定 Settings', component: 'CogIcon' },
      { name: '文件 Document', component: 'DocumentIcon' },
      { name: '資料夾 Folder', component: 'FolderIcon' },
      { name: '愛心 Heart', component: 'HeartIcon' },
      { name: '星星 Star', component: 'StarIcon' },
      { name: '鈴鐺 Bell', component: 'BellIcon' },
      { name: '聊天 Chat', component: 'ChatIcon' },
      { name: '加號 Plus', component: 'PlusIcon' },
      { name: '減號 Minus', component: 'MinusIcon' },
      { name: '關閉 Close', component: 'XIcon' },
      { name: '信件 Mail', component: 'MailIcon' },
      { name: '電話 Phone', component: 'PhoneIcon' },
      { name: '日曆 Calendar', component: 'CalendarIcon' },
      { name: '時鐘 Clock', component: 'ClockIcon' },
      { name: '搜尋 Search', component: 'SearchIcon' },
      { name: '眼睛 Eye', component: 'EyeIcon' },
      { name: '編輯 Pencil', component: 'PencilIcon' },
      { name: '刪除 Trash', component: 'TrashIcon' },
      { name: '下載 Download', component: 'DownloadIcon' },
      { name: '上傳 Upload', component: 'UploadIcon' },
      { name: '分享 Share', component: 'ShareIcon' },
      { name: '書籤 Bookmark', component: 'BookmarkIcon' },
      { name: '旗幟 Flag', component: 'FlagIcon' },
      { name: '禮物 Gift', component: 'GiftIcon' },
      { name: '燈泡 Light Bulb', component: 'LightBulbIcon' },
      { name: '火焰 Fire', component: 'FireIcon' },
      { name: '盾牌 Shield Check', component: 'ShieldCheckIcon' },
      { name: '驚嘆號 Exclamation', component: 'ExclamationIcon' }
    ]
    
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
      selectedIcon.value = icon
      iconType.value = type
      emit('update:modelValue', icon)
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
    
    // 篩選後的圖標列表
    const filteredHeroicons = computed(() => {
      if (!searchQuery.value) return heroicons
      const query = searchQuery.value.toLowerCase()
      return heroicons.filter(icon => 
        icon.name.toLowerCase().includes(query)
      )
    })
    
    const filteredBootstrapIcons = computed(() => {
      if (!searchQuery.value) return bootstrapIcons
      const query = searchQuery.value.toLowerCase()
      return bootstrapIcons.filter(icon => 
        icon.name.toLowerCase().includes(query)
      )
    })
    
    const filteredEmojis = computed(() => {
      if (!searchQuery.value) return emojis
      const query = searchQuery.value.toLowerCase()
      return emojis.filter(emoji => 
        emoji.name.toLowerCase().includes(query)
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
      filteredHeroicons,
      filteredBootstrapIcons,
      filteredEmojis,
      isSearchEmpty,
      togglePicker,
      closePicker,
      selectIcon,
      clearIcon,
      clearSearch
    }
  }
}
</script>

<style scoped>
.icon-picker {
  @apply relative inline-block;
}
</style>