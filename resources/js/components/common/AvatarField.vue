<template>
  <div class="avatar-field">
    <!-- 標籤 -->
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">{{ label }}</label>
    
    <div class="flex items-start space-x-6">
      <!-- 頭像預覽區域 -->
      <div :class="avatarContainerClass" class="relative group">
        <!-- 當前圖片或預覽圖片 -->
        <img
          v-if="previewUrl || currentImageUrl"
          :src="previewUrl || currentImageUrl"
          :alt="imageAlt"
          class="h-full w-full object-cover"
        />
        
        <!-- 佔位符內容 -->
        <div v-else class="h-full w-full flex items-center justify-center">
          <slot name="placeholder">
            <component 
              v-if="placeholderIcon" 
              :is="placeholderIcon" 
              :class="placeholderIconClass" 
            />
            <span v-else-if="placeholderText" :class="placeholderTextClass">
              {{ placeholderText }}
            </span>
          </slot>
        </div>
        
        <!-- 載入覆蓋層 -->
        <div 
          v-if="isUploading || isRemoving" 
          class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-full"
        >
          <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
        </div>
        
        <!-- 懸停效果 -->
        <div 
          v-if="!isUploading && !isRemoving" 
          class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer"
          :class="avatarShapeClass"
          @click="triggerFileInput"
        >
          <CloudUploadIcon class="w-6 h-6 text-white" />
        </div>
      </div>
      
      <div class="flex-1 space-y-3">
        <!-- 拖拽上傳區域 -->
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
          class="border-2 border-dashed rounded-lg p-4 text-center transition-colors cursor-pointer hover:border-primary-400 hover:bg-primary-25"
          @click="triggerFileInput"
        >
          <CloudUploadIcon class="mx-auto h-8 w-8 text-gray-400" />
          <p class="mt-2 text-sm text-gray-600">
            <span class="font-medium text-primary-500">點擊上傳</span>
            或拖曳檔案至此
          </p>
          <p class="text-xs text-gray-500 mt-1">{{ fileHint }}</p>
        </div>
        
        <!-- 隱藏的檔案輸入 -->
        <input
          ref="fileInput"
          type="file"
          :accept="accept"
          @change="handleFileChange"
          class="hidden"
        />
        
        <!-- 操作按鈕組 -->
        <div class="flex space-x-3">
          <!-- 移除按鈕 -->
          <button
            v-if="showRemoveButton && (previewUrl || currentImageUrl)"
            type="button"
            @click="showRemoveConfirm"
            :disabled="isUploading || isRemoving"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <TrashIcon class="w-4 h-4 mr-2" />
            {{ removeButtonText }}
          </button>
          
          <!-- 取消預覽按鈕 -->
          <button
            v-if="previewUrl && !currentImageUrl"
            type="button"
            @click="cancelPreview"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            <XIcon class="w-4 h-4 mr-2" />
            取消預覽
          </button>
        </div>
        
        <!-- 檔案資訊 -->
        <div v-if="selectedFile" class="text-sm text-gray-500">
          <p><strong>檔案：</strong>{{ selectedFile.name }}</p>
          <p><strong>大小：</strong>{{ formatFileSize(selectedFile.size) }}</p>
        </div>
        
        <!-- 錯誤訊息 -->
        <div v-if="errorMessage" class="text-sm text-red-600 bg-red-50 p-2 rounded">
          {{ errorMessage }}
        </div>
        
        <!-- 成功訊息 -->
        <div v-if="successMessage" class="text-sm text-green-600 bg-green-50 p-2 rounded">
          {{ successMessage }}
        </div>
      </div>
    </div>

    <!-- 移除確認對話框 -->
    <ConfirmDialog
      :show="showRemoveDialog"
      type="danger"
      :title="removeConfirmTitle"
      :message="removeConfirmMessage"
      :confirm-text="removeConfirmText"
      cancel-text="取消"
      :loading="isRemoving"
      @confirm="confirmRemove"
      @cancel="cancelRemove"
    />
  </div>
</template>

<script>
import { ref, computed, watch, nextTick } from 'vue'
import { CloudUploadIcon, TrashIcon, XIcon } from '@heroicons/vue/outline'
import ConfirmDialog from './ConfirmDialog.vue'

