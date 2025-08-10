<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 導航欄 -->
    <AppNavbar />

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex">
          <!-- 側邊欄 -->
          <div class="w-64 bg-white rounded-lg shadow mr-6">
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
                <IconUser class="mr-3 h-5 w-5" />
                使用者管理
              </router-link>
              
              <router-link
                to="/admin/organizations"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                :class="$route.path === '/admin/organizations' 
                  ? 'bg-primary-100 text-primary-900' 
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
              >
                <IconBuilding class="mr-3 h-5 w-5" />
                組織管理
              </router-link>
              
              <router-link
                to="/admin/system"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                :class="$route.path === '/admin/system' 
                  ? 'bg-primary-100 text-primary-900' 
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
              >
                <IconSettings class="mr-3 h-5 w-5" />
                系統設定
              </router-link>
            </nav>
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
import AppNavbar from '../AppNavbar.vue'
// Tabler Icons
import { IconUser, IconBuilding, IconSettings } from '@tabler/icons-vue'

export default {
  name: 'AdminLayout',
  components: {
    AppNavbar,
    IconUser,
    IconBuilding,
    IconSettings
  }
}
</script>