<template>
  <div class="color-picker" :class="{ 'color-picker-open': isOpen }">
    <!-- 顏色預覽按鈕 -->
    <button
      type="button"
      @click="togglePicker"
      :style="{ backgroundColor: modelValue }"
      class="w-8 h-8 rounded border-2 border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
      :class="[
        'relative overflow-hidden',
        !modelValue ? 'bg-gray-100' : ''
      ]"
    >
      <!-- 透明圖案 (當沒有顏色時) -->
      <div 
        v-if="!modelValue" 
        class="absolute inset-0 bg-gradient-to-br from-red-500 via-transparent to-red-500 opacity-20"
      />
    </button>
    
    <!-- 顏色選擇面板 -->
    <div 
      v-if="isOpen" 
      ref="colorPanel"
      class="absolute z-50 mt-2 p-4 bg-white border border-gray-200 rounded-lg shadow-lg min-w-[280px]"
      @click.stop
    >
      <!-- 色彩搜尋欄位 -->
      <div class="mb-4">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="搜尋顏色名稱或輸入 Hex 值..."
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
      
      <!-- 預設色彩調色盤 -->
      <div class="mb-4" v-show="!searchQuery || showDefaultColors">
        <h4 class="text-sm font-medium text-gray-700 mb-2">預設顏色</h4>
        <div class="grid grid-cols-8 gap-2">
          <button
            v-for="color in filteredDefaultColors"
            :key="color.value"
            @click="selectColor(color.value)"
            :style="{ backgroundColor: color.value }"
            class="w-6 h-6 rounded border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-primary-500 transition-all"
            :class="{ 'ring-2 ring-primary-500': modelValue === color.value }"
            :title="color.name"
          />
        </div>
      </div>
      
      <!-- 淡色系調色盤 -->
      <div class="mb-4" v-show="!searchQuery || showLightColors">
        <h4 class="text-sm font-medium text-gray-700 mb-2">淡色系 (推薦)</h4>
        <div class="grid grid-cols-8 gap-2">
          <button
            v-for="color in filteredLightColors"
            :key="color.value"
            @click="selectColor(color.value)"
            :style="{ backgroundColor: color.value }"
            class="w-6 h-6 rounded border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-primary-500 transition-all"
            :class="{ 'ring-2 ring-primary-500': modelValue === color.value }"
            :title="color.name"
          />
        </div>
      </div>
      
      <!-- HTML 色彩輸入 -->
      <div class="mb-4">
        <h4 class="text-sm font-medium text-gray-700 mb-2">自訂顏色</h4>
        <div class="flex space-x-2">
          <input
            type="color"
            :value="modelValue || '#6366f1'"
            @input="selectColor($event.target.value)"
            class="w-10 h-8 border border-gray-300 rounded cursor-pointer"
          />
          <input
            type="text"
            :value="modelValue || ''"
            @input="handleTextInput"
            placeholder="#6366f1"
            class="flex-1 px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
          />
        </div>
      </div>
      
      <!-- 搜尋結果提示 -->
      <div v-if="searchQuery && !showDefaultColors && !showLightColors && !isValidHexColor" class="mb-4 text-center py-4 text-gray-500">
        <p class="text-sm">找不到符合的顏色</p>
        <p class="text-xs text-gray-400 mt-1">請嘗試輸入 Hex 顏色碼（如 #ff6b6b）</p>
      </div>
      
      <!-- Hex 顏色預覽 -->
      <div v-if="searchQuery && isValidHexColor" class="mb-4">
        <h4 class="text-sm font-medium text-gray-700 mb-2">預覽顏色</h4>
        <button
          @click="selectColor(searchQuery)"
          :style="{ backgroundColor: searchQuery }"
          class="w-full h-10 rounded border-2 border-gray-300 hover:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all flex items-center justify-center"
        >
          <span class="text-white text-sm font-medium drop-shadow-md">點擊使用 {{ searchQuery.toUpperCase() }}</span>
        </button>
      </div>
      
      <!-- 隨機顏色按鈕 -->
      <div class="flex space-x-2">
        <button
          @click="selectRandomColor"
          class="flex-1 px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
        >
          🎲 隨機淡色
        </button>
        <button
          @click="clearColor"
          class="px-3 py-2 text-sm bg-red-50 hover:bg-red-100 text-red-600 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-red-500"
        >
          清除
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
import { ref, computed, onMounted, onUnmounted } from 'vue'

