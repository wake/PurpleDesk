<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- 背景遮罩 -->
      <div 
        class="fixed inset-0 transition-opacity" 
        aria-hidden="true"
        @click="cancel"
      >
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
      </div>

      <!-- 對話框 -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <!-- 圖標 -->
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="iconBgClass">
              <!-- 警告圖標 -->
              <svg v-if="type === 'warning'" class="h-6 w-6" :class="iconClass" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <!-- 危險圖標 -->
              <svg v-else-if="type === 'danger'" class="h-6 w-6" :class="iconClass" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9 12a1 1 0 002 0V8a1 1 0 10-2 0v4zm1 3a1 1 0 100-2 1 1 0 000 2zm-7-3a7 7 0 1014 0A7 7 0 003 12z" clip-rule="evenodd" />
              </svg>
              <!-- 資訊圖標 -->
              <svg v-else class="h-6 w-6" :class="iconClass" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
            </div>
            
            <!-- 內容 -->
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ title }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  {{ message }}
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- 按鈕區域 -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="confirm"
            :disabled="loading"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            :class="confirmButtonClass"
          >
            {{ loading ? '處理中...' : confirmText }}
          </button>
          <button
            @click="cancel"
            :disabled="loading"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
          >
            {{ cancelText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'ConfirmDialog',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: '確認操作'
    },
    message: {
      type: String,
      required: true
    },
    confirmText: {
      type: String,
      default: '確認'
    },
    cancelText: {
      type: String,
      default: '取消'
    },
    type: {
      type: String,
      default: 'warning', // warning, danger, info
      validator: (value) => ['warning', 'danger', 'info'].includes(value)
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['confirm', 'cancel'],
  setup(props, { emit }) {
    const iconBgClass = computed(() => {
      switch (props.type) {
        case 'danger':
          return 'bg-red-100'
        case 'info':
          return 'bg-blue-100'
        default: // warning
          return 'bg-yellow-100'
      }
    })
    
    const iconClass = computed(() => {
      switch (props.type) {
        case 'danger':
          return 'text-red-600'
        case 'info':
          return 'text-blue-600'
        default: // warning
          return 'text-yellow-600'
      }
    })
    
    const confirmButtonClass = computed(() => {
      switch (props.type) {
        case 'danger':
          return 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
        case 'info':
          return 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'
        default: // warning
          return 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500'
      }
    })
    
    const confirm = () => {
      emit('confirm')
    }
    
    const cancel = () => {
      emit('cancel')
    }
    
    return {
      iconBgClass,
      iconClass,
      confirmButtonClass,
      confirm,
      cancel
    }
  }
}
</script>