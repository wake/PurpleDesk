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

        <!-- 動作按鈕組 -->
        <div class="mt-4 lg:mt-0 lg:ml-6">
          <div class="flex space-x-3">
            <button 
              v-if="activeTab === 'members'"
              @click="showInviteModal = true"
              class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              邀請成員
            </button>
            <button 
              v-if="activeTab === 'teams'"
              @click="showCreateTeamModal = true"
              class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              建立團隊
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
        :showInviteButton="false"
        :showInviteModal="showInviteModal"
        @refresh="fetchOrganization"
        @success="showSuccessMessage"
        @show-invite="showInviteModal = true"
        @close-invite="showInviteModal = false"
      />
      
      <!-- 團隊管理頁籤 -->
      <OrganizationTeams 
        v-if="activeTab === 'teams'"
        :organization="organization"
        :showCreateButton="false"
        :showCreateModal="showCreateTeamModal"
        @refresh="fetchOrganization"
        @success="showSuccessMessage"
        @show-create="showCreateTeamModal = true"
        @close-create="showCreateTeamModal = false"
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
import axios from 'axios'
import { CogIcon } from '@heroicons/vue/outline'

export default {
  name: 'OrganizationManage',
  components: {
    OrganizationMembers,
    OrganizationTeams,
    OrganizationSettings,
    CogIcon
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const authStore = useAuthStore()
    const activeTab = ref(route.query.tab || 'members')
    const organization = ref(null)
    const isLoading = ref(false)
    const successMessage = ref('')
    const showInviteModal = ref(false)
    const showCreateTeamModal = ref(false)
    
    const user = computed(() => authStore.user)
    const organizationId = computed(() => route.params.id)
    
    // 讓 AdminLayout 可以調用這個方法
    const setActiveTab = (tab) => {
      activeTab.value = tab
      router.push({
        path: route.path,
        query: { ...route.query, tab }
      })
    }
    
    // 監聽 URL 查詢參數變化
    watch(() => route.query.tab, (newTab) => {
      if (newTab && ['members', 'teams', 'settings'].includes(newTab)) {
        activeTab.value = newTab
      } else if (!newTab) {
        // 如果沒有 tab 參數，默認顯示 members
        activeTab.value = 'members'
      }
    })
    
    const fetchOrganization = async () => {
      if (!organizationId.value) {
        console.warn('No organization ID available')
        return
      }
      
      isLoading.value = true
      organization.value = null // 重置狀態
      
      try {
        console.log('Fetching organization:', organizationId.value)
        const response = await axios.get(`/api/organizations/${organizationId.value}`)
        console.log('Organization response:', response.data)
        
        // 處理分頁回應格式
        if (response.data.users && response.data.users.data) {
          // 如果 users 是分頁格式，重新整理為簡單格式以兼容 OrganizationSettings
          organization.value = {
            ...response.data,
            users: response.data.users.data || response.data.users
          }
        } else {
          organization.value = response.data
        }
        
        console.log('Organization loaded:', organization.value?.name)
      } catch (error) {
        console.error('Failed to fetch organization:', error)
        organization.value = null
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
      // 確保在 DOM 渲染完成後執行
      setTimeout(() => {
        fetchOrganization()
      }, 100)
    })
    
    // 全局暴露實例方法和數據供 AdminLayout 使用
    if (typeof window !== 'undefined') {
      window.organizationManageInstance = {
        setActiveTab,
        organization: organization,
        fetchOrganization: fetchOrganization
      }
    }
    
    return {
      user,
      activeTab,
      organization,
      isLoading,
      successMessage,
      showInviteModal,
      showCreateTeamModal,
      fetchOrganization,
      showSuccessMessage
    }
  }
}
</script>