export default {
  name: 'ColorPicker',
  props: {
    modelValue: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const isOpen = ref(false)
    const colorPanel = ref(null)
    const searchQuery = ref('')
    
    // 預設顏色調色盤
    const defaultColors = [
      { value: '#ef4444', name: '紅色 Red' },
      { value: '#f97316', name: '橙色 Orange' },
      { value: '#f59e0b', name: '黃色 Amber' },
      { value: '#eab308', name: '黃綠色 Yellow' },
      { value: '#84cc16', name: '蔀色 Lime' },
      { value: '#22c55e', name: '綠色 Green' },
      { value: '#10b981', name: '翠綠色 Emerald' },
      { value: '#14b8a6', name: '青綠色 Teal' },
      { value: '#06b6d4', name: '青色 Cyan' },
      { value: '#0ea5e9', name: '天空藍 Sky Blue' },
      { value: '#3b82f6', name: '藍色 Blue' },
      { value: '#6366f1', name: '靈藍色 Indigo' },
      { value: '#8b5cf6', name: '紫羅蘭 Violet' },
      { value: '#a855f7', name: '紫色 Purple' },
      { value: '#d946ef', name: '紫紅色 Fuchsia' },
      { value: '#ec4899', name: '桃紅色 Pink' }
    ]
    
    // 淡色系調色盤 (適合做背景)
    const lightColors = [
      { value: '#fef2f2', name: '淡紅色 Light Red' },
      { value: '#fff7ed', name: '淡橙色 Light Orange' },
      { value: '#fffbeb', name: '淡黃色 Light Amber' },
      { value: '#fefce8', name: '淡黃綠色 Light Yellow' },
      { value: '#f7fee7', name: '淡蔀色 Light Lime' },
      { value: '#f0fdf4', name: '淡綠色 Light Green' },
      { value: '#ecfdf5', name: '淡翠綠色 Light Emerald' },
      { value: '#f0fdfa', name: '淡青綠色 Light Teal' },
      { value: '#ecfeff', name: '淡青色 Light Cyan' },
      { value: '#f0f9ff', name: '淡天空藍 Light Sky' },
      { value: '#eff6ff', name: '淡藍色 Light Blue' },
      { value: '#eef2ff', name: '淡靈藍色 Light Indigo' },
      { value: '#f5f3ff', name: '淡紫羅蘭 Light Violet' },
      { value: '#faf5ff', name: '淡紫色 Light Purple' },
      { value: '#fdf4ff', name: '淡紫紅色 Light Fuchsia' },
      { value: '#fdf2f8', name: '淡桃紅色 Light Pink' },
      { value: '#f8fafc', name: '灰白色 Slate' },
      { value: '#f9fafb', name: '灰色 Gray' },
      { value: '#fafaf9', name: '石色 Stone' },
      { value: '#fefefe', name: '中性色 Neutral' },
      { value: '#fdfdfc', name: '锆色 Zinc' },
      { value: '#f7f7f7', name: '暖灰色 Warm Gray' }
    ]
    
    const togglePicker = () => {
      isOpen.value = !isOpen.value
    }
    
    const closePicker = () => {
      isOpen.value = false
    }
    
    const selectColor = (color) => {
      emit('update:modelValue', color)
      closePicker()
    }
    
    const handleTextInput = (event) => {
      const value = event.target.value
      if (value.match(/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/)) {
        emit('update:modelValue', value)
      }
    }
    
    const selectRandomColor = () => {
      const randomColor = lightColors[Math.floor(Math.random() * lightColors.length)]
      selectColor(randomColor.value)
    }
    
    const clearSearch = () => {
      searchQuery.value = ''
    }
    
    // 檢查是否為有效的 Hex 顏色
    const isValidHexColor = computed(() => {
      if (!searchQuery.value) return false
      const hex = searchQuery.value.toLowerCase().trim()
      return /^#([a-f0-9]{3}|[a-f0-9]{6})$/.test(hex)
    })
    
    // 築選後的顏色列表
    const filteredDefaultColors = computed(() => {
      if (!searchQuery.value) return defaultColors
      const query = searchQuery.value.toLowerCase()
      return defaultColors.filter(color => 
        color.name.toLowerCase().includes(query) ||
        color.value.toLowerCase().includes(query)
      )
    })
    
    const filteredLightColors = computed(() => {
      if (!searchQuery.value) return lightColors
      const query = searchQuery.value.toLowerCase()
      return lightColors.filter(color => 
        color.name.toLowerCase().includes(query) ||
        color.value.toLowerCase().includes(query)
      )
    })
    
    // 是否顯示各個色盤區域
    const showDefaultColors = computed(() => {
      return !searchQuery.value || filteredDefaultColors.value.length > 0
    })
    
    const showLightColors = computed(() => {
      return !searchQuery.value || filteredLightColors.value.length > 0
    })
    
    const clearColor = () => {
      emit('update:modelValue', '')
      closePicker()
    }
    
    // 點擊外部關閉
    const handleClickOutside = (event) => {
      if (colorPanel.value && !colorPanel.value.contains(event.target)) {
        closePicker()
      }
    }
    
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
    })
    
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })
    
    return {
      isOpen,
      colorPanel,
      searchQuery,
      defaultColors,
      lightColors,
      filteredDefaultColors,
      filteredLightColors,
      showDefaultColors,
      showLightColors,
      isValidHexColor,
      togglePicker,
      closePicker,
      selectColor,
      handleTextInput,
      selectRandomColor,
      clearColor,
      clearSearch
    }
  }
}
</script>

<style scoped>
.color-picker {
  @apply relative inline-block;
}

.color-picker-open .absolute {
  @apply block;
}
</style>