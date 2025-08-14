<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h1 class="text-2xl font-bold text-gray-900">頭像系統測試頁面</h1>
          <p class="mt-2 text-sm text-gray-600">
            展示各種類型的頭像配置與顯示效果
          </p>
        </div>

        <div class="p-6 space-y-8">
          
          <!-- 載入狀態 -->
          <div v-if="loading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>

          <!-- 用戶頭像 -->
          <div v-else>
            <section class="space-y-4">
              <h2 class="text-xl font-semibold text-gray-900">用戶頭像</h2>
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
                    />
                    <IconDisplay 
                      :icon-data="user.avatar_data" 
                      size="sm" 
                      :title="user.full_name"
                    />
                    <IconDisplay 
                      :icon-data="user.avatar_data" 
                      size="md" 
                      :title="user.full_name"
                    />
                    <IconDisplay 
                      :icon-data="user.avatar_data" 
                      size="lg" 
                      :title="user.full_name"
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
            </section>

            <!-- 組織頭像 -->
            <section class="space-y-4">
              <h2 class="text-xl font-semibold text-gray-900">組織頭像</h2>
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
                    />
                    <IconDisplay 
                      :icon-data="org.avatar_data" 
                      size="md" 
                      :title="org.name"
                    />
                    <IconDisplay 
                      :icon-data="org.avatar_data" 
                      size="lg" 
                      :title="org.name"
                    />
                    <IconDisplay 
                      :icon-data="org.avatar_data" 
                      size="xl" 
                      :title="org.name"
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
            </section>

            <!-- 團隊頭像 -->
            <section class="space-y-4">
              <h2 class="text-xl font-semibold text-gray-900">團隊頭像</h2>
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
                    />
                    <IconDisplay 
                      :icon-data="team.avatar_data" 
                      size="md" 
                      :title="team.name"
                    />
                    <IconDisplay 
                      :icon-data="team.avatar_data" 
                      size="lg" 
                      :title="team.name"
                    />
                    <IconDisplay 
                      :icon-data="team.avatar_data" 
                      size="xl" 
                      :title="team.name"
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
            </section>

            <!-- 頭像類型示例 -->
            <section class="space-y-4">
              <h2 class="text-xl font-semibold text-gray-900">頭像類型示例</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                  v-for="(example, key) in avatarExamples" 
                  :key="key"
                  class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg"
                >
                  <IconDisplay 
                    :icon-data="example.data" 
                    size="lg" 
                    :title="example.name"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">
                      {{ example.name }}
                    </p>
                    <p class="text-xs text-gray-500">
                      {{ example.description }}
                    </p>
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import IconDisplay from '@/components/common/IconDisplay.vue'
import axios from 'axios'

export default {
  name: 'AvatarTest',
  components: {
    IconDisplay
  },
  setup() {
    const loading = ref(true)
    const users = ref([])
    const organizations = ref([])
    const teams = ref([])
    const router = useRouter()
    
    // 頭像類型示例
    const avatarExamples = {
      default_user: {
        name: '預設用戶頭像',
        description: '名字第一字母 + 隨機淡色背景',
        data: { type: 'text', text: '測', backgroundColor: '#f0fdf4', textColor: '#374151' }
      },
      custom_text: {
        name: '自訂文字頭像',
        description: '自訂文字 + 自訂顏色',
        data: { type: 'text', text: '王', backgroundColor: '#f0fdf4', textColor: '#059669' }
      },
      emoji_simple: {
        name: 'Emoji 頭像',
        description: '簡單 Emoji + 背景色',
        data: { type: 'emoji', emoji: '😀', backgroundColor: '#fef2f2' }
      },
      emoji_skin: {
        name: 'Emoji 膚色頭像',
        description: 'Emoji + 膚色修飾符',
        data: { type: 'emoji', emoji: '👋🏻', backgroundColor: '#fff7ed' }
      },
      hero_outline: {
        name: 'Hero Icon Outline',
        description: 'Hero Icons 線條樣式',
        data: { type: 'hero_icon', icon: 'user', backgroundColor: '#f0f9ff', iconColor: '#0ea5e9', style: 'outline' }
      },
      hero_solid: {
        name: 'Hero Icon Solid',
        description: 'Hero Icons 實心樣式',
        data: { type: 'hero_icon', icon: 'heart', backgroundColor: '#fdf2f8', iconColor: '#ec4899', style: 'solid' }
      },
      bs_outline: {
        name: 'Bootstrap Icon Outline',
        description: 'Bootstrap Icons 線條樣式',
        data: { type: 'bootstrap_icon', icon: 'star', backgroundColor: '#fffbeb', iconColor: '#f59e0b', style: 'outline' }
      },
      bs_fill: {
        name: 'Bootstrap Icon Fill',
        description: 'Bootstrap Icons 填充樣式',
        data: { type: 'bootstrap_icon', icon: 'lightning', backgroundColor: '#f7fee7', iconColor: '#84cc16', style: 'fill' }
      },
      default_org: {
        name: '預設組織頭像',
        description: '淡紫背景 + 紫色建築圖標',
        data: { type: 'bootstrap_icon', icon: 'building', backgroundColor: '#faf5ff', iconColor: '#a855f7', style: 'fill' }
      },
      default_team: {
        name: '預設團隊頭像',
        description: '淡藍背景 + 藍色團隊圖標',
        data: { type: 'bootstrap_icon', icon: 'people', backgroundColor: '#eff6ff', iconColor: '#3b82f6', style: 'fill' }
      }
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
            const teamsResponse = await axios.get(`/api/organizations/${org.id}/teams`)
            teams.value.push(...teamsResponse.data.data)
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
    
    onMounted(() => {
      loadData()
    })
    
    return {
      loading,
      users,
      organizations,
      teams,
      avatarExamples,
      getAvatarTypeDescription
    }
  }
}
</script>