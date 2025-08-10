<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 導航欄 -->
    <AppNavbar />

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex">
          <!-- 側邊欄 -->
          <div class="w-64 space-y-4 mr-6">
            <!-- 系統管理 Widget -->
            <div class="bg-white rounded-lg shadow">
              <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">系統管理</h2>
              </div>
              
              <nav class="p-4 space-y-2">
                <router-link
                  to="/admin/users"
                  class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                  :class="$route.path === '/admin/users' 
                    ? 'bg-primary-100 text-primary-900' 
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                >
                  <i class="bi bi-person-fill mr-3 text-xl"></i>
                  使用者管理
                </router-link>
                
                <router-link
                  to="/admin/organizations"
                  class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                  :class="$route.path === '/admin/organizations' 
                    ? 'bg-primary-100 text-primary-900' 
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                >
                  <OfficeBuildingIcon class="mr-3 h-5 w-5" />
                  組織管理
                </router-link>
                
                <router-link
                  to="/admin/system"
                  class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                  :class="$route.path === '/admin/system' 
                    ? 'bg-primary-100 text-primary-900' 
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                >
                  <CogIcon class="mr-3 h-5 w-5" />
                  系統設定
                </router-link>
              </nav>
            </div>

            <!-- 組織切換和管理 Widget -->
            <div v-if="isOrganizationManagePage" class="bg-white rounded-lg shadow">
              <!-- 組織切換下拉選單 -->
              <div class="relative p-4 border-b border-gray-200 organization-dropdown">
                <button
                  @click="toggleOrganizationDropdown"
                  class="w-full flex items-center justify-between text-left"
                >
                  <div class="flex items-center min-w-0 flex-1">
                    <img 
                      v-if="currentOrganization?.logo_url"
                      :src="currentOrganization.logo_url"
                      :alt="organizationName"
                      class="flex-shrink-0 mr-3 h-4 w-4 rounded object-cover"
                    />
                    <OfficeBuildingIcon 
                      v-else
                      class="flex-shrink-0 mr-3 h-4 w-4 text-gray-400" 
                    />
                    <h2 class="text-lg font-medium text-gray-900 truncate">{{ organizationName || '載入中...' }}</h2>
                  </div>
                  <svg
                    :class="[
                      'flex-shrink-0 h-4 w-4 text-gray-400 transition-transform duration-200',
                      showOrganizationDropdown ? 'rotate-180' : 'rotate-0'
                    ]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                
                <!-- 組織選單下拉清單 -->
                <div 
                  v-if="showOrganizationDropdown"
                  class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-50 max-h-64 overflow-y-auto"
                >
                  <div v-if="isLoadingOrganizations" class="p-4 text-center text-gray-500">
                    <div class="animate-spin inline-block w-4 h-4 border-2 border-solid border-current border-r-transparent rounded-full mr-2"></div>
                    載入中...
                  </div>
                  <div v-else>
                    <button
                      v-for="org in availableOrganizations"
                      :key="org.id"
                      @click="switchToOrganization(org)"
                      class="w-full px-4 py-3 text-left hover:bg-gray-50 flex items-center transition-colors duration-150"
                      :class="{
                        'bg-primary-50 text-primary-700': org.id == currentOrganizationId,
                        'text-gray-700': org.id != currentOrganizationId
                      }"
                    >
                      <img 
                        v-if="org.logo_url"
                        :src="org.logo_url"
                        :alt="org.name"
                        class="flex-shrink-0 mr-3 h-4 w-4 rounded object-cover"
                      />
                      <OfficeBuildingIcon 
                        v-else
                        class="flex-shrink-0 mr-3 h-4 w-4" 
                      />
                      <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">{{ org.name }}</div>
                        <div v-if="org.description" class="text-xs text-gray-500 truncate">{{ org.description }}</div>
                      </div>
                      <div v-if="org.id == currentOrganizationId" class="flex-shrink-0 ml-2">
                        <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- 組織管理選單 -->
              <nav class="p-4 space-y-2">
                <button
                  @click="setActiveTab('members')"
                  :class="[
                    'w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200',
                    activeTab === 'members'
                      ? 'bg-primary-100 text-primary-700'
                      : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                  ]"
                >
                  <i class="bi bi-person-check-fill mr-3 text-xl"></i>
                  成員管理
                </button>
                <button
                  @click="setActiveTab('teams')"
                  :class="[
                    'w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200',
                    activeTab === 'teams'
                      ? 'bg-primary-100 text-primary-700'
                      : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                  ]"
                >
                  <i class="bi bi-people-fill mr-3 text-xl"></i>
                  團隊管理
                </button>
                <button
                  @click="setActiveTab('settings')"
                  :class="[
                    'w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200',
                    activeTab === 'settings'
                      ? 'bg-primary-100 text-primary-700'
                      : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                  ]"
                >
                  <CogIcon class="mr-3 h-5 w-5" />
                  組織設定
                </button>
              </nav>
            </div>
          </div>

          <!-- 主要內容 -->
          <div class="flex-1">
            <router-view />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, inject, ref, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import AppNavbar from '../AppNavbar.vue'
import { OfficeBuildingIcon, CogIcon } from '@heroicons/vue/outline'

