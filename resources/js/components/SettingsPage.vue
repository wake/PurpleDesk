<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 導航欄 -->
    <AppNavbar />

    <!-- 主要內容 -->
    <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white shadow rounded-lg">
          <!-- 頁面標題 -->
          <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">設定</h1>
            <p class="mt-1 text-sm text-gray-600">管理您的個人偏好設定</p>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-6 p-6">
            <!-- 個人設定 -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">個人偏好</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="locale" class="block text-sm font-medium text-gray-700">語系</label>
                  <select
                    id="locale"
                    v-model="form.locale"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  >
                    <option value="zh_TW">繁體中文</option>
                    <option value="en">English</option>
                    <option value="ja">日本語</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">選擇您偏好的顯示語言</p>
                </div>

                <div>
                  <label for="timezone" class="block text-sm font-medium text-gray-700">時區</label>
                  <select
                    id="timezone"
                    v-model="form.timezone"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  >
                    <option value="Asia/Taipei">台北 (UTC+8)</option>
                    <option value="Asia/Tokyo">東京 (UTC+9)</option>
                    <option value="Asia/Shanghai">上海 (UTC+8)</option>
                    <option value="Asia/Hong_Kong">香港 (UTC+8)</option>
                    <option value="Asia/Singapore">新加坡 (UTC+8)</option>
                    <option value="America/New_York">紐約 (UTC-5)</option>
                    <option value="America/Los_Angeles">洛杉磯 (UTC-8)</option>
                    <option value="Europe/London">倫敦 (UTC+0)</option>
                    <option value="Europe/Paris">巴黎 (UTC+1)</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">設定您所在地區的時區</p>
                </div>
              </div>
            </div>

            <!-- 通知設定 -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">通知設定</h3>
              <div class="space-y-4">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="email_notifications"
                      v-model="form.email_notifications"
                      type="checkbox"
                      class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="email_notifications" class="font-medium text-gray-700">電子郵件通知</label>
                    <p class="text-gray-500">接收重要更新和活動的電子郵件通知</p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="browser_notifications"
                      v-model="form.browser_notifications"
                      type="checkbox"
                      class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="browser_notifications" class="font-medium text-gray-700">瀏覽器通知</label>
                    <p class="text-gray-500">在瀏覽器中接收即時通知</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 顯示設定 -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">顯示設定</h3>
              <div class="space-y-4">
                <div>
                  <label for="theme" class="block text-sm font-medium text-gray-700">主題</label>
                  <select
                    id="theme"
                    v-model="form.theme"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  >
                    <option value="light">淺色模式</option>
                    <option value="dark">深色模式</option>
                    <option value="auto">跟隨系統</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">選擇您偏好的顯示主題</p>
                </div>
              </div>
            </div>

            <!-- 操作按鈕 -->
            <div class="border-t border-gray-200 pt-6 flex justify-end space-x-3">
              <router-link
                to="/dashboard"
                class="bg-white border border-gray-300 rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                取消
              </router-link>
              <button
                type="submit"
                :disabled="isLoading"
                class="bg-primary-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ isLoading ? '儲存中...' : '儲存設定' }}
              </button>
            </div>
          </form>

          <!-- 錯誤訊息 -->
          <div v-if="errorMessage" class="mx-6 mb-6">
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-red-800">
                    更新失敗
                  </h3>
                  <div class="mt-2 text-sm text-red-700">
                    {{ errorMessage }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    
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
import { reactive, ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import AppNavbar from './AppNavbar.vue'
import axios from 'axios'

export default {
  name: 'SettingsPage',
  components: {
    AppNavbar
  },
  setup() {
    const authStore = useAuthStore()
    const isLoading = ref(false)
    const successMessage = ref('')
    const errorMessage = ref('')
    
    const user = computed(() => authStore.user)
    
    const form = reactive({
      locale: 'zh_TW',
      timezone: 'Asia/Taipei',
      email_notifications: true,
      browser_notifications: false,
      theme: 'light'
    })
    
    const handleSubmit = async () => {
      if (isLoading.value) return
      
      isLoading.value = true
      errorMessage.value = ''
      successMessage.value = ''
      
      try {
        const response = await axios.post('/api/settings', {
          locale: form.locale,
          timezone: form.timezone,
          email_notifications: form.email_notifications,
          browser_notifications: form.browser_notifications,
          theme: form.theme
        })
        
        // 更新本地用戶資料
        await authStore.fetchUser()
        
        successMessage.value = '設定已成功更新'
        
        // 3秒後清除成功訊息
        setTimeout(() => {
          successMessage.value = ''
        }, 3000)
        
      } catch (error) {
        errorMessage.value = error.response?.data?.message || '更新失敗，請稍後再試'
      } finally {
        isLoading.value = false
      }
    }
    
    // 初始化表單資料
    onMounted(() => {
      if (user.value) {
        form.locale = user.value.locale || 'zh_TW'
        form.timezone = user.value.timezone || 'Asia/Taipei'
        form.email_notifications = user.value.email_notifications ?? true
        form.browser_notifications = user.value.browser_notifications ?? false
        form.theme = user.value.theme || 'light'
      }
    })
    
    return {
      user,
      form,
      isLoading,
      successMessage,
      errorMessage,
      handleSubmit
    }
  }
}
</script>