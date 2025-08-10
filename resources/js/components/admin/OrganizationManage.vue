<template>
  <div class="bg-white shadow rounded-lg">
    <!-- 頁面標題 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <h1 class="text-2xl font-bold text-gray-900">
            {{ organization?.name }} 管理
          </h1>
          <p class="mt-1 text-sm text-gray-600">
            管理組織設定、成員和團隊
          </p>
        </div>
        <div v-if="organization" class="mt-4 flex md:mt-0 md:ml-4">
          <div class="h-12 w-12 rounded bg-primary-100 flex items-center justify-center overflow-hidden">
            <img
              v-if="organization.logo_url"
              :src="organization.logo_url"
              :alt="organization.name"
              class="h-full w-full object-cover"
            />
            <svg v-else class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- 頁籤導航 -->
    <div class="border-b border-gray-200">
          <div class="sm:hidden">
            <select
              v-model="activeTab"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
            >
              <option value="members">成員管理</option>
              <option value="teams">團隊管理</option>
              <option value="settings">組織設定</option>
            </select>
          </div>
          <div class="hidden sm:block">
            <nav class="flex space-x-8" aria-label="Tabs">
              <button
                @click="activeTab = 'members'"
                :class="[
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm',
                  activeTab === 'members'
                    ? 'border-primary-500 text-primary-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <svg class="inline-block h-5 w-5 mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                成員管理
              </button>
              <button
                @click="activeTab = 'teams'"
                :class="[
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm',
                  activeTab === 'teams'
                    ? 'border-primary-500 text-primary-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <svg class="inline-block h-5 w-5 mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                團隊管理
              </button>
              <button
                @click="activeTab = 'settings'"
                :class="[
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm',
                  activeTab === 'settings'
                    ? 'border-primary-500 text-primary-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <svg class="inline-block h-5 w-5 mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                組織設定
              </button>
            </nav>
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
import axios from 'axios'

export default {
  name: 'OrganizationManage',
  components: {
    OrganizationMembers,
    OrganizationTeams,
    OrganizationSettings
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