export default {
  name: 'AdminLayout',
  components: {
    AppNavbar,
    OfficeBuildingIcon,
    CogIcon
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const showOrganizationDropdown = ref(false)
    const availableOrganizations = ref([])
    const isLoadingOrganizations = ref(false)
    
    // 檢查是否在組織管理頁面
    const isOrganizationManagePage = computed(() => {
      return route.path.includes('/admin/organizations/') && route.path.includes('/manage')
    })
    
    // 獲取當前組織 ID
    const currentOrganizationId = computed(() => {
      return route.params.id
    })
    
    // 獲取組織資訊和名稱
    const currentOrganization = ref(null)
    const isLoadingCurrentOrg = ref(false)
    
    const organizationName = computed(() => {
      return currentOrganization.value?.name || null
    })
    
    // 獲取當前組織資訊
    const fetchCurrentOrganization = async () => {
      if (!currentOrganizationId.value) {
        console.warn('AdminLayout: No organization ID available')
        return
      }
      
      isLoadingCurrentOrg.value = true
      currentOrganization.value = null // 重置狀態
      
      try {
        console.log('AdminLayout: Fetching organization:', currentOrganizationId.value)
        const response = await axios.get(`/api/organizations/${currentOrganizationId.value}`)
        console.log('AdminLayout: Organization response:', response.data)
        
        // 處理分頁和非分頁格式
        if (response.data.users && response.data.users.data) {
          currentOrganization.value = {
            ...response.data,
            users: response.data.users.data || response.data.users
          }
        } else {
          currentOrganization.value = response.data
        }
        
        // 同時更新 availableOrganizations 陣列中對應的組織資料
        if (availableOrganizations.value.length > 0 && currentOrganization.value) {
          const index = availableOrganizations.value.findIndex(org => org.id == currentOrganizationId.value)
          if (index !== -1) {
            console.log('AdminLayout: Updating organization in available list')
            availableOrganizations.value[index] = {
              ...availableOrganizations.value[index],
              ...currentOrganization.value
            }
          }
        }
        
        console.log('AdminLayout: Organization loaded:', currentOrganization.value?.name)
      } catch (error) {
        console.error('AdminLayout: Failed to fetch current organization:', error)
        currentOrganization.value = null
      } finally {
        isLoadingCurrentOrg.value = false
      }
    }
    
    // 從組織管理組件獲取和設置 activeTab
    const activeTab = computed(() => {
      // 嘗試從子組件獲取 activeTab，如果沒有則默認為 'members'
      return route.query.tab || 'members'
    })
    
    const setActiveTab = (tab) => {
      // 更新 URL query 參數來改變 activeTab
      router.push({
        path: route.path,
        query: { ...route.query, tab }
      })
      
      // 通知子組件更改
      if (window.organizationManageInstance) {
        window.organizationManageInstance.setActiveTab(tab)
      }
    }
    
    const toggleOrganizationDropdown = async () => {
      showOrganizationDropdown.value = !showOrganizationDropdown.value
      
      if (showOrganizationDropdown.value && availableOrganizations.value.length === 0) {
        await fetchAvailableOrganizations()
      }
    }
    
    const fetchAvailableOrganizations = async () => {
      isLoadingOrganizations.value = true
      try {
        // 使用管理員 API 獲取所有組織
        const response = await axios.get('/api/admin/organizations')
        if (response.data && response.data.data) {
          availableOrganizations.value = response.data.data
        } else {
          availableOrganizations.value = response.data || []
        }
      } catch (error) {
        console.error('Failed to fetch organizations:', error)
        availableOrganizations.value = []
      } finally {
        isLoadingOrganizations.value = false
      }
    }
    
    const switchToOrganization = async (organization) => {
      showOrganizationDropdown.value = false
      
      // 先檢查是否真的要切換到不同組織
      if (organization.id == currentOrganizationId.value) {
        return
      }
      
      // 切換到新組織的管理頁面
      const currentTab = route.query.tab || 'members'
      await router.push({
        path: `/admin/organizations/${organization.id}/manage`,
        query: { tab: currentTab }
      })
    }
    
    // 點擊外部關閉下拉選單
    const handleClickOutside = (event) => {
      if (showOrganizationDropdown.value && !event.target.closest('.organization-dropdown')) {
        showOrganizationDropdown.value = false
      }
    }
    
    // 監聽路由變更，當 ID 變更時重新獲取組織資訊
    watch(
      () => currentOrganizationId.value,
      async (newId, oldId) => {
        if (newId && isOrganizationManagePage.value && newId !== oldId) {
          await fetchCurrentOrganization()
          // 通知子組件重新載入數據
          if (window.organizationManageInstance) {
            window.organizationManageInstance.fetchOrganization()
          }
        }
      },
      { immediate: true }
    )
    
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
      
      // 全局暴露方法供子組件使用
      if (typeof window !== 'undefined') {
        window.adminLayoutInstance = {
          fetchCurrentOrganization
        }
      }
    })
    
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
      
      // 清理全局引用
      if (typeof window !== 'undefined') {
        delete window.adminLayoutInstance
      }
    })
    
    return {
      isOrganizationManagePage,
      organizationName,
      activeTab,
      currentOrganizationId,
      showOrganizationDropdown,
      availableOrganizations,
      isLoadingOrganizations,
      currentOrganization,
      isLoadingCurrentOrg,
      setActiveTab,
      toggleOrganizationDropdown,
      switchToOrganization,
      fetchCurrentOrganization
    }
  }
}
</script>