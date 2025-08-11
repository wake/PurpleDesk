<template>
  <div class="px-6 py-4 border-t border-gray-200">
    <!-- 手機版分頁 -->
    <div class="flex items-center justify-between sm:hidden">
      <button
        @click="changePage(currentPage - 1)"
        :disabled="currentPage <= 1"
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400"
      >
        上一頁
      </button>
      
      <span class="text-sm text-gray-700">
        {{ currentPage }} / {{ pagination.last_page }}
      </span>
      
      <button
        @click="changePage(currentPage + 1)"
        :disabled="currentPage >= pagination.last_page"
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400"
      >
        下一頁
      </button>
    </div>
    
    <!-- 桌面版分頁 -->
    <div class="hidden sm:flex sm:items-center sm:justify-center">
      <!-- 整合的分頁控制區塊 -->
      <div class="relative flex items-center justify-between bg-gray-50 px-4 py-2 rounded-lg min-w-96">
        <!-- 左側：每頁數量下拉（自訂樣式） -->
        <div class="flex items-center">
          <span class="text-sm text-gray-600">每頁</span>
          <div class="relative ml-1">
            <button
              @click.stop="showPerPageDropdown = !showPerPageDropdown"
              data-dropdown-button
              class="inline-flex items-center text-sm text-primary-600 border-b border-primary-600 hover:text-primary-700 hover:border-primary-700 transition-colors"
            >
              {{ pagination.per_page }}
              <svg class="ml-0.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            
            <!-- 下拉選單（向上展開） -->
            <div
              v-if="showPerPageDropdown"
              @click.stop
              data-dropdown-menu
              class="absolute left-0 bottom-full mb-1 w-16 bg-white rounded-md shadow-lg z-10 border border-gray-200"
            >
              <button
                v-for="option in perPageOptions"
                :key="option"
                @click="selectPerPage(option)"
                :class="[
                  'block w-full text-left px-3 py-2 text-sm hover:bg-gray-100',
                  option === pagination.per_page ? 'text-primary-600 font-medium bg-gray-50' : 'text-gray-700'
                ]"
              >
                {{ option }}
              </button>
            </div>
          </div>
          <span class="text-sm text-gray-600 ml-1">筆</span>
        </div>
        
        <!-- 中間：分頁按鈕 -->
        <nav class="flex items-center space-x-1">
        <!-- 上一頁 -->
        <button
          @click="changePage(currentPage - 1)"
          :disabled="currentPage <= 1"
          class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-gray-400 transition-colors"
        >
          <div v-if="loadingPage === currentPage - 1" class="absolute inset-0 flex items-center justify-center">
            <div class="animate-spin h-6 w-6 border-2 border-primary-300 rounded-full border-t-transparent opacity-40"></div>
          </div>
          <svg class="relative z-10 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        
        <!-- 頁碼按鈕 -->
        <template v-if="pagination.last_page <= 7">
          <!-- 總頁數少於等於 7 頁，全部顯示 -->
          <button
            v-for="page in pagination.last_page"
            :key="page"
            @click="changePage(page)"
            :class="[
              'relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium rounded-md transition-colors',
              page === currentPage
                ? 'bg-primary-200 text-primary-900 font-semibold'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'
            ]"
          >
            <div v-if="loadingPage === page" class="absolute inset-0 flex items-center justify-center">
              <div class="animate-spin h-6 w-6 border-2 border-primary-300 rounded-full border-t-transparent opacity-40"></div>
            </div>
            <span class="relative z-10">{{ page }}</span>
          </button>
        </template>
        
        <template v-else>
          <!-- 總頁數大於 7 頁，使用省略號邏輯 -->
          <template v-if="currentPage <= 4">
            <!-- 當前頁在前面時 -->
            <button
              v-for="page in 5"
              :key="page"
              @click="changePage(page)"
              :class="[
                'relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium rounded-md transition-colors',
                page === currentPage
                  ? 'bg-primary-200 text-primary-900 font-semibold'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'
              ]"
            >
              <div v-if="loadingPage === page" class="absolute inset-0 flex items-center justify-center">
                <div class="animate-spin h-6 w-6 border-2 border-primary-300 rounded-full border-t-transparent opacity-40"></div>
              </div>
              <span class="relative z-10">{{ page }}</span>
            </button>
            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400">
              ...
            </span>
            <button
              @click="changePage(pagination.last_page)"
              class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 rounded-md transition-colors"
            >
              <div v-if="loadingPage === pagination.last_page" class="absolute inset-0 flex items-center justify-center">
                <div class="animate-spin h-6 w-6 border-2 border-primary-300 rounded-full border-t-transparent opacity-40"></div>
              </div>
              <span class="relative z-10">{{ pagination.last_page }}</span>
            </button>
          </template>
          
          <template v-else-if="currentPage >= pagination.last_page - 3">
            <!-- 當前頁在後面時 -->
            <button
              @click="changePage(1)"
              class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 rounded-md transition-colors"
            >
              <div v-if="loadingPage === 1" class="absolute inset-0 flex items-center justify-center">
                <div class="animate-spin h-6 w-6 border-2 border-primary-300 rounded-full border-t-transparent opacity-40"></div>
              </div>
              <span class="relative z-10">1</span>
            </button>
            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400">
              ...
            </span>
            <button
              v-for="page in 5"
              :key="page"
              @click="changePage(pagination.last_page - 5 + page)"
              :class="[
                'relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium rounded-md transition-colors',
                (pagination.last_page - 5 + page) === currentPage
                  ? 'bg-primary-200 text-primary-900 font-semibold'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'
              ]"
            >
              <div v-if="loadingPage === pagination.last_page - 5 + page" class="absolute inset-0 flex items-center justify-center">
                <div class="animate-spin h-6 w-6 border-2 border-primary-300 rounded-full border-t-transparent opacity-40"></div>
              </div>
              <span class="relative z-10">{{ pagination.last_page - 5 + page }}</span>
            </button>
          </template>
          
          <template v-else>
            <!-- 當前頁在中間時 -->
            <button
              @click="changePage(1)"
              class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 rounded-md transition-colors"
            >
              <div v-if="loadingPage === 1" class="absolute inset-0 flex items-center justify-center">
                <div class="animate-spin h-6 w-6 border-2 border-primary-300 rounded-full border-t-transparent opacity-40"></div>
              </div>
              <span class="relative z-10">1</span>
            </button>
            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400">
              ...
            </span>
            <button
              v-for="page in [currentPage - 1, currentPage, currentPage + 1]"
              :key="page"
              @click="changePage(page)"
              :class="[
                'relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium rounded-md transition-colors',
                page === currentPage
                  ? 'bg-primary-200 text-primary-900 font-semibold'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'
              ]"
            >
              <div v-if="loadingPage === page" class="absolute inset-0 flex items-center justify-center">
                <div class="animate-spin h-6 w-6 border-2 border-primary-300 rounded-full border-t-transparent opacity-40"></div>
              </div>
              <span class="relative z-10">{{ page }}</span>
            </button>
            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400">
              ...
            </span>
            <button
              @click="changePage(pagination.last_page)"
              class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 rounded-md transition-colors"
            >
              <div v-if="loadingPage === pagination.last_page" class="absolute inset-0 flex items-center justify-center">
                <div class="animate-spin h-6 w-6 border-2 border-primary-300 rounded-full border-t-transparent opacity-40"></div>
              </div>
              <span class="relative z-10">{{ pagination.last_page }}</span>
            </button>
          </template>
        </template>
        
        <!-- 下一頁 -->
        <button
          @click="changePage(currentPage + 1)"
          :disabled="currentPage >= pagination.last_page"
          class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-gray-400 transition-colors"
        >
          <div v-if="loadingPage === currentPage + 1" class="absolute inset-0 flex items-center justify-center">
            <div class="animate-spin h-6 w-6 border-2 border-primary-300 rounded-full border-t-transparent opacity-40"></div>
          </div>
          <svg class="relative z-10 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
        </nav>
        
        <!-- 右側：總筆數資訊 -->
        <div class="text-sm text-gray-600">
          總共 {{ pagination.total }} 筆
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
    },
    isLoading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['page-changed', 'per-page-changed'],
  data() {
    return {
      showPerPageDropdown: false,
      loadingPage: null
    }
  },
  computed: {
    currentPage() {
      return this.pagination.current_page
    }
  },
  watch: {
    isLoading(newVal) {
      if (!newVal) {
        this.loadingPage = null
      }
    }
  },
  methods: {
    selectPerPage(value) {
      this.$emit('per-page-changed', value)
      this.showPerPageDropdown = false
    },
    changePage(page) {
      this.loadingPage = page
      this.$emit('page-changed', page)
    },
    handleClickOutside(event) {
      // 檢查點擊是否在下拉按鈕或下拉選單之外
      const dropdownButton = this.$el.querySelector('[data-dropdown-button]')
      const dropdownMenu = this.$el.querySelector('[data-dropdown-menu]')
      
      if (dropdownButton && !dropdownButton.contains(event.target) && 
          (!dropdownMenu || !dropdownMenu.contains(event.target))) {
        this.showPerPageDropdown = false
      }
    },
    handleEscKey(event) {
      if (event.key === 'Escape' && this.showPerPageDropdown) {
        this.showPerPageDropdown = false
      }
    }
  },
  mounted() {
    // 點擊外部關閉下拉選單
    document.addEventListener('click', this.handleClickOutside)
    // ESC 鍵關閉下拉選單
    document.addEventListener('keydown', this.handleEscKey)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
    document.removeEventListener('keydown', this.handleEscKey)
  }
}
</script>