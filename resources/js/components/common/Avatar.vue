<template>
  <div
    :class="[
      'inline-flex items-center justify-center rounded-full select-none',
      sizeClasses,
      'overflow-hidden flex-shrink-0'
    ]"
    :style="containerStyles"
    :title="title"
  >
    <!-- 圖片頭像 -->
    <img
      v-if="avatarData?.type === 'image' && imageUrl"
      :src="imageUrl"
      :alt="title"
      class="w-full h-full object-cover"
      @error="onImageError"
    />
    
    <!-- 文字頭像 -->
    <span
      v-else-if="avatarData?.type === 'text'"
      :class="textSizeClasses"
      :style="textStyles"
      class="font-semibold leading-none"
    >
      {{ avatarData.text }}
    </span>
    
    <!-- Emoji 頭像 -->
    <span
      v-else-if="avatarData?.type === 'emoji'"
      :class="textSizeClasses"
      class="leading-none"
    >
      {{ avatarData.emoji }}
    </span>
    
    <!-- Hero Icon 頭像 -->
    <component
      v-else-if="avatarData?.type === 'hero_icon' && heroIconComponent"
      :is="heroIconComponent"
      :class="iconSizeClasses"
      :style="iconStyles"
    />
    
    <!-- Bootstrap Icon 頭像 -->
    <i
      v-else-if="avatarData?.type === 'bootstrap_icon'"
      :class="[
        `bi-${avatarData.icon}${avatarData.style === 'fill' ? '-fill' : ''}`,
        iconSizeClasses
      ]"
      :style="iconStyles"
    ></i>
    
    <!-- 預設頭像（當沒有有效頭像時） -->
    <i
      v-else
      :class="['bi-person-fill', iconSizeClasses]"
      style="color: #9ca3af;"
    ></i>
  </div>
</template>

<script>
import { computed, ref, onMounted } from 'vue'

export default {
  name: 'Avatar',
  props: {
    /**
     * 頭像數據物件
     * {
     *   type: 'image' | 'text' | 'emoji' | 'hero_icon' | 'bootstrap_icon',
     *   // 依據類型有不同的屬性
     * }
     */
    avatarData: {
      type: Object,
      default: null
    },
    
    /**
     * 頭像大小
     */
    size: {
      type: String,
      default: 'md',
      validator: value => ['xs', 'sm', 'md', 'lg', 'xl', '2xl'].includes(value)
    },
    
    /**
     * 顯示標題（hover 提示）
     */
    title: {
      type: String,
      default: ''
    },
    
    /**
     * 強制設定背景顏色
     */
    backgroundColor: {
      type: String,
      default: null
    }
  },
  
  setup(props) {
    const imageError = ref(false)
    const heroIconComponent = ref(null)
    
    // 大小相關的 CSS 類別
    const sizeClasses = computed(() => {
      const sizeMap = {
        'xs': 'w-6 h-6',
        'sm': 'w-8 h-8',
        'md': 'w-10 h-10',
        'lg': 'w-12 h-12',
        'xl': 'w-16 h-16',
        '2xl': 'w-20 h-20'
      }
      return sizeMap[props.size] || sizeMap.md
    })
    
    // 文字大小類別
    const textSizeClasses = computed(() => {
      const textSizeMap = {
        'xs': 'text-xs',
        'sm': 'text-sm', 
        'md': 'text-base',
        'lg': 'text-lg',
        'xl': 'text-xl',
        '2xl': 'text-2xl'
      }
      return textSizeMap[props.size] || textSizeMap.md
    })
    
    // 圖標大小類別
    const iconSizeClasses = computed(() => {
      const iconSizeMap = {
        'xs': 'text-xs',
        'sm': 'text-sm',
        'md': 'text-base', 
        'lg': 'text-lg',
        'xl': 'text-xl',
        '2xl': 'text-2xl'
      }
      return iconSizeMap[props.size] || iconSizeMap.md
    })
    
    // 容器樣式
    const containerStyles = computed(() => {
      const styles = {}
      
      // 背景顏色（優先級：props > avatarData）
      const bgColor = props.backgroundColor || props.avatarData?.backgroundColor
      if (bgColor) {
        styles.backgroundColor = bgColor
      } else {
        // 預設淡灰色背景
        styles.backgroundColor = '#f3f4f6'
      }
      
      return styles
    })
    
    // 文字樣式
    const textStyles = computed(() => {
      const styles = {}
      if (props.avatarData?.textColor) {
        styles.color = props.avatarData.textColor
      }
      return styles
    })
    
    // 圖標樣式
    const iconStyles = computed(() => {
      const styles = {}
      if (props.avatarData?.iconColor) {
        styles.color = props.avatarData.iconColor
      }
      return styles
    })
    
    // 圖片 URL
    const imageUrl = computed(() => {
      if (imageError.value) return null
      
      if (props.avatarData?.type === 'image') {
        if (props.avatarData.path) {
          // 本地圖片路徑
          return `/storage/${props.avatarData.path}`
        }
        if (props.avatarData.url) {
          // 外部圖片 URL
          return props.avatarData.url
        }
      }
      
      return null
    })
    
    // 處理圖片載入失敗
    const onImageError = () => {
      imageError.value = true
    }
    
    // 動態載入 Hero Icon 組件
    const loadHeroIcon = async () => {
      if (props.avatarData?.type !== 'hero_icon' || !props.avatarData?.icon) {
        return
      }
      
      try {
        const iconName = props.avatarData.icon
        const style = props.avatarData.style === 'solid' ? 'solid' : 'outline'
        
        // 動態載入 Hero Icon
        const iconModule = await import(`@heroicons/vue/${style}`)
        
        // 轉換圖標名稱格式 (user -> UserIcon)
        const componentName = iconName
          .split('-')
          .map(part => part.charAt(0).toUpperCase() + part.slice(1))
          .join('') + 'Icon'
        
        heroIconComponent.value = iconModule[componentName]
      } catch (error) {
        console.warn('Failed to load hero icon:', props.avatarData.icon, error)
      }
    }
    
    // 監聽頭像數據變化
    onMounted(() => {
      loadHeroIcon()
    })
    
    return {
      imageError,
      heroIconComponent,
      sizeClasses,
      textSizeClasses,
      iconSizeClasses,
      containerStyles,
      textStyles,
      iconStyles,
      imageUrl,
      onImageError
    }
  }
}
</script>

<style scoped>
/* 確保頭像容器保持正圓形 */
.rounded-full {
  aspect-ratio: 1 / 1;
}
</style>