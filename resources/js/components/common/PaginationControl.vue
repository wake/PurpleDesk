<template>
  <div class="px-6 py-4 border-t border-gray-200">
    <div class="flex items-center justify-between">
      <!-- 手機版分頁 -->
      <div class="flex-1 flex justify-between sm:hidden">
        <button
          @click="$emit('page-changed', currentPage - 1)"
          :disabled="currentPage <= 1"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          上一頁
        </button>
        <button
          @click="$emit('page-changed', currentPage + 1)"
          :disabled="currentPage >= pagination.last_page"
          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          下一頁
        </button>
      </div>
      
      <!-- 桌面版分頁 -->
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <!-- 左側：顯示資訊和每頁數量選擇 -->
        <div class="flex items-center space-x-4">
          <div>
            <p class="text-sm text-gray-700">
              顯示第
              <span class="font-medium">{{ startItem }}</span>
              到
              <span class="font-medium">{{ endItem }}</span>
              筆，共
              <span class="font-medium">{{ pagination.total }}</span>
              筆結果
            </p>
          </div>
          
          <!-- 每頁顯示數量選擇 -->
          <div class="flex items-center space-x-2">
            <label for="per-page" class="text-sm text-gray-700">每頁顯示：</label>
            <div class="relative">
              <select
                id="per-page"
                :value="pagination.per_page"
                @change="$emit('per-page-changed', parseInt($event.target.value))"
                class="block w-full pl-3 pr-8 py-1.5 text-base border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: none;"
              >
                <option v-for="option in perPageOptions" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
        
        <!-- 右側：分頁按鈕 -->
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            <button
              @click="$emit('page-changed', currentPage - 1)"
              :disabled="currentPage <= 1"
              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              ‹
            </button>
            
            <!-- 頁碼按鈕 -->
            <template v-if="pagination.last_page <= 7">
              <!-- 總頁數少於等於 7 頁，全部顯示 -->
              <button
                v-for="page in pagination.last_page"
                :key="page"
                @click="$emit('page-changed', page)"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                  page === currentPage
                    ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
            </template>
            
            <template v-else>
              <!-- 總頁數大於 7 頁，使用省略號邏輯 -->
              <template v-if="currentPage <= 4">
                <!-- 當前頁在前面時 -->
                <button
                  v-for="page in 5"
                  :key="page"
                  @click="$emit('page-changed', page)"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    page === currentPage
                      ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                  ...
                </span>
                <button
                  @click="$emit('page-changed', pagination.last_page)"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                  {{ pagination.last_page }}
                </button>
              </template>
              
              <template v-else-if="currentPage >= pagination.last_page - 3">
                <!-- 當前頁在後面時 -->
                <button
                  @click="$emit('page-changed', 1)"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                  1
                </button>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                  ...
                </span>
                <button
                  v-for="page in 5"
                  :key="page"
                  @click="$emit('page-changed', pagination.last_page - 5 + page)"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    (pagination.last_page - 5 + page) === currentPage
                      ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                  ]"
                >
                  {{ pagination.last_page - 5 + page }}
                </button>
              </template>
              
              <template v-else>
                <!-- 當前頁在中間時 -->
                <button
                  @click="$emit('page-changed', 1)"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                  1
                </button>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                  ...
                </span>
                <button
                  v-for="page in [currentPage - 1, currentPage, currentPage + 1]"
                  :key="page"
                  @click="$emit('page-changed', page)"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    page === currentPage
                      ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                  ...
                </span>
                <button
                  @click="$emit('page-changed', pagination.last_page)"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                  {{ pagination.last_page }}
                </button>
              </template>
            </template>
            
            <button
              @click="$emit('page-changed', currentPage + 1)"
              :disabled="currentPage >= pagination.last_page"
              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              ›
            </button>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PaginationControl',
  props: {
    pagination: {
      type: Object,
      required: true,
      validator: (pagination) => {
        return pagination && 
               typeof pagination.current_page === 'number' &&
               typeof pagination.last_page === 'number' &&
               typeof pagination.per_page === 'number' &&
               typeof pagination.total === 'number'
      }
    },
    perPageOptions: {
      type: Array,
      default: () => [10, 25, 50, 100]
    }
  },
  emits: ['page-changed', 'per-page-changed'],
  computed: {
    currentPage() {
      return this.pagination.current_page
    },
    startItem() {
      return (this.currentPage - 1) * this.pagination.per_page + 1
    },
    endItem() {
      return Math.min(this.currentPage * this.pagination.per_page, this.pagination.total)
    }
  }
}
</script>