<template>
  <div class="icon-picker" :class="{ 'icon-picker-open': isOpen }">
    <!-- 圖標預覽按鈕 -->
    <button
      type="button"
      @click="togglePicker"
      class="w-8 h-8 rounded border-2 border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors bg-white flex items-center justify-center"
    >
      <!-- 顯示選中的圖標 -->
      <component 
        v-if="selectedIcon && iconType !== 'emoji'" 
        :is="selectedIcon" 
        class="w-5 h-5 text-gray-600" 
      />
      <span v-else-if="selectedIcon && iconType === 'emoji'" class="text-sm">
        {{ selectedIcon }}
      </span>
      <span v-else class="text-gray-400 text-xs">圖標</span>
    </button>
    
    <!-- 圖標選擇面板 -->
    <div 
      v-if="isOpen" 
      ref="iconPanel"
      class="absolute z-50 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg w-80 max-h-96 overflow-hidden"
      @click.stop
    >
      <!-- 標籤切換 -->
      <div class="flex border-b border-gray-200">
        <button
          v-for="tab in iconTabs"
          :key="tab.key"
          @click="activeTab = tab.key"
          class="flex-1 px-4 py-2 text-sm font-medium transition-colors focus:outline-none"
          :class="activeTab === tab.key 
            ? 'text-primary-600 border-b-2 border-primary-500 bg-primary-50' 
            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
        >
          {{ tab.label }}
        </button>
      </div>
      
      <!-- 圖標內容區域 -->
      <div class="p-4 overflow-y-auto max-h-80">
        <!-- Heroicons -->
        <div v-if="activeTab === 'heroicons'" class="grid grid-cols-6 gap-2">
          <button
            v-for="icon in heroicons"
            :key="icon.name"
            @click="selectIcon(icon.component, 'heroicons')"
            class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
            :class="selectedIcon === icon.component ? 'border-primary-500 bg-primary-100' : ''"
            :title="icon.name"
          >
            <component :is="icon.component" class="w-5 h-5 text-gray-600" />
          </button>
        </div>
        
        <!-- Bootstrap Icons -->
        <div v-if="activeTab === 'bootstrap'" class="grid grid-cols-6 gap-2">
          <button
            v-for="icon in bootstrapIcons"
            :key="icon.name"
            @click="selectIcon(icon.class, 'bootstrap')"
            class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
            :class="selectedIcon === icon.class ? 'border-primary-500 bg-primary-100' : ''"
            :title="icon.name"
          >
            <i :class="icon.class" class="text-gray-600"></i>
          </button>
        </div>
        
        <!-- Emoji -->
        <div v-if="activeTab === 'emoji'" class="grid grid-cols-8 gap-2">
          <button
            v-for="emoji in emojis"
            :key="emoji.char"
            @click="selectIcon(emoji.char, 'emoji')"
            class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg"
            :class="selectedIcon === emoji.char ? 'border-primary-500 bg-primary-100' : ''"
            :title="emoji.name"
          >
            {{ emoji.char }}
          </button>
        </div>
      </div>
      
      <!-- 底部操作區 -->
      <div class="border-t border-gray-200 p-3 flex justify-between">
        <button
          @click="clearIcon"
          class="px-3 py-1 text-sm text-red-600 hover:bg-red-50 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-red-500"
        >
          清除圖標
        </button>
        <button
          @click="closePicker"
          class="px-4 py-1 text-sm bg-primary-600 text-white rounded hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
        >
          確定
        </button>
      </div>
    </div>
    
    <!-- 背景遮罩 -->
    <div
      v-if="isOpen"
      @click="closePicker"
      class="fixed inset-0 z-40"
    />
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
// Heroicons
import {
  UserIcon, CogIcon, HomeIcon, DocumentTextIcon, FolderIcon, 
  TagIcon, StarIcon, HeartIcon, LightBulbIcon, ShieldCheckIcon,
  BellIcon, ChatIcon, MailIcon, PhoneIcon, LocationMarkerIcon,
  CalendarIcon, ClockIcon, SearchIcon, PlusIcon, CheckIcon,
  XIcon, ArrowRightIcon, DownloadIcon, UploadIcon, ShareIcon,
  EyeIcon, PencilIcon, TrashIcon, DuplicateIcon, BookmarkIcon
} from '@heroicons/vue/outline'

