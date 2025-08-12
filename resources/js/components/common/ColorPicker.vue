<template>
  <div class="color-picker" ref="colorPickerRef">
    <!-- 顏色預覽按鈕 -->
    <button
      type="button"
      @click="togglePicker"
      :style="{ backgroundColor: modelValue || '#6366f1' }"
      class="w-8 h-8 rounded border-2 border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
    />
    
    <!-- 顏色選擇面板 -->
    <Teleport to="body">
      <div 
        v-if="isOpen" 
        ref="colorPanel"
        class="fixed z-[10000] mt-2 p-4 pt-5 bg-white border border-gray-200 rounded-lg shadow-xl min-w-[280px]"
        :style="panelPosition"
        @click.stop
      >
        <!-- 關閉按鈕 -->
        <button
          @click="closePicker"
          class="absolute top-2 right-2 w-6 h-6 text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center"
          title="關閉"
        >
          <i class="bi bi-x-lg"></i>
        </button>
        
        <!-- 預設色彩調色盤 -->
        <div class="mb-4">
          <h4 class="text-sm font-medium text-gray-700 mb-2">預設顏色</h4>
          <div class="grid grid-cols-8 gap-2">
            <button
              v-for="color in defaultColors"
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
        <div class="mb-4">
          <h4 class="text-sm font-medium text-gray-700 mb-2">淡色系</h4>
          <div class="grid grid-cols-8 gap-2">
            <button
              v-for="color in lightColors"
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
              @input="handleColorInput"
              @change="handleColorInput"
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
    </Teleport>
    
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'

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
    const colorPickerRef = ref(null)
    const panelPosition = ref({ top: '0px', left: '0px' })
    
    // 預設顏色調色盤
    const defaultColors = [
      { value: '#ef4444', name: '紅色 Red' },
      { value: '#f97316', name: '橙色 Orange' },
      { value: '#f59e0b', name: '黃色 Amber' },
      { value: '#eab308', name: '黃綠色 Yellow' },
      { value: '#84cc16', name: '萊色 Lime' },
      { value: '#22c55e', name: '綠色 Green' },
      { value: '#10b981', name: '翠綠色 Emerald' },
      { value: '#14b8a6', name: '青綠色 Teal' },
      { value: '#06b6d4', name: '青色 Cyan' },
      { value: '#0ea5e9', name: '天空藍 Sky Blue' },
      { value: '#3b82f6', name: '藍色 Blue' },
      { value: '#6366f1', name: '靛藍色 Indigo' },
      { value: '#8b5cf6', name: '紫羅蘭 Violet' },
      { value: '#a855f7', name: '紫色 Purple' },
      { value: '#d946ef', name: '紫紅色 Fuchsia' },
      { value: '#ec4899', name: '桃紅色 Pink' }
    ]
    
    // 淡色系調色盤
    const lightColors = [
      { value: '#fef2f2', name: '淡紅色 Light Red' },
      { value: '#fff7ed', name: '淡橙色 Light Orange' },
      { value: '#fffbeb', name: '淡黃色 Light Amber' },
      { value: '#fefce8', name: '淡黃綠色 Light Yellow' },
      { value: '#f7fee7', name: '淡萊色 Light Lime' },
      { value: '#f0fdf4', name: '淡綠色 Light Green' },
      { value: '#ecfdf5', name: '淡翠綠色 Light Emerald' },
      { value: '#f0fdfa', name: '淡青綠色 Light Teal' },
      { value: '#ecfeff', name: '淡青色 Light Cyan' },
      { value: '#f0f9ff', name: '淡天空藍 Light Sky' },
      { value: '#eff6ff', name: '淡藍色 Light Blue' },
      { value: '#eef2ff', name: '淡靛藍色 Light Indigo' },
      { value: '#f5f3ff', name: '淡紫羅蘭 Light Violet' },
      { value: '#faf5ff', name: '淡紫色 Light Purple' },
      { value: '#fdf4ff', name: '淡紫紅色 Light Fuchsia' },
      { value: '#fdf2f8', name: '淡桃紅色 Light Pink' }
    ]
    
    const calculatePosition = () => {
      if (!colorPickerRef.value) return
      
      const rect = colorPickerRef.value.getBoundingClientRect()
      const viewportHeight = window.innerHeight
      const viewportWidth = window.innerWidth
      
      // 彈窗預設尺寸
      const panelWidth = 280
      const panelHeight = 400
      
      let top = rect.bottom + 5
      let left = rect.left
      
      // 優先顯示在下方，只有在下方空間真的不足時才顯示在上方
      const spaceBelow = viewportHeight - rect.bottom
      const spaceAbove = rect.top
      
      if (spaceBelow < panelHeight && spaceAbove > spaceBelow) {
        // 只有當上方空間比下方多時才顯示在上方
        top = rect.top - panelHeight - 5
      } else if (spaceBelow < panelHeight) {
        // 如果下方空間不足但仍要顯示在下方，調整高度
        top = rect.bottom + 5
      }
      
      // 檢查是否超出視窗右邊
      if (left + panelWidth > viewportWidth) {
        left = viewportWidth - panelWidth - 10
      }
      
      // 檢查是否超出視窗左邊
      if (left < 10) {
        left = 10
      }
      
      panelPosition.value = {
        top: `${top}px`,
        left: `${left}px`
      }
    }
    
    const togglePicker = async () => {
      isOpen.value = !isOpen.value
      if (isOpen.value) {
        await nextTick()
        calculatePosition()
      }
    }
    
    const closePicker = () => {
      isOpen.value = false
    }
    
    const selectColor = (color) => {
      emit('update:modelValue', color)
      closePicker()
    }
    
    const handleColorInput = (event) => {
      // 只更新值，不關閉選擇器，讓使用者可以拖曳調色
      emit('update:modelValue', event.target.value)
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
    
    const clearColor = () => {
      emit('update:modelValue', '')
      closePicker()
    }
    
    // 點擊外部關閉
    const handleClickOutside = (event) => {
      // 檢查是否點擊在 ColorPicker 內部
      const isInsideColorPicker = colorPanel.value && colorPanel.value.contains(event.target)
      const isColorPickerButton = colorPickerRef.value && colorPickerRef.value.contains(event.target)
      
      // 檢查是否點擊在 IconPicker 內部
      const iconPickerPanel = document.querySelector('.icon-picker .fixed.z-\\[9999\\]')
      const isInsideIconPicker = iconPickerPanel && iconPickerPanel.contains(event.target)
      
      // 如果點擊在 IconPicker 內部或外部（但不在 ColorPicker 內部），則關閉 ColorPicker
      if (!isInsideColorPicker && !isColorPickerButton) {
        closePicker()
      }
    }
    
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
      window.addEventListener('resize', () => {
        if (isOpen.value) calculatePosition()
      })
      window.addEventListener('scroll', () => {
        if (isOpen.value) calculatePosition()
      })
    })
    
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
      window.removeEventListener('resize', calculatePosition)
      window.removeEventListener('scroll', calculatePosition)
    })
    
    return {
      isOpen,
      colorPanel,
      colorPickerRef,
      panelPosition,
      defaultColors,
      lightColors,
      togglePicker,
      closePicker,
      selectColor,
      handleColorInput,
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
</style>