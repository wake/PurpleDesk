<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 導航欄 -->
    <nav class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/dashboard" class="flex-shrink-0 flex items-center">
              <div class="h-8 w-8 bg-primary-600 rounded-full flex items-center justify-center">
                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.93 10.7a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
              </div>
              <span class="ml-2 text-xl font-bold text-gray-900">PurpleDesk</span>
            </router-link>
          </div>
          
          <div class="flex items-center">
            <router-link
              to="/dashboard"
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
            >
              返回儀表板
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- 主要內容 -->
    <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white shadow rounded-lg">
          <!-- 頁面標題 -->
          <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">個人資料</h1>
            <p class="mt-1 text-sm text-gray-600">管理您的帳號設定與個人資訊</p>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-6 p-6">
            <!-- 頭像上傳 -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">頭像</label>
              <div class="flex items-center space-x-6">
                <div class="h-20 w-20 rounded-full bg-primary-600 flex items-center justify-center overflow-hidden">
                  <img
                    v-if="form.avatar || user?.avatar"
                    :src="avatarPreview || user.avatar"
                    :alt="user?.name"
                    class="h-full w-full object-cover"
                  />
                  <span v-else class="text-white text-lg font-medium">
                    {{ getUserInitials(user) }}
                  </span>
                </div>
                
                <div>
                  <input
                    ref="fileInput"
                    type="file"
                    accept="image/*"
                    @change="handleFileChange"
                    class="hidden"
                  />
                  <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    class="bg-white border border-gray-300 rounded-md py-2 px-3 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                  >
                    更換頭像
                  </button>
                  <p class="text-xs text-gray-500 mt-1">支援 JPG, PNG 格式，檔案大小不超過 2MB</p>
                </div>
              </div>
            </div>

            <!-- 基本資料 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">姓名 *</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
                <span v-if="errors.name" class="text-sm text-red-600">{{ errors.name[0] }}</span>
              </div>

              <div>
                <label for="display_name" class="block text-sm font-medium text-gray-700">暱稱</label>
                <input
                  id="display_name"
                  v-model="form.display_name"
                  type="text"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>

              <div class="md:col-span-2">
                <label for="email" class="block text-sm font-medium text-gray-700">電子郵件 *</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
                <span v-if="errors.email" class="text-sm text-red-600">{{ errors.email[0] }}</span>
              </div>

              <div class="md:col-span-2">
                <label for="organization_id" class="block text-sm font-medium text-gray-700">所屬單位</label>
                <select
                  id="organization_id"
                  v-model="form.organization_id"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                >
                  <option value="">請選擇單位</option>
                  <option v-for="org in organizations" :key="org.id" :value="org.id">
                    {{ org.name }}
                  </option>
                </select>
              </div>
            </div>

            <!-- 密碼更改 -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">更改密碼</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                  <label for="current_password" class="block text-sm font-medium text-gray-700">目前密碼</label>
                  <input
                    id="current_password"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    placeholder="如不更改密碼請留空"
                  />
                  <span v-if="errors.current_password" class="text-sm text-red-600">{{ errors.current_password[0] }}</span>
                </div>

                <div>
                  <label for="password" class="block text-sm font-medium text-gray-700">新密碼</label>
                  <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    placeholder="如不更改密碼請留空"
                  />
                  <span v-if="errors.password" class="text-sm text-red-600">{{ errors.password[0] }}</span>
                </div>

                <div>
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-700">確認新密碼</label>
                  <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    placeholder="再次輸入新密碼"
                  />
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
                {{ isLoading ? '儲存中...' : '儲存變更' }}
              </button>
            </div>
          </form>

          <!-- 成功訊息 -->
          <div v-if="successMessage" class="mx-6 mb-6">
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.93 10.7a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-800">
                    {{ successMessage }}
                  </p>
                </div>
              </div>
            </div>
          </div>

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
  </div>
</template>

<script>
import { reactive, ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'

export default {
  name: 'ProfilePage',
  setup() {
    const authStore = useAuthStore()
    const isLoading = ref(false)
    const successMessage = ref('')
    const errorMessage = ref('')
    const errors = ref({})
    const organizations = ref([])
    const avatarPreview = ref(null)
    
    const user = computed(() => authStore.user)
    
    const form = reactive({
      name: '',
      display_name: '',
      email: '',
      organization_id: '',
      current_password: '',
      password: '',
      password_confirmation: '',
      avatar: null
    })
    
    const getUserInitials = (user) => {
      if (!user) return ''
      const name = user.display_name || user.name
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }
    
    const fetchOrganizations = async () => {
      try {
        const response = await axios.get('/api/organizations')
        organizations.value = response.data
      } catch (error) {
        console.error('Failed to fetch organizations:', error)
      }
    }
    
    const handleFileChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        // 驗證檔案大小 (2MB)
        if (file.size > 2 * 1024 * 1024) {
          errorMessage.value = '檔案大小不能超過 2MB'
          return
        }
        
        // 驗證檔案類型
        if (!file.type.startsWith('image/')) {
          errorMessage.value = '請選擇圖片檔案'
          return
        }
        
        form.avatar = file
        
        // 產生預覽圖
        const reader = new FileReader()
        reader.onload = (e) => {
          avatarPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
      }
    }
    
    const handleSubmit = async () => {
      if (isLoading.value) return
      
      isLoading.value = true
      errorMessage.value = ''
      successMessage.value = ''
      errors.value = {}
      
      try {
        const formData = new FormData()
        formData.append('name', form.name)
        formData.append('display_name', form.display_name)
        formData.append('email', form.email)
        formData.append('organization_id', form.organization_id || '')
        
        if (form.current_password) {
          formData.append('current_password', form.current_password)
        }
        if (form.password) {
          formData.append('password', form.password)
          formData.append('password_confirmation', form.password_confirmation)
        }
        if (form.avatar) {
          formData.append('avatar', form.avatar)
        }
        
        const response = await axios.post('/api/profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        // 更新本地用戶資料
        await authStore.fetchUser()
        
        successMessage.value = '個人資料已成功更新'
        
        // 清空密碼欄位
        form.current_password = ''
        form.password = ''
        form.password_confirmation = ''
        form.avatar = null
        avatarPreview.value = null
        
      } catch (error) {
        if (error.response?.status === 422) {
          errors.value = error.response.data.errors || {}
        } else {
          errorMessage.value = error.response?.data?.message || '更新失敗，請稍後再試'
        }
      } finally {
        isLoading.value = false
      }
    }
    
    // 初始化表單資料
    onMounted(async () => {
      await fetchOrganizations()
      
      if (user.value) {
        form.name = user.value.name || ''
        form.display_name = user.value.display_name || ''
        form.email = user.value.email || ''
        form.organization_id = user.value.organization_id || ''
      }
    })
    
    return {
      user,
      form,
      isLoading,
      successMessage,
      errorMessage,
      errors,
      organizations,
      avatarPreview,
      getUserInitials,
      handleFileChange,
      handleSubmit
    }
  }
}
</script>