export default {
  name: 'IconPicker',
  components: {
    UserIcon, CogIcon, HomeIcon, DocumentTextIcon, FolderIcon, 
    TagIcon, StarIcon, HeartIcon, LightBulbIcon, ShieldCheckIcon,
    BellIcon, ChatIcon, MailIcon, PhoneIcon, LocationMarkerIcon,
    CalendarIcon, ClockIcon, SearchIcon, PlusIcon, CheckIcon,
    XIcon, ArrowRightIcon, DownloadIcon, UploadIcon, ShareIcon,
    EyeIcon, PencilIcon, TrashIcon, DuplicateIcon, BookmarkIcon
  },
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    iconType: {
      type: String,
      default: '' // 'heroicons', 'bootstrap', 'emoji'
    }
  },
  emits: ['update:modelValue', 'update:iconType'],
  setup(props, { emit }) {
    const isOpen = ref(false)
    const iconPanel = ref(null)
    const activeTab = ref('heroicons')
    const selectedIcon = ref(props.modelValue)
    
    const iconTabs = [
      { key: 'heroicons', label: 'Heroicons' },
      { key: 'bootstrap', label: 'BS Icons' },
      { key: 'emoji', label: 'Emoji' }
    ]
    
    // Heroicons 圖標列表
    const heroicons = [
      { name: 'User', component: 'UserIcon' },
      { name: 'Settings', component: 'CogIcon' },
      { name: 'Home', component: 'HomeIcon' },
      { name: 'Document', component: 'DocumentTextIcon' },
      { name: 'Folder', component: 'FolderIcon' },
      { name: 'Tag', component: 'TagIcon' },
      { name: 'Star', component: 'StarIcon' },
      { name: 'Heart', component: 'HeartIcon' },
      { name: 'Light Bulb', component: 'LightBulbIcon' },
      { name: 'Shield', component: 'ShieldCheckIcon' },
      { name: 'Bell', component: 'BellIcon' },
      { name: 'Chat', component: 'ChatIcon' },
      { name: 'Mail', component: 'MailIcon' },
      { name: 'Phone', component: 'PhoneIcon' },
      { name: 'Location', component: 'LocationMarkerIcon' },
      { name: 'Calendar', component: 'CalendarIcon' },
      { name: 'Clock', component: 'ClockIcon' },
      { name: 'Search', component: 'SearchIcon' },
      { name: 'Plus', component: 'PlusIcon' },
      { name: 'Check', component: 'CheckIcon' },
      { name: 'Close', component: 'XIcon' },
      { name: 'Arrow Right', component: 'ArrowRightIcon' },
      { name: 'Download', component: 'DownloadIcon' },
      { name: 'Upload', component: 'UploadIcon' },
      { name: 'Share', component: 'ShareIcon' },
      { name: 'Eye', component: 'EyeIcon' },
      { name: 'Edit', component: 'PencilIcon' },
      { name: 'Delete', component: 'TrashIcon' },
      { name: 'Duplicate', component: 'DuplicateIcon' },
      { name: 'Bookmark', component: 'BookmarkIcon' }
    ]
    
    // Bootstrap Icons 類別列表
    const bootstrapIcons = [
      { name: 'Person', class: 'bi bi-person' },
      { name: 'Gear', class: 'bi bi-gear' },
      { name: 'House', class: 'bi bi-house' },
      { name: 'File Text', class: 'bi bi-file-text' },
      { name: 'Folder', class: 'bi bi-folder' },
      { name: 'Tag', class: 'bi bi-tag' },
      { name: 'Star', class: 'bi bi-star' },
      { name: 'Heart', class: 'bi bi-heart' },
      { name: 'Lightbulb', class: 'bi bi-lightbulb' },
      { name: 'Shield Check', class: 'bi bi-shield-check' },
      { name: 'Bell', class: 'bi bi-bell' },
      { name: 'Chat', class: 'bi bi-chat' },
      { name: 'Envelope', class: 'bi bi-envelope' },
      { name: 'Telephone', class: 'bi bi-telephone' },
      { name: 'Geo Alt', class: 'bi bi-geo-alt' },
      { name: 'Calendar', class: 'bi bi-calendar' },
      { name: 'Clock', class: 'bi bi-clock' },
      { name: 'Search', class: 'bi bi-search' },
      { name: 'Plus', class: 'bi bi-plus' },
      { name: 'Check', class: 'bi bi-check' },
      { name: 'X', class: 'bi bi-x' },
      { name: 'Arrow Right', class: 'bi bi-arrow-right' },
      { name: 'Download', class: 'bi bi-download' },
      { name: 'Upload', class: 'bi bi-upload' },
      { name: 'Share', class: 'bi bi-share' },
      { name: 'Eye', class: 'bi bi-eye' },
      { name: 'Pencil', class: 'bi bi-pencil' },
      { name: 'Trash', class: 'bi bi-trash' },
      { name: 'Files', class: 'bi bi-files' },
      { name: 'Bookmark', class: 'bi bi-bookmark' }
    ]
    
    // Emoji 列表
    const emojis = [
      { char: '😀', name: 'Grinning Face' },
      { char: '😃', name: 'Grinning Face with Big Eyes' },
      { char: '😄', name: 'Grinning Face with Smiling Eyes' },
      { char: '😊', name: 'Smiling Face with Smiling Eyes' },
      { char: '😍', name: 'Smiling Face with Heart-Eyes' },
      { char: '🤔', name: 'Thinking Face' },
      { char: '😎', name: 'Smiling Face with Sunglasses' },
      { char: '🚀', name: 'Rocket' },
      { char: '⭐', name: 'Star' },
      { char: '❤️', name: 'Red Heart' },
      { char: '💡', name: 'Light Bulb' },
      { char: '🎯', name: 'Direct Hit' },
      { char: '🏆', name: 'Trophy' },
      { char: '🎨', name: 'Artist Palette' },
      { char: '📚', name: 'Books' },
      { char: '💼', name: 'Briefcase' },
      { char: '🏠', name: 'House' },
      { char: '🌟', name: 'Glowing Star' },
      { char: '🔥', name: 'Fire' },
      { char: '⚡', name: 'Lightning' },
      { char: '🌈', name: 'Rainbow' },
      { char: '🎪', name: 'Circus Tent' },
      { char: '🎭', name: 'Performing Arts' },
      { char: '🎵', name: 'Musical Note' },
      { char: '📱', name: 'Mobile Phone' },
      { char: '💻', name: 'Laptop' },
      { char: '⌚', name: 'Watch' },
      { char: '📷', name: 'Camera' },
      { char: '🎮', name: 'Video Game' },
      { char: '🧩', name: 'Puzzle Piece' },
      { char: '🎲', name: 'Dice' },
      { char: '🏃', name: 'Runner' }
    ]
    
    const togglePicker = () => {
      isOpen.value = !isOpen.value
      if (isOpen.value && props.iconType) {
        activeTab.value = props.iconType
      }
    }
    
    const closePicker = () => {
      isOpen.value = false
    }
    
    const selectIcon = (icon, type) => {
      selectedIcon.value = icon
      emit('update:modelValue', icon)
      emit('update:iconType', type)
    }
    
    const clearIcon = () => {
      selectedIcon.value = ''
      emit('update:modelValue', '')
      emit('update:iconType', '')
      closePicker()
    }
    
    // 點擊外部關閉
    const handleClickOutside = (event) => {
      if (iconPanel.value && !iconPanel.value.contains(event.target)) {
        closePicker()
      }
    }
    
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
      selectedIcon.value = props.modelValue
      if (props.iconType) {
        activeTab.value = props.iconType
      }
    })
    
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })
    
    return {
      isOpen,
      iconPanel,
      activeTab,
      selectedIcon,
      iconTabs,
      heroicons,
      bootstrapIcons,
      emojis,
      togglePicker,
      closePicker,
      selectIcon,
      clearIcon
    }
  }
}
</script>

<style scoped>
.icon-picker {
  @apply relative inline-block;
}

.icon-picker-open .absolute {
  @apply block;
}
</style>