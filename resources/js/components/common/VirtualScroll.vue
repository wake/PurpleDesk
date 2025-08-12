<template>
  <div 
    ref="scrollContainer"
    class="virtual-scroll-container px-1"
    :style="{ height: containerHeight + 'px' }"
    @scroll="handleScroll"
  >
    <div 
      class="virtual-scroll-spacer"
      :style="{ height: totalHeight + 'px' }"
    >
      <div
        class="virtual-scroll-content"
        :style="{ transform: `translateY(${offsetY}px)` }"
      >
        <div 
          v-for="(row, index) in visibleRows"
          :key="startIndex + index"
          class="grid-row"
          :class="{
            'first-row': startIndex + index === 0,
            'last-row': startIndex + index === totalRows - 1
          }"
        >
          <slot 
            name="row"
            :items="row"
            :rowIndex="startIndex + index"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

export default {
  name: 'VirtualScroll',
  props: {
    items: {
      type: Array,
      required: true
    },
    itemsPerRow: {
      type: Number,
      default: 6
    },
    rowHeight: {
      type: Number,
      default: 48
    },
    containerHeight: {
      type: Number,
      default: 192
    },
    buffer: {
      type: Number,
      default: 2
    }
  },
  setup(props) {
    const scrollContainer = ref(null)
    const scrollTop = ref(0)
    
    // 計算總行數
    const totalRows = computed(() => 
      Math.ceil(props.items.length / props.itemsPerRow)
    )
    
    // 計算總高度
    const totalHeight = computed(() => 
      totalRows.value * props.rowHeight
    )
    
    // 計算可見行數
    const visibleRowCount = computed(() => 
      Math.ceil(props.containerHeight / props.rowHeight) + props.buffer * 2
    )
    
    // 計算起始索引
    const startIndex = computed(() => {
      const start = Math.floor(scrollTop.value / props.rowHeight) - props.buffer
      return Math.max(0, start)
    })
    
    // 計算結束索引
    const endIndex = computed(() => {
      const end = startIndex.value + visibleRowCount.value
      return Math.min(totalRows.value, end)
    })
    
    // 計算偏移量
    const offsetY = computed(() => 
      startIndex.value * props.rowHeight
    )
    
    // 將項目分組為行
    const groupItemsIntoRows = (items, itemsPerRow) => {
      const rows = []
      for (let i = 0; i < items.length; i += itemsPerRow) {
        rows.push(items.slice(i, i + itemsPerRow))
      }
      return rows
    }
    
    // 所有行
    const allRows = computed(() => 
      groupItemsIntoRows(props.items, props.itemsPerRow)
    )
    
    // 可見行
    const visibleRows = computed(() => 
      allRows.value.slice(startIndex.value, endIndex.value)
    )
    
    // 處理滾動
    const handleScroll = (e) => {
      scrollTop.value = e.target.scrollTop
    }
    
    // 監聽項目變化，重置滾動位置
    watch(() => props.items, () => {
      if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0
        scrollTop.value = 0
      }
    })
    
    return {
      scrollContainer,
      totalHeight,
      totalRows,
      offsetY,
      visibleRows,
      startIndex,
      handleScroll
    }
  }
}
</script>

<style scoped>
.virtual-scroll-container {
  overflow-y: auto;
  position: relative;
  padding: 0.25rem 0.5rem 0.25rem 0.5rem; /* 右側較少內邊距 */
  border-radius: 6px;
}

/* Webkit 瀏覽器的滾動條樣式 (Chrome, Safari, Edge) */
.virtual-scroll-container::-webkit-scrollbar {
  width: 6px;
}

.virtual-scroll-container::-webkit-scrollbar-track {
  background: transparent;
  border-radius: 3px;
  margin: 2px 0; /* 上下留白 */
}

.virtual-scroll-container::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
  border: 1px solid #f1f5f9; /* 細邊框 */
}

.virtual-scroll-container::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.virtual-scroll-container::-webkit-scrollbar-corner {
  background: transparent;
}

/* Firefox 的滾動條樣式 */
.virtual-scroll-container {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}

/* 確保在所有瀏覽器中都有一致的外觀 */
.virtual-scroll-container {
  /* 強制顯示滾動條，但只在需要時 */
  overflow-y: auto;
  /* 防止水平滾動 */
  overflow-x: hidden;
}

.virtual-scroll-spacer {
  position: relative;
}

.virtual-scroll-content {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
}

.grid-row {
  display: grid;
  grid-template-columns: repeat(10, 1fr);
  gap: 2px;
  padding: 2px 0;
}

.grid-row button {
  width: 30px;
  height: 30px;
}
</style>