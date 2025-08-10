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

            <!-- 組織詳細管理 Widget -->
            <div v-if="isOrganizationManagePage" class="bg-white rounded-lg shadow">
              <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ organizationName || '組織管理' }}</h2>
              </div>
              
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
                  <i class="bi bi-person-check text-base mr-3"></i>
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
                  <i class="bi bi-people-fill text-base mr-3"></i>
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
                  <CogIcon class="h-4 w-4 mr-3" />
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
import { computed, inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
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
    
    // 檢查是否在組織管理頁面
    const isOrganizationManagePage = computed(() => {
      return route.path.includes('/admin/organizations/') && route.path.includes('/manage')
    })
    
    // 獲取組織名稱（從全局狀態或 OrganizationManage 組件獲取）
    const organizationName = computed(() => {
      if (window.organizationManageInstance && window.organizationManageInstance.organization && window.organizationManageInstance.organization.value) {
        return window.organizationManageInstance.organization.value.name
      }
      return null
    })
    
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
    
    return {
      isOrganizationManagePage,
      organizationName,
      activeTab,
      setActiveTab
    }
  }
}
</script>