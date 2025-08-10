<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 導航欄 -->
    <AppNavbar />
    
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- 頁面標題 -->
        <div class="mb-6">
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <router-link to="/admin/organizations" class="text-gray-400 hover:text-gray-500">
                  組織管理
                </router-link>
              </li>
              <li>
                <div class="flex items-center">
                  <svg class="h-5 w-5 text-gray-300 mx-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-gray-500">{{ organization?.name }}</span>
                </div>
              </li>
            </ol>
          </nav>
          
          <div class="mt-2 md:flex md:items-center md:justify-between">
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
        <div class="mb-6">
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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
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
        <div class="bg-white shadow rounded-lg">
          <!-- 成員管理頁籤 -->
          <OrganizationMembers 
            v-if="activeTab === 'members'"
            :organization="organization"
            @refresh="fetchOrganization"
          />
          
          <!-- 團隊管理頁籤 -->
          <OrganizationTeams 
            v-if="activeTab === 'teams'"
            :organization="organization"
            @refresh="fetchOrganization"
          />
          
          <!-- 組織設定頁籤 -->
          <OrganizationSettings 
            v-if="activeTab === 'settings'"
            :organization="organization"
            @refresh="fetchOrganization"
          />
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import AppNavbar from '../AppNavbar.vue'
import OrganizationMembers from './organization/OrganizationMembers.vue'
import OrganizationTeams from './organization/OrganizationTeams.vue'
import OrganizationSettings from './organization/OrganizationSettings.vue'
import axios from 'axios'

export default {
  name: 'OrganizationManage',
  components: {
    AppNavbar,
    OrganizationMembers,
    OrganizationTeams,
    OrganizationSettings
  },
  setup() {
    const route = useRoute()
    const authStore = useAuthStore()
    const activeTab = ref('members')
    const organization = ref(null)
    const isLoading = ref(false)
    
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
      fetchOrganization
    }
  }
}
</script>