<template>
  <div class="flex flex-col items-center justify-center" :class="containerClass">
    <svg class="animate-spin text-primary-600" :class="sizeClass" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <p v-if="showText" class="text-gray-500 mt-2" :class="textSizeClass">{{ text }}</p>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    text: {
      type: String,
      default: '載入中...'
    },
    showText: {
      type: Boolean,
      default: true
    },
    containerClass: {
      type: String,
      default: 'py-12'
    }
  },
  computed: {
    sizeClass() {
      const sizes = {
        sm: 'h-4 w-4',
        md: 'h-8 w-8',
        lg: 'h-12 w-12',
        xl: 'h-16 w-16'
      }
      return sizes[this.size] || sizes.md
    },
    textSizeClass() {
      const sizes = {
        sm: 'text-xs',
        md: 'text-sm',
        lg: 'text-base',
        xl: 'text-lg'
      }
      return sizes[this.size] || sizes.md
    }
  }
}
</script>