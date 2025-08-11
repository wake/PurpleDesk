<template>
  <div class="px-6 py-4 border-t border-gray-200">
    <!-- 手機版分頁 -->
    <div class="flex items-center justify-between sm:hidden">
      <button
        @click="$emit('page-changed', currentPage - 1)"
        :disabled="currentPage <= 1"
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        上一頁
      </button>
      
      <span class="text-sm text-gray-700">
        {{ currentPage }} / {{ pagination.last_page }}
      </span>
      
      <button
        @click="$emit('page-changed', currentPage + 1)"
        :disabled="currentPage >= pagination.last_page"
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        下一頁
      </button>
    </div>
    
    <!-- 桌面版分頁 -->
    <div class="hidden sm:flex sm:items-center sm:justify-center sm:space-x-8">
      <!-- 左側：每頁顯示數量選擇 -->
      <div class="flex items-center space-x-2">
        <label for="per-page" class="text-sm text-gray-600">每頁</label>
        <div class="relative">
          <select
            id="per-page"
            :value="pagination.per_page"
            @change="$emit('per-page-changed', parseInt($event.target.value))"
            class="block w-16 pl-3 pr-8 py-1.5 text-sm border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: none;"
          >
            <option v-for="option in perPageOptions" :key="option" :value="option">
              {{ option }}
            </option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </div>
        </div>
        <span class="text-sm text-gray-600">筆</span>
      </div>
      
      <!-- 中間：分頁按鈕 -->
      <nav class="flex items-center space-x-1">
        <!-- 上一頁 -->
        <button
          @click="$emit('page-changed', currentPage - 1)"
          :disabled="currentPage <= 1"
          class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        
        <!-- 頁碼按鈕 -->
        <template v-if="pagination.last_page <= 7">
          <!-- 總頁數少於等於 7 頁，全部顯示 -->
          <button
            v-for="page in pagination.last_page"
            :key="page"
            @click="$emit('page-changed', page)"
            :class="[
              'relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium border rounded-md transition-colors',
              page === currentPage
                ? 'bg-primary-600 border-primary-600 text-white hover:bg-primary-700'
                : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700'
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
                'relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium border rounded-md transition-colors',
                page === currentPage
                  ? 'bg-primary-600 border-primary-600 text-white hover:bg-primary-700'
                  : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700'
              ]"
            >
              {{ page }}
            </button>
            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400">
              ...
            </span>
            <button
              @click="$emit('page-changed', pagination.last_page)"
              class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700"
            >
              {{ pagination.last_page }}
            </button>
          </template>
          
          <template v-else-if="currentPage >= pagination.last_page - 3">
            <!-- 當前頁在後面時 -->
            <button
              @click="$emit('page-changed', 1)"
              class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700"
            >
              1
            </button>
            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400">
              ...
            </span>
            <button
              v-for="page in 5"
              :key="page"
              @click="$emit('page-changed', pagination.last_page - 5 + page)"
              :class="[
                'relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium border rounded-md transition-colors',
                (pagination.last_page - 5 + page) === currentPage
                  ? 'bg-primary-600 border-primary-600 text-white hover:bg-primary-700'
                  : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700'
              ]"
            >
              {{ pagination.last_page - 5 + page }}
            </button>
          </template>
          
          <template v-else>
            <!-- 當前頁在中間時 -->
            <button
              @click="$emit('page-changed', 1)"
              class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700"
            >
              1
            </button>
            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400">
              ...
            </span>
            <button
              v-for="page in [currentPage - 1, currentPage, currentPage + 1]"
              :key="page"
              @click="$emit('page-changed', page)"
              :class="[
                'relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium border rounded-md transition-colors',
                page === currentPage
                  ? 'bg-primary-600 border-primary-600 text-white hover:bg-primary-700'
                  : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700'
              ]"
            >
              {{ page }}
            </button>
            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400">
              ...
            </span>
            <button
              @click="$emit('page-changed', pagination.last_page)"
              class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700"
            >
              {{ pagination.last_page }}
            </button>
          </template>
        </template>
        
        <!-- 下一頁 -->
        <button
          @click="$emit('page-changed', currentPage + 1)"
          :disabled="currentPage >= pagination.last_page"
          class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </nav>
      
      <!-- 右側：顯示資訊 -->
      <div class="text-sm text-gray-600">
        {{ startItem }}-{{ endItem }} / {{ pagination.total }}
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