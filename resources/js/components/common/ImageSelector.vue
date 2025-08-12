<template>
  <div class="image-selector">
    <!-- 主要顯示區域 -->
    <div class="flex items-start space-x-6">
      <!-- 預覽區域 -->
      <div :class="previewContainerClass" class="relative group">
        <!-- 上傳的圖片 -->
        <img
          v-if="mode === 'upload' && (previewUrl || currentImageUrl)"
          :src="previewUrl || currentImageUrl"
          :alt="imageAlt"
          class="h-full w-full object-cover"
        />
        
        <!-- 字母縮寫 -->
        <div 
          v-else-if="mode === 'initials'"
          :style="{ backgroundColor: backgroundColor || defaultBackgroundColor }"
          class="font-type-image h-full w-full flex items-center justify-center text-white font-semibold"
          :class="textSizeClass"
        >
          {{ displayInitials }}
        </div>
        
        <!-- 圖標顯示 -->
        <div 
          v-else-if="mode === 'icon' && selectedIcon"
          :style="{ backgroundColor: backgroundColor || defaultBackgroundColor }"
          class="h-full w-full flex items-center justify-center"
        >
          <!-- Heroicons -->
          <component 
            v-if="iconType === 'heroicons'" 
            :is="getHeroiconComponent()" 
            :class="iconSizeClass"
            class="hero-type-image text-white"
          />
          <!-- Bootstrap Icons -->
          <i 
            v-else-if="iconType === 'bootstrap'" 
            :class="['bi', selectedIcon, bsIconSizeClass]"
            class="bs-type-image text-white"
          />
          <!-- Emoji -->
          <span 
            v-else-if="iconType === 'emoji'"
            :class="emojiSizeClass"
            class="emoji-type-image"
            style="transform: translateY(2px);"
          >
            {{ selectedIcon }}
          </span>
        </div>
        
        <!-- 預設佔位符 -->
        <div 
          v-else
          :style="{ backgroundColor: backgroundColor || defaultBackgroundColor }"
          class="h-full w-full flex items-center justify-center text-white"
        >
          <slot name="default-placeholder">
            <span :class="textSizeClass">{{ defaultInitials }}</span>
          </slot>
        </div>
        
        <!-- 載入覆蓋層 -->
        <div 
          v-if="isUploading || isRemoving" 
          class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center"
          :class="shapeClass"
        >
          <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
        </div>
        
        <!-- 懸停編輯提示 -->
        <div 
          v-if="!isUploading && !isRemoving" 
          class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer"
          :class="shapeClass"
          @click="openIconPicker"
        >
          <CogIcon class="w-5 h-5 text-white" />
        </div>
        
        <!-- 隱藏的 IconPicker (頭像點擊時顯示) -->
        <div class="absolute top-full left-0 mt-2 z-50">
          <IconPicker 
            v-show="showIconPicker"
            ref="avatarIconPickerRef"
            v-model="selectedIcon"
            v-model:icon-type="iconType"
            :background-color="backgroundColor"
            @file-selected="handleIconPickerFile"
            @color-picker-click="openBgColorPicker"
          />
        </div>
      </div>
      
      <!-- 快速操作區域 -->
      <div class="flex-1 space-y-3">
        <!-- 模式切換按鈕 -->
        <div class="flex space-x-2">
          <button
            type="button"
            @click="setMode('initials')"
            class="px-3 py-2 text-sm rounded border transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
            :class="mode === 'initials' 
              ? 'bg-primary-100 border-primary-300 text-primary-700' 
              : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50'"
          >
            字母
          </button>
          <button
            type="button"
            @click="setMode('icon')"
            class="px-3 py-2 text-sm rounded border transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
            :class="mode === 'icon' 
              ? 'bg-primary-100 border-primary-300 text-primary-700' 
              : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50'"
          >
            圖標
          </button>
          <button
            type="button"
            @click="setMode('upload')"
            class="px-3 py-2 text-sm rounded border transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
            :class="mode === 'upload' 
              ? 'bg-primary-100 border-primary-300 text-primary-700' 
              : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50'"
          >
            上傳
          </button>
        </div>
        
        <!-- 快速設定區 -->
        <div v-if="mode !== 'upload'" class="flex items-center space-x-2">
          <span class="text-sm text-gray-600">背景：</span>
          <ColorPicker v-model="backgroundColor" />
          
          <span v-if="mode === 'icon'" class="text-sm text-gray-600 ml-4">圖標：</span>
          <IconPicker 
            v-if="mode === 'icon'"
            ref="iconPickerRef"
            v-model="selectedIcon"
            v-model:icon-type="iconType"
            @file-selected="handleIconPickerFile"
          />
        </div>
        
        <!-- 上傳區域 -->
        <div v-if="mode === 'upload'" class="space-y-3">
          <div
            ref="dropZone"
            @drop="handleDrop"
            @dragover="handleDragOver"
            @dragenter="handleDragEnter"
            @dragleave="handleDragLeave"
            :class="{
              'border-primary-500 bg-primary-50': isDragOver,
              'border-gray-300': !isDragOver
            }"
            class="border-2 border-dashed rounded-lg p-3 text-center transition-colors cursor-pointer hover:border-primary-400 hover:bg-primary-25"
            @click="triggerFileInput"
          >
            <CloudUploadIcon class="mx-auto h-6 w-6 text-gray-400" />
            <p class="mt-1 text-sm text-gray-600">
              <span class="font-medium text-primary-500">點擊上傳</span>
              或拖曳檔案至此
            </p>
            <p class="text-xs text-gray-500">{{ fileHint }}</p>
          </div>
          
          <input
            ref="fileInput"
            type="file"
            :accept="accept"
            @change="handleFileChange"
            class="hidden"
          />
        </div>
        
        <!-- 錯誤訊息 -->
        <div v-if="errorMessage" class="text-sm text-red-600 bg-red-50 p-2 rounded">
          {{ errorMessage }}
        </div>
      </div>
    </div>
    
    <!-- 詳細設定彈窗 -->
    <div v-if="showSettings" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ title }} 設定
            </h3>
            
            <!-- 模式選擇 -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">顯示模式</label>
              <div class="grid grid-cols-3 gap-3">
                <button
                  type="button"
                  @click="setMode('initials')"
                  class="p-3 border-2 rounded-lg transition-colors focus:outline-none"
                  :class="mode === 'initials' 
                    ? 'border-primary-500 bg-primary-50' 
                    : 'border-gray-200 hover:border-gray-300'"
                >
                  <div class="text-center">
                    <div class="w-8 h-8 bg-primary-500 rounded-full mx-auto mb-1 flex items-center justify-center text-white text-sm font-medium">
                      AB
                    </div>
                    <span class="text-xs text-gray-600">字母縮寫</span>
                  </div>
                </button>
                
                <button
                  type="button"
                  @click="setMode('icon')"
                  class="p-3 border-2 rounded-lg transition-colors focus:outline-none"
                  :class="mode === 'icon' 
                    ? 'border-primary-500 bg-primary-50' 
                    : 'border-gray-200 hover:border-gray-300'"
                >
                  <div class="text-center">
                    <div class="w-8 h-8 bg-primary-500 rounded-full mx-auto mb-1 flex items-center justify-center">
                      <StarIcon class="w-4 h-4 text-white" />
                    </div>
                    <span class="text-xs text-gray-600">圖標</span>
                  </div>
                </button>
                
                <button
                  type="button"
                  @click="setMode('upload')"
                  class="p-3 border-2 rounded-lg transition-colors focus:outline-none"
                  :class="mode === 'upload' 
                    ? 'border-primary-500 bg-primary-50' 
                    : 'border-gray-200 hover:border-gray-300'"
                >
                  <div class="text-center">
                    <div class="w-8 h-8 bg-gray-200 rounded-full mx-auto mb-1 flex items-center justify-center">
                      <CloudUploadIcon class="w-4 h-4 text-gray-600" />
                    </div>
                    <span class="text-xs text-gray-600">上傳圖片</span>
                  </div>
                </button>
              </div>
            </div>
            
            <!-- 字母縮寫設定 -->
            <div v-if="mode === 'initials'" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">文字內容</label>
                <input
                  v-model="customInitials"
                  type="text"
                  maxlength="3"
                  placeholder="自訂文字 (最多3個字元)"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
                <p class="mt-1 text-xs text-gray-500">留空將自動使用姓名縮寫</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">背景顏色</label>
                <div class="flex items-center space-x-2">
                  <ColorPicker v-model="backgroundColor" />
                  <span class="text-sm text-gray-600">{{ backgroundColor || '使用預設顏色' }}</span>
                </div>
              </div>
            </div>
            
            <!-- 圖標設定 -->
            <div v-if="mode === 'icon'" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">選擇圖標</label>
                <IconPicker 
                  v-model="selectedIcon"
                  v-model:icon-type="iconType"
                  @file-selected="handleIconPickerFile"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">背景顏色</label>
                <div class="flex items-center space-x-2">
                  <ColorPicker v-model="backgroundColor" />
                  <span class="text-sm text-gray-600">{{ backgroundColor || '使用預設顏色' }}</span>
                </div>
              </div>
            </div>
            
            <!-- 上傳設定 -->
            <div v-if="mode === 'upload'" class="space-y-4">
              <div
                ref="modalDropZone"
                @drop="handleDrop"
                @dragover="handleDragOver"
                @dragenter="handleDragEnter"
                @dragleave="handleDragLeave"
                :class="{
                  'border-primary-500 bg-primary-50': isDragOver,
                  'border-gray-300': !isDragOver
                }"
                class="border-2 border-dashed rounded-lg p-6 text-center transition-colors cursor-pointer hover:border-primary-400 hover:bg-primary-25"
                @click="triggerFileInput"
              >
                <CloudUploadIcon class="mx-auto h-12 w-12 text-gray-400" />
                <p class="mt-2 text-sm text-gray-600">
                  <span class="font-medium text-primary-500">點擊選擇檔案</span>
                  或拖曳檔案至此區域
                </p>
                <p class="text-xs text-gray-500 mt-1">{{ fileHint }}</p>
              </div>
              
              <!-- 檔案資訊 -->
              <div v-if="selectedFile" class="bg-gray-50 p-3 rounded">
                <p class="text-sm"><strong>檔案：</strong>{{ selectedFile.name }}</p>
                <p class="text-sm"><strong>大小：</strong>{{ formatFileSize(selectedFile.size) }}</p>
              </div>
            </div>
          </div>
          
          <!-- 彈窗底部按鈕 -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="applySettings"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              確定
            </button>
            <button
              type="button"
              @click="cancelSettings"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              取消
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, nextTick } from 'vue'
import { CloudUploadIcon, CogIcon, StarIcon } from '@heroicons/vue/outline'
// 導入所有可能用到的 Heroicons (outline 和 solid)
import * as HeroIconsOutline from '@heroicons/vue/outline'
import * as HeroIconsSolid from '@heroicons/vue/solid'
import ColorPicker from './ColorPicker.vue'
import IconPicker from './IconPicker.vue'

