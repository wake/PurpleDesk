<template>
  <div class="bg-white shadow rounded-lg">
    <!-- 頁面標題 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">使用者管理</h1>
          <p class="mt-1 text-sm text-gray-600">管理系統中的所有使用者帳號</p>
        </div>
        <button 
          @click="showCreateModal = true"
          class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium"
        >
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
        <div class="relative">
          <select
            v-model="selectedOrganization"
            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: none;"
          >
            <option value="">所有組織</option>
            <option v-for="org in organizations" :key="org.id" :value="org.id">
              {{ org.name }}
            </option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- 使用者列表 -->
    <div class="overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
          <tr>
            <th class="w-1/4 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              使用者
            </th>
            <th class="w-1/4 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              所屬組織
            </th>
            <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              註冊時間
            </th>
            <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              狀態
            </th>
            <th class="w-1/6 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              操作
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in filteredUsers" :key="user.id">
            <td class="w-1/4 px-6 py-4">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-primary-600 flex items-center justify-center overflow-hidden flex-shrink-0">
                  <img
                    v-if="user.avatar_url"
                    :src="user.avatar_url"
                    :alt="user.name"
                    class="h-full w-full object-cover"
                  />
                  <span v-else class="text-white text-sm font-medium">
                    {{ getUserInitials(user) }}
                  </span>
                </div>
                <div class="ml-4 min-w-0 flex-1">
                  <div class="text-sm font-medium text-gray-900 truncate">
                    {{ user.display_name }}
                  </div>
                  <div class="text-sm text-gray-500 truncate" :title="user.email">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="w-1/4 px-6 py-4 text-sm text-gray-900">
              <div v-if="user.organizations && user.organizations.length > 0" class="space-y-1">
                <span 
                  v-for="org in user.organizations.slice(0, 2)" 
                  :key="org.id" 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 mr-1 truncate"
                  :title="org.name"
                >
                  {{ org.name }}
                </span>
                <span v-if="user.organizations.length > 2" class="text-xs text-gray-500">
                  +{{ user.organizations.length - 2 }} 個組織
                </span>
              </div>
              <span v-else class="text-gray-500">無</span>
            </td>
            <td class="w-1/6 px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="w-1/6 px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                啟用
              </span>
            </td>
            <td class="w-1/6 px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button 
                @click="editUser(user)"
                class="text-primary-600 hover:text-primary-900 mr-3"
              >
                編輯
              </button>
              <button class="text-red-600 hover:text-red-900">停用</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 空狀態 -->
    <div v-if="!isLoading && filteredUsers.length === 0" class="text-center py-12">
      <i class="bi bi-people-fill mx-auto text-5xl text-gray-400"></i>
      <h3 class="mt-2 text-sm font-medium text-gray-900">沒有找到使用者</h3>
      <p class="mt-1 text-sm text-gray-500">請嘗試調整搜尋條件</p>
    </div>

    <!-- 分頁導航 -->
    <PaginationControl 
      v-if="pagination.last_page > 1" 
      :pagination="pagination" 
      :is-loading="isLoading"
      @page-changed="changePage" 
      @per-page-changed="changePerPage" 
    />

    <!-- 新增/編輯使用者 Modal -->
    <div v-if="showCreateModal || editingUser" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full max-h-[90vh] overflow-y-auto">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ editingUser ? '編輯使用者' : '新增使用者' }}
            </h3>
            
            <div class="space-y-4">
              <!-- 基本資訊 -->
              <div>
                <div class="space-y-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">名稱 *</label>
                    <input
                      v-model="userForm.display_name"
                      type="text"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                      placeholder="用於系統中顯示的名稱"
                    />
                    <span v-if="userFormErrors.display_name" class="text-sm text-red-600">{{ userFormErrors.display_name[0] }}</span>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">姓名</label>
                    <input
                      v-model="userForm.full_name"
                      type="text"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                      placeholder="完整姓名（選填）"
                    />
                    <span v-if="userFormErrors.full_name" class="text-sm text-red-600">{{ userFormErrors.full_name[0] }}</span>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">電子郵件 *</label>
                    <input
                      v-model="userForm.email"
                      type="email"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    />
                    <span v-if="userFormErrors.email" class="text-sm text-red-600">{{ userFormErrors.email[0] }}</span>
                  </div>
                </div>
              </div>
              
              <div v-if="!editingUser">
                <label class="block text-sm font-medium text-gray-700">密碼 *</label>
                <input
                  v-model="userForm.password"
                  type="password"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
                <span v-if="userFormErrors.password" class="text-sm text-red-600">{{ userFormErrors.password[0] }}</span>
              </div>
              
              <div v-if="!editingUser">
                <label class="block text-sm font-medium text-gray-700">確認密碼 *</label>
                <input
                  v-model="userForm.password_confirmation"
                  type="password"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="saveUser"
              :disabled="isCreatingUser || !userForm.display_name || !userForm.email || (!editingUser && (!userForm.password || !userForm.password_confirmation))"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ isCreatingUser ? '處理中...' : (editingUser ? '更新' : '建立') }}
            </button>
            <button
              @click="cancelUserEdit"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              取消
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import LoadingSpinner from '../common/LoadingSpinner.vue'
import PaginationControl from '../common/PaginationControl.vue'

