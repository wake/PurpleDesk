<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h1 class="text-2xl font-bold text-gray-900">Icon 系統測試與調整頁面</h1>
          <p class="mt-2 text-sm text-gray-600">
            測試所有 icon 類型在各種尺寸下的顯示效果，並即時調整尺寸比例
          </p>
        </div>

        <div class="p-6 space-y-8">
          
          <!-- 載入狀態 -->
          <div v-if="loading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>

          <!-- 尺寸調整控制台 -->
          <div v-else>
            <section class="space-y-6">
              <h2 class="text-xl font-semibold text-gray-900">尺寸調整控制台</h2>
              
              <!-- 基準設定 -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">基準設定 (背景 80x80px)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  
                  <!-- 文字調整 -->
                  <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">文字大小 (px)</label>
                    <input 
                      v-model.number="sizeConfig.text.fontSize" 
                      type="number" 
                      min="10" 
                      max="60" 
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                    <p class="text-xs text-gray-500">建議：中文 28px，拉丁語系可調大</p>
                  </div>
                  
                  <!-- Emoji 調整 -->
                  <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Emoji 大小 (rem)</label>
                    <input 
                      v-model.number="sizeConfig.emoji.fontSize" 
                      type="number" 
                      min="1" 
                      max="5" 
                      step="0.25" 
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                    <p class="text-xs text-gray-500">建議：2.75rem (44px)</p>
                  </div>
                  
                  <!-- Icon 調整 -->
                  <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Icon 大小 (rem)</label>
                    <input 
                      v-model.number="sizeConfig.icon.size" 
                      type="number" 
                      min="1" 
                      max="5" 
                      step="0.25" 
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                    <p class="text-xs text-gray-500">建議：2.75rem (44px)</p>
                  </div>
                </div>
                
                <!-- 動作按鈕 -->
                <div class="mt-4 flex space-x-3">
                  <button 
                    @click="resetToDefaults" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                  >
                    重設為預設值
                  </button>
                  <button 
                    @click="exportSizeConfig" 
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700"
                  >
                    匯出配置資料
                  </button>
                </div>
              </div>
            </section>
            
            <!-- 完整尺寸測試矩陣 -->
            <section class="space-y-6">
              <h2 class="text-xl font-semibold text-gray-900">完整尺寸測試矩陣</h2>
              
              <!-- 所有尺寸列表 -->
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-3">測試尺寸：</p>
                <div class="flex flex-wrap gap-2">
                  <span v-for="size in allSizes" :key="size" class="px-2 py-1 bg-white rounded text-xs font-mono">{{ size }}</span>
                </div>
              </div>
              
              <!-- 各類型完整測試 -->
              <div class="space-y-8">
                
                <!-- 文字類型測試 -->
                <div class="border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">文字類型 (最多2字)</h3>
                  <div class="space-y-4">
                    <div v-for="textExample in textExamples" :key="textExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ textExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <IconDisplay 
                            :icon-data="textExample.data" 
                            :size="size" 
                            :custom-config="getCurrentSizeConfig()"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Emoji 類型測試 -->
                <div class="border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Emoji 類型</h3>
                  <div class="space-y-4">
                    <div v-for="emojiExample in emojiExamples" :key="emojiExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ emojiExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <IconDisplay 
                            :icon-data="emojiExample.data" 
                            :size="size" 
                            :custom-config="getCurrentSizeConfig()"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Hero Icons 測試 -->
                <div class="border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Hero Icons</h3>
                  <div class="space-y-4">
                    <div v-for="heroExample in heroExamples" :key="heroExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ heroExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <IconDisplay 
                            :icon-data="heroExample.data" 
                            :size="size" 
                            :custom-config="getCurrentSizeConfig()"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Bootstrap Icons 測試 -->
                <div class="border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Bootstrap Icons</h3>
                  <div class="space-y-4">
                    <div v-for="bsExample in bsExamples" :key="bsExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ bsExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <IconDisplay 
                            :icon-data="bsExample.data" 
                            :size="size" 
                            :custom-config="getCurrentSizeConfig()"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- 圖片類型測試 -->
                <div class="border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">圖片類型</h3>
                  <div class="space-y-3">
                    <h4 class="text-sm font-medium text-gray-700">示例圖片 (placeholder)</h4>
                    <div class="flex flex-wrap gap-3 items-end">
                      <div v-for="size in allSizes" :key="size" class="text-center">
                        <IconDisplay 
                          :icon-data="imageExample.data" 
                          :size="size" 
                          :custom-config="getCurrentSizeConfig()"
                        />
                        <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            
            <!-- 現有資料測試 -->
            <section class="space-y-4">
              <h2 class="text-xl font-semibold text-gray-900">現有測試資料</h2>
              
              <!-- 用戶資料 -->
              <div class="space-y-3">
                <h3 class="text-lg font-medium text-gray-700">用戶頭像</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div 
                    v-for="user in users" 
                    :key="user.id"
                    class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg"
                  >
                    <div class="flex space-x-2">
                      <IconDisplay 
                        :icon-data="user.avatar_data" 
                        size="xs" 
                        :title="user.full_name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                      <IconDisplay 
                        :icon-data="user.avatar_data" 
                        size="sm" 
                        :title="user.full_name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                      <IconDisplay 
                        :icon-data="user.avatar_data" 
                        size="md" 
                        :title="user.full_name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                      <IconDisplay 
                        :icon-data="user.avatar_data" 
                        size="lg" 
                        :title="user.full_name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        {{ user.full_name }}
                      </p>
                      <p class="text-xs text-gray-500 truncate">
                        {{ getAvatarTypeDescription(user.avatar_data) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 組織資料 -->
              <div class="space-y-3">
                <h3 class="text-lg font-medium text-gray-700">組織頭像</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div 
                    v-for="org in organizations" 
                    :key="org.id"
                    class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg"
                  >
                    <div class="flex space-x-2">
                      <IconDisplay 
                        :icon-data="org.avatar_data" 
                        size="sm" 
                        :title="org.name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                      <IconDisplay 
                        :icon-data="org.avatar_data" 
                        size="md" 
                        :title="org.name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                      <IconDisplay 
                        :icon-data="org.avatar_data" 
                        size="lg" 
                        :title="org.name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                      <IconDisplay 
                        :icon-data="org.avatar_data" 
                        size="xl" 
                        :title="org.name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        {{ org.name }}
                      </p>
                      <p class="text-xs text-gray-500 truncate">
                        {{ getAvatarTypeDescription(org.avatar_data) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 團隊資料 -->
              <div class="space-y-3">
                <h3 class="text-lg font-medium text-gray-700">團隊頭像</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div 
                    v-for="team in teams" 
                    :key="team.id"
                    class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg"
                  >
                    <div class="flex space-x-2">
                      <IconDisplay 
                        :icon-data="team.avatar_data" 
                        size="sm" 
                        :title="team.name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                      <IconDisplay 
                        :icon-data="team.avatar_data" 
                        size="md" 
                        :title="team.name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                      <IconDisplay 
                        :icon-data="team.avatar_data" 
                        size="lg" 
                        :title="team.name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                      <IconDisplay 
                        :icon-data="team.avatar_data" 
                        size="xl" 
                        :title="team.name"
                        :custom-config="getCurrentSizeConfig()"
                      />
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        {{ team.name }}
                      </p>
                      <p class="text-xs text-gray-500 truncate">
                        {{ getAvatarTypeDescription(team.avatar_data) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </section>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, reactive } from 'vue'
import { useRouter } from 'vue-router'
import IconDisplay from '@/components/common/IconDisplay.vue'
import axios from 'axios'

export default {
  name: 'IconSizeTest',
  components: {
    IconDisplay
  },
  setup() {
    const loading = ref(true)
    const users = ref([])
    const organizations = ref([])
    const teams = ref([])
    const router = useRouter()
    
    // 所有可用尺寸
    const allSizes = ['4', '5', '6', '8', '10', '12', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl']
    
    // 尺寸配置 (基於 80x80px 背景)
    const sizeConfig = reactive({
      text: {
        fontSize: 28 // px
      },
      emoji: {
        fontSize: 2.75 // rem
      },
      icon: {
        size: 2.75 // rem
      }
    })
    
    // 測試範例資料
    const textExamples = [
      {
        key: 'chinese_1',
        name: '中文 1 字',
        data: { type: 'text', text: '李', backgroundColor: '#f0fdf4', textColor: '#374151' }
      },
      {
        key: 'chinese_2', 
        name: '中文 2 字',
        data: { type: 'text', text: '李華', backgroundColor: '#fef2f2', textColor: '#374151' }
      },
      {
        key: 'english_1',
        name: '英文 1 字母',
        data: { type: 'text', text: 'A', backgroundColor: '#eff6ff', textColor: '#374151' }
      },
      {
        key: 'english_2',
        name: '英文 2 字母',
        data: { type: 'text', text: 'AB', backgroundColor: '#faf5ff', textColor: '#374151' }
      },
      {
        key: 'mixed',
        name: '混合字符',
        data: { type: 'text', text: 'A李', backgroundColor: '#fff7ed', textColor: '#374151' }
      }
    ]
    
    const emojiExamples = [
      {
        key: 'simple',
        name: '簡單 Emoji',
        data: { type: 'emoji', emoji: '😀', backgroundColor: '#fef2f2' }
      },
      {
        key: 'complex',
        name: '複雜 Emoji',
        data: { type: 'emoji', emoji: '👨‍💻', backgroundColor: '#f0f9ff' }
      },
      {
        key: 'skin_tone',
        name: '膚色修飾',
        data: { type: 'emoji', emoji: '👋🏻', backgroundColor: '#fff7ed' }
      }
    ]
    
    const heroExamples = [
      {
        key: 'outline',
        name: 'Outline 樣式',
        data: { type: 'hero_icon', icon: 'user', backgroundColor: '#f0f9ff', iconColor: '#0ea5e9', style: 'outline' }
      },
      {
        key: 'solid',
        name: 'Solid 樣式',
        data: { type: 'hero_icon', icon: 'heart', backgroundColor: '#fdf2f8', iconColor: '#ec4899', style: 'solid' }
      }
    ]
    
    const bsExamples = [
      {
        key: 'outline',
        name: 'Outline 樣式',
        data: { type: 'bootstrap_icon', icon: 'star', backgroundColor: '#fffbeb', iconColor: '#f59e0b', style: 'outline' }
      },
      {
        key: 'fill',
        name: 'Fill 樣式',
        data: { type: 'bootstrap_icon', icon: 'lightning', backgroundColor: '#f7fee7', iconColor: '#84cc16', style: 'fill' }
      }
    ]
    
    const imageExample = {
      name: '圖片類型',
      data: { type: 'image', path: 'https://via.placeholder.com/80x80/6366f1/ffffff?text=IMG' }
    }
    
    const loadData = async () => {
      try {
        loading.value = true
        
        // 載入測試用戶
        const usersResponse = await axios.get('/api/admin/users', {
          params: { search: 'test', per_page: 20 }
        })
        users.value = usersResponse.data.data
        
        // 載入測試組織
        const orgsResponse = await axios.get('/api/admin/organizations', {
          params: { search: '測試組織', per_page: 20 }
        })
        organizations.value = orgsResponse.data.data
        
        // 載入測試團隊 (透過組織)
        if (organizations.value.length > 0) {
          for (const org of organizations.value) {
            try {
              const teamsResponse = await axios.get(`/api/organizations/${org.id}/teams`)
              // 檢查回應結構
              if (teamsResponse.data && Array.isArray(teamsResponse.data.data)) {
                teams.value.push(...teamsResponse.data.data)
              } else if (teamsResponse.data && Array.isArray(teamsResponse.data)) {
                teams.value.push(...teamsResponse.data)
              }
            } catch (teamError) {
              console.warn(`無法載入組織 ${org.id} 的團隊:`, teamError)
            }
          }
        }
        
      } catch (error) {
        console.error('載入測試資料失敗:', error)
        if (error.response?.status === 401) {
          router.push('/login')
        }
      } finally {
        loading.value = false
      }
    }
    
    const getAvatarTypeDescription = (avatarData) => {
      if (!avatarData) return '無頭像數據'
      
      switch (avatarData.type) {
        case 'text':
          return `文字: "${avatarData.text}"`
        case 'emoji':
          return `Emoji: ${avatarData.emoji}`
        case 'hero_icon':
          return `Hero Icon: ${avatarData.icon} (${avatarData.style})`
        case 'bootstrap_icon':
          return `Bootstrap Icon: ${avatarData.icon} (${avatarData.style})`
        case 'image':
          return `圖片: ${avatarData.path || avatarData.url}`
        default:
          return '未知類型'
      }
    }
    
    const getCurrentSizeConfig = () => {
      return {
        text: { fontSize: `${sizeConfig.text.fontSize}px` },
        emoji: { fontSize: `${sizeConfig.emoji.fontSize}rem` },
        icon: { size: `${sizeConfig.icon.size}rem` }
      }
    }
    
    const resetToDefaults = () => {
      sizeConfig.text.fontSize = 28
      sizeConfig.emoji.fontSize = 2.75
      sizeConfig.icon.size = 2.75
    }
    
    const exportSizeConfig = () => {
      const config = {
        baseContainer: '80x80px',
        text: {
          fontSize: `${sizeConfig.text.fontSize}px`,
          note: '適合中文，拉丁語系建議調大'
        },
        emoji: {
          fontSize: `${sizeConfig.emoji.fontSize}rem`,
          pixels: `${sizeConfig.emoji.fontSize * 16}px`
        },
        icon: {
          size: `${sizeConfig.icon.size}rem`,
          pixels: `${sizeConfig.icon.size * 16}px`
        },
        exportTime: new Date().toISOString()
      }
      
      console.log('=== Icon Size Configuration ===')
      console.log(JSON.stringify(config, null, 2))
      alert('配置已匯出到 Console，請檢查瀏覽器開發者工具')
    }
    
    onMounted(() => {
      loadData()
    })
    
    return {
      loading,
      users,
      organizations,
      teams,
      allSizes,
      sizeConfig,
      textExamples,
      emojiExamples,
      heroExamples,
      bsExamples,
      imageExample,
      getAvatarTypeDescription,
      getCurrentSizeConfig,
      resetToDefaults,
      exportSizeConfig
    }
  }
}
</script>