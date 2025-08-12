<template>
  <div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-2xl mx-auto px-4">
      <h1 class="text-2xl font-bold text-gray-900 mb-8">測試圖標選擇器</h1>
      
      <!-- 直接使用 IconPicker -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">IconPicker 測試</h2>
        <div class="flex items-center space-x-4">
          <label class="text-sm font-medium text-gray-700">選擇圖標：</label>
          <IconPicker 
            v-model="selectedIcon"
            v-model:iconType="selectedIconType"
          />
          <div v-if="selectedIcon" class="ml-4">
            <p class="text-sm text-gray-600">
              選中: {{ selectedIcon }} ({{ selectedIconType }})
            </p>
          </div>
        </div>
      </div>

      <!-- 測試 ImageSelector -->
      <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">ImageSelector 測試</h2>
        <ImageSelector
          :current-image-url="null"
          :initials="'TP'"
          size="large"
          shape="rounded"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import IconPicker from '../components/common/IconPicker.vue'
import ImageSelector from '../components/common/ImageSelector.vue'

export default {
  name: 'TestIconPicker',
  components: {
    IconPicker,
    ImageSelector
  },
  setup() {
    const selectedIcon = ref('')
    const selectedIconType = ref('')
    
    return {
      selectedIcon,
      selectedIconType
    }
  }
}
</script>