export default {
  name: 'ImageSelector',
  components: {
    CloudUploadIcon,
    CogIcon,
    StarIcon,
    ColorPicker,
    IconPicker,
    ...HeroIconsOutline, // 註冊所有 Heroicons Outline
    ...HeroIconsSolid // 註冊所有 Heroicons Solid
  },
  props: {
    // 基本設定
    title: {
      type: String,
      default: '圖片'
    },
    currentImageUrl: {
      type: String,
      default: null
    },
    imageAlt: {
      type: String,
      default: ''
    },
    
    // 外觀設定
    size: {
      type: String,
      default: 'large'
    },
    shape: {
      type: String,
      default: 'circle'
    },
    
    // 字母縮寫設定
    initials: {
      type: String,
      default: ''
    },
    
    // 檔案上傳設定
    accept: {
      type: String,
      default: 'image/*'
    },
    maxSize: {
      type: Number,
      default: 2 * 1024 * 1024
    },
    allowedTypes: {
      type: Array,
      default: () => ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
    },
    fileHint: {
      type: String,
      default: '支援 JPG, PNG 格式，檔案大小不超過 2MB'
    },
    
    // 載入狀態
    uploading: {
      type: Boolean,
      default: false
    },
    removing: {
      type: Boolean,
      default: false
    }
  },
  emits: ['mode-changed', 'settings-changed', 'file-selected', 'file-error'],
  setup(props, { emit }) {
    const mode = ref('initials') // 'initials', 'icon', 'upload'
    const showSettings = ref(false)
    const showIconPicker = ref(false)
    const backgroundColor = ref('#6366f1')
    const customInitials = ref('')
    
    // 解析 Heroicon 圖標名稱和樣式
    const parseHeroicon = (icon) => {
      if (icon && icon.includes(':')) {
        const [style, name] = icon.split(':')
        return { style, name }
      }
      return { style: 'outline', name: icon }
    }
    
    // 獲取 Heroicon 組件
    const getHeroiconComponent = () => {
      if (!selectedIcon.value) return null
      
      const { style, name } = parseHeroicon(selectedIcon.value)
      heroiconStyle.value = style
      
      // 根據樣式選擇正確的組件庫
      const icons = style === 'solid' ? HeroIconsSolid : HeroIconsOutline
      return icons[name] || HeroIconsOutline[name]
    }
    const selectedIcon = ref('')
    const iconType = ref('heroicons')
    const heroiconStyle = ref('outline') // 儲存 Heroicon 樣式
    const selectedFile = ref(null)
    const previewUrl = ref(null)
    const isDragOver = ref(false)
    const errorMessage = ref('')
    const isUploading = ref(false)
    const isRemoving = ref(false)
    
    // 監聽外部載入狀態
    watch(() => props.uploading, (val) => isUploading.value = val)
    watch(() => props.removing, (val) => isRemoving.value = val)
    
    // 預設背景色
    const defaultBackgroundColor = '#6366f1'
    
    // 計算樣式類別
    const previewContainerClass = computed(() => {
      const sizeClasses = {
        small: 'h-12 w-12',
        medium: 'h-16 w-16', 
        large: 'h-20 w-20',
        xlarge: 'h-24 w-24'
      }
      
      const shapeClasses = {
        circle: 'rounded-full',
        rounded: 'rounded-lg',
        square: 'rounded-none'
      }
      
      return [
        sizeClasses[props.size],
        shapeClasses[props.shape],
        'flex items-center justify-center overflow-hidden flex-shrink-0'
      ].join(' ')
    })
    
    const shapeClass = computed(() => {
      return props.shape === 'circle' ? 'rounded-full' : 
             props.shape === 'rounded' ? 'rounded-lg' : ''
    })
    
    const textSizeClass = computed(() => {
      const sizeClasses = {
        small: 'text-sm',
        medium: 'text-base', 
        large: 'text-lg',
        xlarge: 'text-xl'
      }
      return sizeClasses[props.size] || 'text-lg'
    })
    
    const iconSizeClass = computed(() => {
      // Heroicons 使用 width/height classes
      const sizeClasses = {
        small: 'w-6 h-6',
        medium: 'w-8 h-8', 
        large: 'w-10 h-10',
        xlarge: 'w-12 h-12'
      }
      return sizeClasses[props.size] || 'w-10 h-10'
    })
    
    const bsIconSizeClass = computed(() => {
      // Bootstrap Icons 使用與 Heroicons 相似的大小
      const sizeClasses = {
        small: 'text-2xl',
        medium: 'text-3xl', 
        large: 'text-4xl',
        xlarge: 'text-5xl'
      }
      return sizeClasses[props.size] || 'text-4xl'
    })
    
    const emojiSizeClass = computed(() => {
      const sizeClasses = {
        small: 'text-2xl',
        medium: 'text-3xl', 
        large: 'text-4xl',
        xlarge: 'text-5xl'
      }
      return sizeClasses[props.size] || 'text-4xl'
    })
    
    // 顯示的字母縮寫
    const displayInitials = computed(() => {
      if (customInitials.value) {
        return customInitials.value.toUpperCase()
      }
      return props.initials?.toUpperCase() || defaultInitials.value
    })
    
    const defaultInitials = computed(() => {
      return props.imageAlt?.split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2) || '?'
    })
    
    // 設定模式
    const setMode = (newMode) => {
      mode.value = newMode
      emit('mode-changed', {
        mode: newMode,
        backgroundColor: backgroundColor.value,
        customInitials: customInitials.value,
        selectedIcon: selectedIcon.value,
        iconType: iconType.value
      })
    }
    
    // 文件處理
    const validateFile = (file) => {
      errorMessage.value = ''
      
      if (file.size > props.maxSize) {
        const maxSizeMB = (props.maxSize / (1024 * 1024)).toFixed(1)
        errorMessage.value = `檔案大小不能超過 ${maxSizeMB}MB`
        emit('file-error', errorMessage.value)
        return false
      }
      
      if (props.allowedTypes.length > 0 && !props.allowedTypes.includes(file.type)) {
        errorMessage.value = '不支援的檔案格式'
        emit('file-error', errorMessage.value)
        return false
      }
      
      return true
    }
    
    const processFile = (file) => {
      if (!validateFile(file)) return
      
      selectedFile.value = file
      
      // 生成預覽圖
      if (file.type.startsWith('image/')) {
        const reader = new FileReader()
        reader.onload = (e) => {
          previewUrl.value = e.target.result
        }
        reader.readAsDataURL(file)
      }
      
      emit('file-selected', file)
    }
    
    const handleFileChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        processFile(file)
      }
    }
    
    // 處理從 IconPicker 上傳的檔案
    const handleIconPickerFile = (file) => {
      // 切換到上傳模式
      mode.value = 'upload'
      
      // 處理檔案
      selectedFile.value = file
      processFile(file)
      
      // 關閉設定彈窗（如果開啟）
      showSettings.value = false
    }
    
    const handleDragEnter = (e) => {
      e.preventDefault()
      isDragOver.value = true
    }
    
    const handleDragOver = (e) => {
      e.preventDefault()
      isDragOver.value = true
    }
    
    const handleDragLeave = (e) => {
      e.preventDefault()
      if (!e.currentTarget.contains(e.relatedTarget)) {
        isDragOver.value = false
      }
    }
    
    const handleDrop = (e) => {
      e.preventDefault()
      isDragOver.value = false
      
      const files = e.dataTransfer.files
      if (files.length > 0) {
        processFile(files[0])
      }
    }
    
    const triggerFileInput = () => {
      document.querySelector('input[type="file"]')?.click()
    }
    
    const formatFileSize = (bytes) => {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }
    
    const applySettings = () => {
      emit('settings-changed', {
        mode: mode.value,
        backgroundColor: backgroundColor.value,
        customInitials: customInitials.value,
        selectedIcon: selectedIcon.value,
        iconType: iconType.value
      })
      showSettings.value = false
    }
    
    const cancelSettings = () => {
      showSettings.value = false
    }
    
    const iconPickerRef = ref(null)
    const avatarIconPickerRef = ref(null)
    
    // 開啟 IconPicker（由子組件 IconPicker 控制）
    const openIconPicker = () => {
      // 直接開啟頭像下方的 IconPicker
      showIconPicker.value = true
      nextTick(() => {
        if (avatarIconPickerRef.value && avatarIconPickerRef.value.togglePicker) {
          avatarIconPickerRef.value.togglePicker()
        }
      })
    }
    
    // 開啟背景顏色選擇器
    const openBgColorPicker = () => {
      // 找到 ColorPicker 並觸發它
      const colorPicker = document.querySelector('.color-picker-wrapper button')
      if (colorPicker) {
        colorPicker.click()
      }
    }
    
    return {
      mode,
      showSettings,
      showIconPicker,
      backgroundColor,
      customInitials,
      selectedIcon,
      iconType,
      heroiconStyle,
      getHeroiconComponent,
      selectedFile,
      previewUrl,
      isDragOver,
      errorMessage,
      isUploading,
      isRemoving,
      defaultBackgroundColor,
      previewContainerClass,
      shapeClass,
      textSizeClass,
      iconSizeClass,
      bsIconSizeClass,
      emojiSizeClass,
      displayInitials,
      defaultInitials,
      setMode,
      handleFileChange,
      handleDragEnter,
      handleDragOver,
      handleDragLeave,
      handleDrop,
      triggerFileInput,
      formatFileSize,
      applySettings,
      cancelSettings,
      handleIconPickerFile,
      openIconPicker,
      openBgColorPicker,
      iconPickerRef,
      avatarIconPickerRef
    }
  }
}
</script>

<style scoped>
.image-selector .hover\:bg-primary-25:hover {
  background-color: rgb(254 249 195 / 0.25);
}

.image-selector .bg-primary-25 {
  background-color: rgb(254 249 195 / 0.25);
}

.image-selector .font-type-image {
  font-size: 40px;
}

.image-selector .bs-type-image {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.image-selector .emoji-type-image {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>