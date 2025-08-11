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
      <!-- 預設色彩調色盤 -->
      <div class="mb-4">
        <h4 class="text-sm font-medium text-gray-700 mb-2">預設顏色</h4>
        <div class="grid grid-cols-8 gap-2">
          <button
            v-for="color in defaultColors"
            :key="color"
            @click="selectColor(color)"
            :style="{ backgroundColor: color }"
            class="w-6 h-6 rounded border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-primary-500 transition-all"
            :class="{ 'ring-2 ring-primary-500': modelValue === color }"
          />
        </div>
      </div>
      
      <!-- 淡色系調色盤 -->
      <div class="mb-4">
        <h4 class="text-sm font-medium text-gray-700 mb-2">淡色系 (推薦)</h4>
        <div class="grid grid-cols-8 gap-2">
          <button
            v-for="color in lightColors"
            :key="color"
            @click="selectColor(color)"
            :style="{ backgroundColor: color }"
            class="w-6 h-6 rounded border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-primary-500 transition-all"
            :class="{ 'ring-2 ring-primary-500': modelValue === color }"
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
import { ref, onMounted, onUnmounted } from 'vue'

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
    
    // 預設顏色調色盤
    const defaultColors = [
      '#ef4444', '#f97316', '#f59e0b', '#eab308', 
      '#84cc16', '#22c55e', '#10b981', '#14b8a6',
      '#06b6d4', '#0ea5e9', '#3b82f6', '#6366f1',
      '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'
    ]
    
    // 淡色系調色盤 (適合做背景)
    const lightColors = [
      '#fef2f2', '#fff7ed', '#fffbeb', '#fefce8',
      '#f7fee7', '#f0fdf4', '#ecfdf5', '#f0fdfa',
      '#ecfeff', '#f0f9ff', '#eff6ff', '#eef2ff',
      '#f5f3ff', '#faf5ff', '#fdf4ff', '#fdf2f8',
      '#fce7f3', '#fed7e2', '#fde2e7', '#fee2e2',
      '#fed7d7', '#fecaca', '#fca5a5', '#f87171'
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
      selectColor(randomColor)
    }
    
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
      defaultColors,
      lightColors,
      togglePicker,
      closePicker,
      selectColor,
      handleTextInput,
      selectRandomColor,
      clearColor
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