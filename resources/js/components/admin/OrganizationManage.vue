<template>
  <div class="bg-white shadow rounded-lg">
    <!-- 頁面標題 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center">
          <!-- 組織標題 -->
          <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-gray-900">
              {{ organization?.name }} 管理
            </h1>
            <p class="mt-1 text-sm text-gray-600">
              管理組織設定、成員和團隊
            </p>
          </div>
        </div>

        <!-- 頁籤導航 - 緊湊按鈕組 -->
        <div class="mt-4 lg:mt-0 lg:ml-6">
          <!-- 行動裝置下拉選單 -->
          <div class="sm:hidden">
            <select
              v-model="activeTab"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            >
              <option value="members">成員管理</option>
              <option value="teams">團隊管理</option>
              <option value="settings">組織設定</option>
            </select>
          </div>
          
          <!-- 桌面版按鈕組 -->
          <div class="hidden sm:flex rounded-lg border border-gray-200 bg-gray-50 p-1">
            <button
              @click="activeTab = 'members'"
              :class="[
                'relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200',
                activeTab === 'members'
                  ? 'bg-white text-primary-700 shadow-sm border border-gray-200'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
              ]"
            >
              <IconUserCheck class="h-4 w-4 mr-1.5" />
              成員管理
            </button>
            <button
              @click="activeTab = 'teams'"
              :class="[
                'relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200',
                activeTab === 'teams'
                  ? 'bg-white text-primary-700 shadow-sm border border-gray-200'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
              ]"
            >
              <IconUsers class="h-4 w-4 mr-1.5" />
              團隊管理
            </button>
            <button
              @click="activeTab = 'settings'"
              :class="[
                'relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200',
                activeTab === 'settings'
                  ? 'bg-white text-primary-700 shadow-sm border border-gray-200'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
              ]"
            >
              <IconSettings class="h-4 w-4 mr-1.5" />
              組織設定
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 內容區域 -->
    <div>
      <!-- 成員管理頁籤 -->
      <OrganizationMembers 
        v-if="activeTab === 'members'"
        :organization="organization"
        @refresh="fetchOrganization"
        @success="showSuccessMessage"
      />
      
      <!-- 團隊管理頁籤 -->
      <OrganizationTeams 
        v-if="activeTab === 'teams'"
        :organization="organization"
        @refresh="fetchOrganization"
        @success="showSuccessMessage"
      />
      
      <!-- 組織設定頁籤 -->
      <OrganizationSettings 
        v-if="activeTab === 'settings'"
        :organization="organization"
        @refresh="fetchOrganization"
        @success="showSuccessMessage"
      />
    </div>
    
    <!-- 成功訊息 Toast -->
    <div v-if="successMessage" class="fixed bottom-4 right-4 z-50">
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg max-w-sm">
        <div class="flex items-center">
          <svg class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.93 10.7a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
          </svg>
          <span class="text-sm font-medium">{{ successMessage }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import OrganizationMembers from './organization/OrganizationMembers.vue'
import OrganizationTeams from './organization/OrganizationTeams.vue'
import OrganizationSettings from './organization/OrganizationSettings.vue'
// Tabler Icons
import { IconUserCheck, IconUsers, IconSettings } from '@tabler/icons-vue'
import axios from 'axios'

export default {
  name: 'OrganizationManage',
  components: {
    OrganizationMembers,
    OrganizationTeams,
    OrganizationSettings,
    IconUserCheck,
    IconUsers,
    IconSettings
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const authStore = useAuthStore()
    const activeTab = ref('members')
    const organization = ref(null)
    const isLoading = ref(false)
    const successMessage = ref('')
    
    const user = computed(() => authStore.user)
    const organizationId = computed(() => route.params.id)
    
    const fetchOrganization = async () => {
      if (!organizationId.value) return
      
      isLoading.value = true
      try {
        const response = await axios.get(`/api/organizations/${organizationId.value}`)
        organization.value = response.data
      } catch (error) {
        console.error('Failed to fetch organization:', error)
      } finally {
        isLoading.value = false
      }
    }
    
    const showSuccessMessage = (message) => {
      successMessage.value = message
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    }
    
    // 監聽路由變化，防止重新整理時跳回 dashboard
    watch(() => route.params.id, (newId) => {
      if (newId && newId !== organizationId.value) {
        fetchOrganization()
      }
    }, { immediate: true })
    
    onMounted(() => {
      fetchOrganization()
      // 從 query 參數中讀取活動頁籤
      if (route.query.tab && ['members', 'teams', 'settings'].includes(route.query.tab)) {
        activeTab.value = route.query.tab
      }
    })
    
    return {
      user,
      activeTab,
      organization,
      isLoading,
      successMessage,
      fetchOrganization,
      showSuccessMessage
    }
  }
}
</script>