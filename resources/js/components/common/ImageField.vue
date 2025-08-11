<template>
  <div class="image-field">
    <!-- 標籤 -->
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">{{ label }}</label>
    
    <!-- 主要圖片選擇器 -->
    <ImageSelector
      :title="label || 'Image'"
      :current-image-url="currentImageUrl"
      :image-alt="imageAlt"
      :size="size"
      :shape="shape"
      :initials="initials"
      :accept="accept"
      :max-size="maxSize"
      :allowed-types="allowedTypes"
      :file-hint="fileHint"
      :uploading="uploading"
      :removing="removing"
      @mode-changed="handleModeChanged"
      @settings-changed="handleSettingsChanged"
      @file-selected="handleFileSelected"
      @file-error="handleFileError"
    />
    
    <!-- 操作按鈕區域 -->
    <div v-if="showActions" class="mt-3 flex space-x-3">
      <!-- 移除按鈕 -->
      <button
        v-if="showRemoveButton && hasImage"
        type="button"
        @click="showRemoveConfirm"
        :disabled="uploading || removing"
        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <TrashIcon class="w-4 h-4 mr-2" />
        {{ removeButtonText }}
      </button>
    </div>
    
    <!-- 錯誤訊息 -->
    <div v-if="errorMessage" class="mt-2 text-sm text-red-600 bg-red-50 p-2 rounded">
      {{ errorMessage }}
    </div>
    
    <!-- 成功訊息 -->
    <div v-if="successMessage" class="mt-2 text-sm text-green-600 bg-green-50 p-2 rounded">
      {{ successMessage }}
    </div>

    <!-- 移除確認對話框 -->
    <ConfirmDialog
      :show="showRemoveDialog"
      type="danger"
      :title="removeConfirmTitle"
      :message="removeConfirmMessage"
      :confirm-text="removeConfirmText"
      cancel-text="取消"
      :loading="removing"
      @confirm="confirmRemove"
      @cancel="cancelRemove"
    />
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { TrashIcon } from '@heroicons/vue/outline'
import ImageSelector from './ImageSelector.vue'
import ConfirmDialog from './ConfirmDialog.vue'

export default {
  name: 'ImageField',
  components: {
    TrashIcon,
    ImageSelector,
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
    initials: {
      type: String,
      default: ''
    },
    
    // 外觀配置
    size: {
      type: String,
      default: 'large',
      validator: value => ['small', 'medium', 'large', 'xlarge'].includes(value)
    },
    shape: {
      type: String,
      default: 'circle',
      validator: value => ['circle', 'rounded', 'square'].includes(value)
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
    fileHint: {
      type: String,
      default: '支援 JPG, PNG, GIF 格式，檔案大小不超過 2MB'
    },
    
    // UI 文字配置
    removeButtonText: {
      type: String,
      default: '移除'
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
    showActions: {
      type: Boolean,
      default: true
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
    'mode-changed',
    'settings-changed', 
    'file-selected',
    'file-error',
    'remove',
    'success',
    'error'
  ],
  setup(props, { emit }) {
    const errorMessage = ref('')
    const successMessage = ref('')
    const showRemoveDialog = ref(false)
    
    // 是否有圖片
    const hasImage = computed(() => {
      return !!props.currentImageUrl
    })
    
    // 事件處理
    const handleModeChanged = (data) => {
      clearMessages()
      emit('mode-changed', data)
    }
    
    const handleSettingsChanged = (data) => {
      clearMessages()
      emit('settings-changed', data)
    }
    
    const handleFileSelected = (file) => {
      clearMessages()
      emit('file-selected', file)
    }
    
    const handleFileError = (error) => {
      errorMessage.value = error
      emit('file-error', error)
    }
    
    // 移除功能
    const showRemoveConfirm = () => {
      showRemoveDialog.value = true
    }
    
    const confirmRemove = () => {
      emit('remove')
      showRemoveDialog.value = false
    }
    
    const cancelRemove = () => {
      showRemoveDialog.value = false
    }
    
    // 訊息管理
    const clearMessages = () => {
      errorMessage.value = ''
      successMessage.value = ''
    }
    
    const showSuccess = (message) => {
      clearMessages()
      successMessage.value = message
      emit('success', message)
      
      // 3秒後清除
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    }
    
    const showError = (message) => {
      clearMessages()
      errorMessage.value = message
      emit('error', message)
    }
    
    // 重置狀態
    const reset = () => {
      showRemoveDialog.value = false
      clearMessages()
    }
    
    return {
      errorMessage,
      successMessage,
      showRemoveDialog,
      hasImage,
      handleModeChanged,
      handleSettingsChanged,
      handleFileSelected,
      handleFileError,
      showRemoveConfirm,
      confirmRemove,
      cancelRemove,
      clearMessages,
      showSuccess,
      showError,
      reset
    }
  }
}
</script>

<style scoped>
.image-field {
  @apply space-y-3;
}
</style>