export default {
  name: 'AvatarField',
  components: {
    CloudUploadIcon,
    TrashIcon,
    XIcon,
    ConfirmDialog
  },
  props: {
    // 基本配置
    label: {
      type: String,
      default: ''
    },
    currentImageUrl: {
      type: String,
      default: null
    },
    imageAlt: {
      type: String,
      default: ''
    },
    
    // 頭像樣式配置
    size: {
      type: String,
      default: 'large', // small, medium, large, xlarge
      validator: value => ['small', 'medium', 'large', 'xlarge'].includes(value)
    },
    shape: {
      type: String,
      default: 'circle', // circle, rounded, square
      validator: value => ['circle', 'rounded', 'square'].includes(value)
    },
    
    // 佔位符配置
    placeholderIcon: {
      type: [String, Object],
      default: null
    },
    placeholderIconClass: {
      type: String,
      default: 'w-8 h-8 text-gray-400'
    },
    placeholderText: {
      type: String,
      default: ''
    },
    placeholderTextClass: {
      type: String,
      default: 'text-white text-lg font-medium'
    },
    
    // 檔案驗證配置
    accept: {
      type: String,
      default: 'image/*'
    },
    maxSize: {
      type: Number,
      default: 2 * 1024 * 1024 // 2MB
    },
    allowedTypes: {
      type: Array,
      default: () => ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
    },
    
    // UI 文字配置
    fileHint: {
      type: String,
      default: '支援 JPG, PNG, GIF 格式，檔案大小不超過 2MB'
    },
    removeButtonText: {
      type: String,
      default: '移除圖片'
    },
    removeConfirmTitle: {
      type: String,
      default: '移除圖片'
    },
    removeConfirmMessage: {
      type: String,
      default: '確定要移除此圖片嗎？此操作無法復原。'
    },
    removeConfirmText: {
      type: String,
      default: '移除'
    },
    
    // 功能開關
    showRemoveButton: {
      type: Boolean,
      default: true
    },
    autoUpload: {
      type: Boolean,
      default: false
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
  emits: [
    'file-selected',
    'file-error', 
    'upload',
    'remove',
    'success',
    'error'
  ],
  setup(props, { emit }) {
    const isDragOver = ref(false)
    const previewUrl = ref(null)
    const selectedFile = ref(null)
    const errorMessage = ref('')
    const successMessage = ref('')
    const showRemoveDialog = ref(false)
    const isUploading = ref(false)
    const isRemoving = ref(false)
    
    // 監聽外部載入狀態
    watch(() => props.uploading, (val) => {
      isUploading.value = val
    })
    
    watch(() => props.removing, (val) => {
      isRemoving.value = val
    })
    
    // 計算頭像容器樣式
    const avatarContainerClass = computed(() => {
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
      
      const bgClass = previewUrl.value || props.currentImageUrl ? 'bg-gray-100' : 'bg-primary-500'
      
      return [
        sizeClasses[props.size],
        shapeClasses[props.shape],
        bgClass,
        'flex items-center justify-center overflow-hidden flex-shrink-0'
      ].join(' ')
    })
    
    const avatarShapeClass = computed(() => {
      return props.shape === 'circle' ? 'rounded-full' : 
             props.shape === 'rounded' ? 'rounded-lg' : ''
    })
    
    // 檔案驗證
    const validateFile = (file) => {
      clearMessages()
      
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
    
    // 處理檔案選擇
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
      
      // 自動上傳
      if (props.autoUpload) {
        handleUpload()
      }
    }
    
    // 檔案輸入處理
    const handleFileChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        processFile(file)
      }
    }
    
    // 拖拽事件處理
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
    
    // 觸發檔案選擇
    const triggerFileInput = () => {
      document.querySelector('input[type="file"]')?.click()
    }
    
    // 取消預覽
    const cancelPreview = () => {
      previewUrl.value = null
      selectedFile.value = null
      clearMessages()
      
      // 清空檔案輸入
      const fileInput = document.querySelector('input[type="file"]')
      if (fileInput) {
        fileInput.value = ''
      }
    }
    
    // 上傳處理
    const handleUpload = () => {
      if (!selectedFile.value) return
      
      isUploading.value = true
      emit('upload', selectedFile.value)
    }
    
    // 顯示移除確認
    const showRemoveConfirm = () => {
      showRemoveDialog.value = true
    }
    
    // 確認移除
    const confirmRemove = () => {
      isRemoving.value = true
      emit('remove')
    }
    
    // 取消移除
    const cancelRemove = () => {
      showRemoveDialog.value = false
    }
    
    // 清除訊息
    const clearMessages = () => {
      errorMessage.value = ''
      successMessage.value = ''
    }
    
    // 顯示成功訊息
    const showSuccess = (message) => {
      clearMessages()
      successMessage.value = message
      emit('success', message)
      
      // 3秒後清除
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    }
    
    // 顯示錯誤訊息
    const showError = (message) => {
      clearMessages()
      errorMessage.value = message
      emit('error', message)
    }
    
    // 格式化檔案大小
    const formatFileSize = (bytes) => {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }
    
    // 重置狀態
    const reset = () => {
      previewUrl.value = null
      selectedFile.value = null
      isDragOver.value = false
      showRemoveDialog.value = false
      isUploading.value = false
      isRemoving.value = false
      clearMessages()
    }
    
    // 外部可調用的方法
    const publicMethods = {
      reset,
      showSuccess,
      showError,
      clearMessages
    }
    
    return {
      isDragOver,
      previewUrl,
      selectedFile,
      errorMessage,
      successMessage,
      showRemoveDialog,
      isUploading,
      isRemoving,
      avatarContainerClass,
      avatarShapeClass,
      handleFileChange,
      handleDragEnter,
      handleDragOver,
      handleDragLeave,
      handleDrop,
      triggerFileInput,
      cancelPreview,
      handleUpload,
      showRemoveConfirm,
      confirmRemove,
      cancelRemove,
      formatFileSize,
      ...publicMethods
    }
  }
}
</script>

<style scoped>
.avatar-field .hover\:bg-primary-25:hover {
  background-color: rgb(254 249 195 / 0.25);
}

.avatar-field .bg-primary-25 {
  background-color: rgb(254 249 195 / 0.25);
}

.avatar-field .group:hover .group-hover\:opacity-100 {
  opacity: 1;
}

.avatar-field .group:hover .group-hover\:bg-opacity-30 {
  background-opacity: 0.3;
}
</style>