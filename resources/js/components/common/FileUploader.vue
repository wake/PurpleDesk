<template>
  <div class="file-uploader">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">{{ label }}</label>
    <div class="flex items-start space-x-6">
      <!-- 預覽區域 -->
      <div v-if="showPreview" :class="previewContainerClass">
        <img
          v-if="previewUrl || currentFileUrl"
          :src="previewUrl || currentFileUrl"
          :alt="previewAlt"
          class="h-full w-full object-cover"
        />
        <slot v-else name="placeholder">
          <component :is="placeholderIcon" :class="placeholderIconClass" />
        </slot>
      </div>
      
      <div class="flex-1">
        <!-- 拖曳上傳區域 -->
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
          @click="$refs.fileInput.click()"
        >
          <CloudUploadIcon class="mx-auto h-8 w-8 text-gray-400" />
          <p class="mt-2 text-sm text-gray-600">
            <span class="font-medium text-primary-500">點擊上傳</span>
            或拖曳檔案至此
          </p>
          <p class="text-xs text-gray-500 mt-1">{{ fileHint }}</p>
        </div>
        
        <input
          ref="fileInput"
          type="file"
          :accept="accept"
          @change="handleFileChange"
          class="hidden"
        />
        
        <!-- 移除檔案按鈕 -->
        <div v-if="showRemoveButton && (previewUrl || currentFileUrl)" class="mt-3">
          <button
            type="button"
            @click="$emit('remove')"
            :disabled="loading"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i class="bi bi-trash mr-2"></i>
            {{ removeButtonText }}
          </button>
        </div>
        
        <!-- 錯誤訊息 -->
        <div v-if="errorMessage" class="mt-2 text-sm text-red-600">
          {{ errorMessage }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { CloudUploadIcon } from '@heroicons/vue/outline'

export default {
  name: 'FileUploader',
  components: {
    CloudUploadIcon
  },
  props: {
    // 標籤
    label: {
      type: String,
      default: ''
    },
    // 當前檔案 URL
    currentFileUrl: {
      type: String,
      default: null
    },
    // 預覽圖片 alt 文字
    previewAlt: {
      type: String,
      default: ''
    },
    // 是否顯示預覽
    showPreview: {
      type: Boolean,
      default: true
    },
    // 預覽容器樣式類別
    previewContainerClass: {
      type: String,
      default: 'h-20 w-20 rounded-full bg-primary-500 flex items-center justify-center overflow-hidden flex-shrink-0'
    },
    // 佔位符圖標
    placeholderIcon: {
      type: [String, Object],
      default: 'CloudUploadIcon'
    },
    // 佔位符圖標樣式類別
    placeholderIconClass: {
      type: String,
      default: 'text-white text-lg font-medium'
    },
    // 接受的檔案類型
    accept: {
      type: String,
      default: 'image/*'
    },
    // 檔案提示文字
    fileHint: {
      type: String,
      default: '支援 JPG, PNG 格式，檔案大小不超過 2MB'
    },
    // 最大檔案大小 (bytes)
    maxSize: {
      type: Number,
      default: 2 * 1024 * 1024 // 2MB
    },
    // 允許的檔案類型 (MIME types)
    allowedTypes: {
      type: Array,
      default: () => ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']
    },
    // 是否顯示移除按鈕
    showRemoveButton: {
      type: Boolean,
      default: true
    },
    // 移除按鈕文字
    removeButtonText: {
      type: String,
      default: '移除檔案'
    },
    // 載入狀態
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['file-selected', 'file-error', 'remove'],
  setup(props, { emit }) {
    const isDragOver = ref(false)
    const previewUrl = ref(null)
    const errorMessage = ref('')
    
    const validateFile = (file) => {
      // 清除舊錯誤
      errorMessage.value = ''
      
      // 驗證檔案大小
      if (file.size > props.maxSize) {
        const maxSizeMB = (props.maxSize / (1024 * 1024)).toFixed(1)
        errorMessage.value = `檔案大小不能超過 ${maxSizeMB}MB`
        emit('file-error', errorMessage.value)
        return false
      }
      
      // 驗證檔案類型
      if (props.allowedTypes.length > 0 && !props.allowedTypes.includes(file.type)) {
        errorMessage.value = '不支援的檔案格式'
        emit('file-error', errorMessage.value)
        return false
      }
      
      return true
    }
    
    const processFile = (file) => {
      if (!validateFile(file)) {
        return
      }
      
      // 產生預覽圖 (僅對圖片檔案)
      if (file.type.startsWith('image/')) {
        const reader = new FileReader()
        reader.onload = (e) => {
          previewUrl.value = e.target.result
        }
        reader.readAsDataURL(file)
      }
      
      // 發送檔案選擇事件
      emit('file-selected', file)
    }
    
    const handleFileChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        processFile(file)
      }
    }
    
    // 拖曳事件處理
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
      // 只有在真正離開目標元素時才設置為 false
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
    
    // 清空預覽和錯誤狀態
    const reset = () => {
      previewUrl.value = null
      errorMessage.value = ''
      const fileInput = document.querySelector('input[type="file"]')
      if (fileInput) {
        fileInput.value = ''
      }
    }
    
    return {
      isDragOver,
      previewUrl,
      errorMessage,
      handleFileChange,
      handleDragEnter,
      handleDragOver,
      handleDragLeave,
      handleDrop,
      reset
    }
  }
}
</script>

<style scoped>
.file-uploader .hover\:bg-primary-25:hover {
  background-color: rgb(254 249 195 / 0.25);
}

.file-uploader .bg-primary-25 {
  background-color: rgb(254 249 195 / 0.25);
}
</style>