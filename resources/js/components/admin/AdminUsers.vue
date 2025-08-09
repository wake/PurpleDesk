<template>
  <div class="bg-white shadow rounded-lg">
    <!-- 頁面標題 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">使用者管理</h1>
          <p class="mt-1 text-sm text-gray-600">管理系統中的所有使用者帳號</p>
        </div>
        <button class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium">
          新增使用者
        </button>
      </div>
    </div>

    <!-- 搜尋列 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex space-x-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="搜尋使用者..."
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          />
        </div>
        <select
          v-model="selectedOrganization"
          class="px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
        >
          <option value="">所有單位</option>
          <option v-for="org in organizations" :key="org.id" :value="org.id">
            {{ org.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- 使用者列表 -->
    <div class="overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              使用者
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              所屬單位
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              註冊時間
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              狀態
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              操作
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in filteredUsers" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-primary-600 flex items-center justify-center overflow-hidden">
                  <img
                    v-if="user.avatar"
                    :src="user.avatar"
                    :alt="user.name"
                    class="h-full w-full object-cover"
                  />
                  <span v-else class="text-white text-sm font-medium">
                    {{ getUserInitials(user) }}
                  </span>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ user.display_name || user.name }}
                  </div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              <span v-if="user.organization" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                {{ user.organization.name }}
              </span>
              <span v-else class="text-gray-500">無</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                啟用
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button class="text-primary-600 hover:text-primary-900 mr-3">編輯</button>
              <button class="text-red-600 hover:text-red-900">停用</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 載入狀態 -->
    <div v-if="isLoading" class="text-center py-12">
      <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-primary-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-gray-500 mt-2">載入中...</p>
    </div>

    <!-- 空狀態 -->
    <div v-else-if="filteredUsers.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">沒有找到使用者</h3>
      <p class="mt-1 text-sm text-gray-500">請嘗試調整搜尋條件</p>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'AdminUsers',
  setup() {
    const users = ref([])
    const organizations = ref([])
    const isLoading = ref(true)
    const searchQuery = ref('')
    const selectedOrganization = ref('')
    
    const getUserInitials = (user) => {
      if (!user) return ''
      const name = user.display_name || user.name
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }
    
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('zh-TW', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    }
    
    const filteredUsers = computed(() => {
      let filtered = users.value
      
      // 依搜尋關鍵字篩選
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(user => 
          user.name.toLowerCase().includes(query) ||
          user.email.toLowerCase().includes(query) ||
          (user.display_name && user.display_name.toLowerCase().includes(query))
        )
      }
      
      // 依單位篩選
      if (selectedOrganization.value) {
        filtered = filtered.filter(user => 
          user.organization_id == selectedOrganization.value
        )
      }
      
      return filtered
    })
    
    const fetchUsers = async () => {
      try {
        isLoading.value = true
        const response = await axios.get('/api/admin/users')
        users.value = response.data
      } catch (error) {
        console.error('Failed to fetch users:', error)
      } finally {
        isLoading.value = false
      }
    }
    
    const fetchOrganizations = async () => {
      try {
        const response = await axios.get('/api/organizations')
        organizations.value = response.data
      } catch (error) {
        console.error('Failed to fetch organizations:', error)
      }
    }
    
    onMounted(async () => {
      await Promise.all([fetchUsers(), fetchOrganizations()])
    })
    
    return {
      users,
      organizations,
      isLoading,
      searchQuery,
      selectedOrganization,
      filteredUsers,
      getUserInitials,
      formatDate
    }
  }
}
</script>