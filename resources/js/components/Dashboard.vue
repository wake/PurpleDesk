<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 導航欄 -->
    <AppNavbar />

    <!-- 主要內容 -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="border-4 border-dashed border-gray-200 rounded-lg p-8">
          <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-primary-100 rounded-full flex items-center justify-center mb-4">
              <DocumentTextIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">歡迎使用 PurpleDesk</h1>
            <p class="text-gray-600 mb-6">您的專案管理系統已準備就緒</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-3xl mx-auto">
              <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-primary-600 mb-2 text-center">
                  <ClipboardCheckIcon class="h-8 w-8 mx-auto" />
                </div>
                <h3 class="font-medium text-gray-900">專案管理</h3>
                <p class="text-sm text-gray-500 mt-1">即將推出</p>
              </div>
              
              <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-primary-600 mb-2 text-center">
                  <DocumentTextIcon class="h-8 w-8 mx-auto" />
                </div>
                <h3 class="font-medium text-gray-900">任務追蹤</h3>
                <p class="text-sm text-gray-500 mt-1">即將推出</p>
              </div>
              
              <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-primary-600 mb-2 text-center">
                  <i class="bi bi-people-fill text-3xl"></i>
                </div>
                <h3 class="font-medium text-gray-900">團隊協作</h3>
                <p class="text-sm text-gray-500 mt-1">即將推出</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import AppNavbar from './AppNavbar.vue'
import { DocumentTextIcon, ClipboardCheckIcon } from '@heroicons/vue/outline'

export default {
  name: 'Dashboard',
  components: {
    AppNavbar,
    DocumentTextIcon,
    ClipboardCheckIcon
  }
}
</script>