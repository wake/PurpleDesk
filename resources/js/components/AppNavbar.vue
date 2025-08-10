<template>
  <nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <router-link to="/dashboard" class="flex-shrink-0 flex items-center">
            <div class="h-8 w-8 bg-primary-500 rounded-full flex items-center justify-center">
              <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.93 10.7a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
              </svg>
            </div>
            <span class="ml-2 text-xl font-bold text-gray-900">PurpleDesk</span>
            <span v-if="isAdmin" class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">管理員</span>
          </router-link>
        </div>
        
        <!-- 個人選單 -->
        <div class="flex items-center space-x-4">
          <!-- 通知圖示 -->
          <button class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100">
            <font-awesome-icon :icon="['far', 'bell']" class="h-5 w-5" />
          </button>

          <!-- 個人選單下拉 -->
          <div class="relative">
            <button
              @click="toggleUserMenu"
              class="flex items-center space-x-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            >
              <!-- 使用者頭像 -->
              <div class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center overflow-hidden">
                <img
                  v-if="user?.avatar_url"
                  :src="user.avatar_url"
                  :alt="user.name"
                  class="h-full w-full object-cover"
                />
                <span v-else class="text-white text-sm font-medium">
                  {{ getUserInitials(user) }}
                </span>
              </div>
              
              <!-- 下拉箭頭 -->
              <font-awesome-icon icon="chevron-down" class="h-4 w-4 text-gray-500" :class="{ 'rotate-180': showUserMenu }" />
            </button>

            <!-- 下拉選單 -->
            <div
              v-show="showUserMenu"
              class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
              @click.away="showUserMenu = false"
            >
              <!-- 使用者資訊 -->
              <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                  <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center overflow-hidden">
                    <img
                      v-if="user?.avatar_url"
                      :src="user.avatar_url"
                      :alt="user.name"
                      class="h-full w-full object-cover"
                    />
                    <span v-else class="text-white font-medium">
                      {{ getUserInitials(user) }}
                    </span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ user?.display_name || user?.name }}
                    </p>
                    <p class="text-sm text-gray-500 truncate">
                      {{ user?.email }}
                    </p>
                    <p v-if="user?.organizations?.length > 0" class="text-xs text-gray-400 truncate">
                      {{ user.organizations[0].name }}
                      <span v-if="user.organizations.length > 1" class="text-gray-300">
                        +{{ user.organizations.length - 1 }} 個組織
                      </span>
                    </p>
                  </div>
                </div>
              </div>

              <!-- 選單項目 -->
              <div class="py-1">
                <!-- 個人資料 -->
                <router-link
                  to="/profile"
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false"
                >
                  <font-awesome-icon :icon="['far', 'user']" class="mr-3 h-4 w-4 text-gray-400" />
                  個人資料
                </router-link>

                <!-- 設定 -->
                <router-link
                  to="/settings"
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false"
                >
                  <font-awesome-icon :icon="['far', 'cog']" class="mr-3 h-4 w-4 text-gray-400" />
                  設定
                </router-link>

                <!-- 管理員專用選項 -->
                <template v-if="isAdmin">
                  <div class="border-t border-gray-200 my-1"></div>
                  
                  <router-link
                    to="/admin/users"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false"
                  >
                    <font-awesome-icon :icon="['far', 'users']" class="mr-3 h-4 w-4 text-gray-400" />
                    使用者管理
                  </router-link>
                  
                  <router-link
                    to="/admin/organizations"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false"
                  >
                    <font-awesome-icon :icon="['far', 'building']" class="mr-3 h-4 w-4 text-gray-400" />
                    組織管理
                  </router-link>
                  
                  <router-link
                    to="/admin/system"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false"
                  >
                    <font-awesome-icon :icon="['far', 'cog']" class="mr-3 h-4 w-4 text-gray-400" />
                    系統設定
                  </router-link>
                </template>

                <div class="border-t border-gray-200 my-1"></div>
                
                <!-- 登出 -->
                <button
                  @click="handleLogout"
                  class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left"
                >
                  <font-awesome-icon icon="sign-out-alt" class="mr-3 h-4 w-4 text-gray-400" />
                  登出
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

export default {
  name: 'AppNavbar',
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    const showUserMenu = ref(false)
    
    const user = computed(() => authStore.user)
    
    // 判斷是否為管理員
    const isAdmin = computed(() => {
      return user.value?.email === 'admin@purpledesk.com'
    })
    
    const getUserInitials = (user) => {
      if (!user) return ''
      const name = user.display_name || user.name
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }
    
    const toggleUserMenu = () => {
      showUserMenu.value = !showUserMenu.value
    }
    
    const handleLogout = async () => {
      try {
        showUserMenu.value = false
        await authStore.logout()
        router.push('/login')
      } catch (error) {
        console.error('登出錯誤:', error)
      }
    }
    
    // 點擊外部關閉選單
    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative')) {
        showUserMenu.value = false
      }
    }
    
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
    })
    
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })
    
    return {
      user,
      isAdmin,
      showUserMenu,
      getUserInitials,
      toggleUserMenu,
      handleLogout
    }
  }
}
</script>