export default {
  name: 'AdminUsers',
  components: {
    LoadingSpinner,
    PaginationControl
  },
  setup() {
    const users = ref([])
    const pagination = ref({})
    const currentPage = ref(1)
    const perPage = ref(10)
    const organizations = ref([])
    const isLoading = ref(true)
    const searchQuery = ref('')
    const selectedOrganization = ref('')
    const showCreateModal = ref(false)
    const editingUser = ref(null)
    const isCreatingUser = ref(false)
    const userFormErrors = ref({})
    
    const userForm = ref({
      display_name: '',
      full_name: '',
      email: '',
      password: '',
      password_confirmation: ''
    })
    
    const getUserInitials = (user) => {
      if (!user) return ''
      const name = user.display_name || user.full_name || user.email
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
          (user.display_name && user.display_name.toLowerCase().includes(query)) ||
          (user.full_name && user.full_name.toLowerCase().includes(query)) ||
          user.email.toLowerCase().includes(query)
        )
      }
      
      // 依組織篩選
      if (selectedOrganization.value) {
        filtered = filtered.filter(user => 
          user.organizations && user.organizations.some(org => org.id == selectedOrganization.value)
        )
      }
      
      return filtered
    })
    
    const fetchUsers = async (page = 1) => {
      try {
        isLoading.value = true
        const response = await axios.get(`/api/admin/users?page=${page}&per_page=${perPage.value}`)
        users.value = response.data.data
        pagination.value = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          total: response.data.total
        }
        currentPage.value = response.data.current_page
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
    
    const resetUserForm = () => {
      userForm.value = {
        display_name: '',
        full_name: '',
        email: '',
        password: '',
        password_confirmation: ''
      }
      userFormErrors.value = {}
    }
    
    const cancelUserEdit = () => {
      showCreateModal.value = false
      editingUser.value = null
      resetUserForm()
    }
    
    const editUser = (user) => {
      editingUser.value = user
      userForm.value = {
        display_name: user.display_name || '',
        full_name: user.full_name || '',
        email: user.email || '',
        password: '',
        password_confirmation: ''
      }
      userFormErrors.value = {}
    }
    
    const saveUser = async () => {
      if (isCreatingUser.value) return
      
      isCreatingUser.value = true
      userFormErrors.value = {}
      
      try {
        if (editingUser.value) {
          // 更新使用者
          const updateData = {
            display_name: userForm.value.display_name,
            full_name: userForm.value.full_name,
            email: userForm.value.email
          }
          
          await axios.put(`/api/admin/users/${editingUser.value.id}`, updateData)
        } else {
          // 新增使用者
          await axios.post('/api/admin/users', userForm.value)
        }
        
        await fetchUsers()
        cancelUserEdit()
        
      } catch (error) {
        if (error.response?.status === 422) {
          userFormErrors.value = error.response.data.errors || {}
        } else {
          alert('操作失敗，請稍後再試')
        }
      } finally {
        isCreatingUser.value = false
      }
    }
    
    const changePage = (page) => {
      fetchUsers(page)
    }
    
    const changePerPage = (newPerPage) => {
      perPage.value = newPerPage
      currentPage.value = 1
      fetchUsers(1)
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
      showCreateModal,
      editingUser,
      isCreatingUser,
      userForm,
      userFormErrors,
      pagination,
      currentPage,
      perPage,
      getUserInitials,
      formatDate,
      editUser,
      saveUser,
      cancelUserEdit,
      changePage,
      changePerPage
    }
  }
}
</script>