<template>
  <div class="flex items-center">
    <!-- 重疊頭像組 -->
    <div class="flex -space-x-0.5 overflow-hidden">
      <div
        v-for="(user, index) in visibleUsers"
        :key="user.id || index"
        :class="[
          'inline-block ring-2',
          ringColorClass
        ]"
      >
        <IconDisplay 
          :icon-data="user.avatar_data" 
          size="6" 
          :title="getUserDisplayName(user)"
        />
      </div>
    </div>
    
    <!-- 剩餘人數提示 -->
    <span v-if="remainingCount > 0" class="ml-2 text-xs text-gray-500">
      +{{ remainingCount }} {{ memberText }}
    </span>
    
    <!-- 無人員提示 -->
    <span v-else-if="!users || users.length === 0" class="text-xs text-gray-400">
      {{ emptyText }}
    </span>
  </div>
</template>

<script>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import IconDisplay from './IconDisplay.vue'

export default {
  name: 'UserAvatarGroup',
  components: {
    IconDisplay
  },
  props: {
    users: {
      type: Array,
      default: () => []
    },
    memberText: {
      type: String,
      default: '個成員'
    },
    emptyText: {
      type: String,
      default: '無成員'
    },
    maxVisible: {
      type: Number,
      default: null // 如果不指定，則使用響應式計算
    },
    theme: {
      type: String,
      default: 'default', // default, primary, admin
      validator: (value) => ['default', 'primary', 'admin'].includes(value)
    }
  },
  setup(props) {
    const screenWidth = ref(window.innerWidth)
    
    // 響應式計算可見成員數量
    const maxVisibleMembers = computed(() => {
      // 如果指定了固定數量，直接使用
      if (props.maxVisible !== null) {
        return props.maxVisible
      }
      
      // 否則根據螢幕寬度動態計算
      let baseMax = 3 // 最小顯示數量
      
      if (screenWidth.value >= 1920) baseMax = 8       // 2K 螢幕
      else if (screenWidth.value >= 1600) baseMax = 6  // 大桌面螢幕
      else if (screenWidth.value >= 1400) baseMax = 5  // 中等桌面螢幕
      else if (screenWidth.value >= 1200) baseMax = 4  // 小桌面螢幕
      else if (screenWidth.value >= 1024) baseMax = 4  // 筆電螢幕
      else if (screenWidth.value >= 768) baseMax = 3   // 平板螢幕
      
      // 如果成員數量少於或等於基準值，直接全部顯示
      return Math.min(baseMax, props.users?.length || 0)
    })
    
    const visibleUsers = computed(() => {
      if (!props.users || props.users.length === 0) return []
      return props.users.slice(0, maxVisibleMembers.value)
    })
    
    const remainingCount = computed(() => {
      if (!props.users) return 0
      return Math.max(0, props.users.length - maxVisibleMembers.value)
    })
    
    // 主題顏色計算屬性
    const ringColorClass = computed(() => {
      switch (props.theme) {
        case 'primary':
          return 'ring-primary-100'
        case 'admin':
          return 'ring-purple-100'
        default:
          return 'ring-white'
      }
    })
    
    const getUserDisplayName = (user) => {
      return user.display_name || user.full_name || user.name || '未知用戶'
    }
    
    // 視窗大小變化監聽
    const handleResize = () => {
      screenWidth.value = window.innerWidth
    }
    
    onMounted(() => {
      window.addEventListener('resize', handleResize)
    })
    
    onUnmounted(() => {
      window.removeEventListener('resize', handleResize)
    })
    
    return {
      visibleUsers,
      remainingCount,
      ringColorClass,
      getUserDisplayName
    }
  }
}
</script>