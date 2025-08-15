<template>
  <div class="bg-white shadow rounded-lg">
    <!-- 頁面標題 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">組織管理</h1>
          <p class="mt-1 text-sm text-gray-600">管理系統中的所有組織組織</p>
        </div>
        <button 
          @click="showCreateModal = true"
          class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium"
        >
          新增組織
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
            placeholder="搜尋組織..."
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          />
        </div>
      </div>
    </div>

    <!-- 載入狀態 -->
    <LoadingSpinner v-if="isLoading" size="lg" />
    
    <!-- 組織列表 -->
    <div v-else class="overflow-x-auto">
      <table class="w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
          <tr>
            <th class="w-2/5 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              組織資訊
            </th>
            <th class="w-1/4 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              成員
            </th>
            <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              建立時間
            </th>
            <th class="w-1/6 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              操作
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="org in filteredOrganizations" :key="org.id">
            <td class="w-2/5 px-6 py-4">
              <div class="flex items-center">
                <IconDisplay 
                  :icon-data="org.avatar_data" 
                  size="md" 
                  :title="org.name"
                />
                <div class="ml-4 min-w-0 flex-1" style="max-width: calc(100% - 3rem);">
                  <div class="text-sm font-medium text-gray-900 truncate">
                    {{ org.name }}
                  </div>
                  <div class="text-sm text-gray-500 truncate">
                    {{ org.description || '無描述' }}
                  </div>
                </div>
              </div>
            </td>
            <td class="w-1/4 px-6 py-4 whitespace-nowrap align-middle">
              <UserAvatarGroup :users="org.users" />
            </td>
            <td class="w-1/6 px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(org.created_at) }}
            </td>
            <td class="w-1/6 px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <router-link
                :to="`/admin/organizations/${org.id}/manage`"
                class="text-primary-600 hover:text-primary-900 mr-3"
              >
                管理
              </router-link>
              <button 
                @click="showDeleteDialog(org)"
                class="text-red-600 hover:text-red-900"
                :disabled="org.users_count > 0"
                :class="{ 'opacity-50 cursor-not-allowed': org.users_count > 0 }"
              >
                刪除
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 空狀態 -->
    <div v-if="!isLoading && filteredOrganizations.length === 0" class="text-center py-12">
      <div class="mx-auto h-12 w-12 text-gray-400 flex items-center justify-center">
        <i class="bi bi-building text-4xl opacity-50"></i>
      </div>
      <h3 class="mt-2 text-sm font-medium text-gray-900">沒有找到組織</h3>
      <p class="mt-1 text-sm text-gray-500">請嘗試調整搜尋條件或新增組織</p>
    </div>

    <!-- 分頁導航 -->
    <PaginationControl 
      v-if="pagination.last_page > 1" 
      :pagination="pagination" 
      :is-loading="isLoading"
      @page-changed="changePage" 
      @per-page-changed="changePerPage" 
    />

    <!-- 新增/編輯組織 Modal（簡化版） -->
    <div v-if="showCreateModal || editingOrganization" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ editingOrganization ? '編輯組織' : '新增組織' }}
            </h3>
            
            <div class="space-y-4">
              <!-- Logo 設定 -->
              <ImageField
                ref="logoField"
                label="組織 Logo"
                :current-image-url="editingOrganization?.logo_url"
                :image-alt="formData.name"
                :initials="formData.name?.substring(0, 2)?.toUpperCase()"
                size="medium"
                shape="rounded"
                remove-button-text="移除 Logo"
                remove-confirm-title="移除組織 Logo"
                remove-confirm-message="確定要移除組織 Logo 嗎？此操作無法復原。"
                :uploading="isLoading"
                :removing="isRemovingLogo"
                @mode-changed="handleModeChanged"
                @settings-changed="handleSettingsChanged"
                @file-selected="handleLogoSelected"
                @file-error="handleLogoError"
                @remove="handleLogoRemove"
                @success="handleLogoSuccess"
                @error="handleLogoError"
              />
              
              <div>
                <label class="block text-sm font-medium text-gray-700">組織名稱</label>
                <input
                  v-model="formData.name"
                  type="text"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">描述</label>
                <textarea
                  v-model="formData.description"
                  rows="3"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                ></textarea>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="saveOrganization"
              :disabled="!formData.name"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ editingOrganization ? '更新' : '建立' }}
            </button>
            <button
              @click="cancelEdit"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              取消
            </button>
          </div>
        </div>
      </div>
    </div>
    
    
    <!-- 刪除組織確認對話框 -->
    <ConfirmDialog
      :show="showDeleteConfirm"
      type="danger"
      title="刪除組織"
      :message="`確定要刪除組織「${deleteTarget?.name}」嗎？此操作無法復原。`"
      confirm-text="刪除"
      cancel-text="取消"
      :loading="isDeleting"
      @confirm="confirmDeleteOrganization"
      @cancel="cancelDeleteOrganization"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, reactive } from 'vue'
import axios from 'axios'
import ConfirmDialog from '../common/ConfirmDialog.vue'
import LoadingSpinner from '../common/LoadingSpinner.vue'
import PaginationControl from '../common/PaginationControl.vue'
import UserAvatarGroup from '../common/UserAvatarGroup.vue'
import ImageField from '../common/ImageField.vue'
import IconDisplay from '../common/IconDisplay.vue'

export default {
  name: 'AdminOrganizations',
  components: {
    ConfirmDialog,
    LoadingSpinner,
    PaginationControl,
    UserAvatarGroup,
    ImageField,
    IconDisplay
  },
  setup() {
    const organizations = ref([])
    const pagination = ref({})
    const currentPage = ref(1)
    const perPage = ref(10)
    const isLoading = ref(true)
    const searchQuery = ref('')
    const showCreateModal = ref(false)
    const editingOrganization = ref(null)
    const logoFile = ref(null)
    const showRemoveLogoConfirm = ref(false)
    const isRemovingLogo = ref(false)
    const showDeleteConfirm = ref(false)
    const isDeleting = ref(false)
    const deleteTarget = ref(null)
    
    const formData = reactive({
      name: '',
      description: '',
      remove_avatar: false
    })
    
    const filteredOrganizations = computed(() => {
      if (!searchQuery.value) return organizations.value
      
      const query = searchQuery.value.toLowerCase()
      return organizations.value.filter(org => 
        org.name.toLowerCase().includes(query) ||
        (org.description && org.description.toLowerCase().includes(query))
      )
    })
    
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('zh-TW', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    }
    
    
    const fetchOrganizations = async (page = 1) => {
      try {
        isLoading.value = true
        console.log('AdminOrganizations: Fetching organizations, page:', page)
        const response = await axios.get(`/api/admin/organizations?page=${page}&per_page=${perPage.value}`)
        console.log('AdminOrganizations: API response:', response.data)
        
        // 處理回應資料
        if (response.data && response.data.data) {
          // 解析 JSON 字串為物件
          organizations.value = response.data.data.map(org => {
            let avatarData = org.avatar_data
            let logoData = org.logo_data
            
            // 處理組織 avatar_data 的雙重編碼
            if (typeof avatarData === 'string') {
              try {
                avatarData = JSON.parse(avatarData)
                if (typeof avatarData === 'string') {
                  avatarData = JSON.parse(avatarData)
                }
              } catch (e) {
                console.warn('Failed to parse org avatar_data:', avatarData, e)
                avatarData = null
              }
            }
            
            // 處理組織 logo_data 的雙重編碼
            if (typeof logoData === 'string') {
              try {
                logoData = JSON.parse(logoData)
                if (typeof logoData === 'string') {
                  logoData = JSON.parse(logoData)
                }
              } catch (e) {
                console.warn('Failed to parse org logo_data:', logoData, e)
                logoData = null
              }
            }
            
            return {
              ...org,
              avatar_data: avatarData,
              logo_data: logoData,
              users: org.users?.map(user => {
                let avatarData = user.avatar_data
                
                // 處理雙重編碼的 JSON 字串
                if (typeof avatarData === 'string') {
                  try {
                    avatarData = JSON.parse(avatarData)
                    // 如果解析後仍是字串，再解析一次
                    if (typeof avatarData === 'string') {
                      avatarData = JSON.parse(avatarData)
                    }
                  } catch (e) {
                    console.warn('Failed to parse user avatar_data:', avatarData, e)
                    avatarData = null
                  }
                }
                
                return {
                  ...user,
                  avatar_data: avatarData
                }
              }) || []
            }
          });
          pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total
          }
          currentPage.value = response.data.current_page
          console.log('AdminOrganizations: Organizations loaded:', organizations.value.length)
        } else {
          console.warn('AdminOrganizations: Unexpected API response format:', response.data)
          organizations.value = []
        }
      } catch (error) {
        console.error('AdminOrganizations: Failed to fetch organizations:', error)
        console.error('Error response:', error.response?.data)
        console.error('Error status:', error.response?.status)
        organizations.value = []
      } finally {
        isLoading.value = false
      }
    }
    
    const handleLogoSelected = (file) => {
      logoFile.value = file
    }
    
    const handleLogoError = (error) => {
      alert(error)
    }
    
    const handleLogoRemove = async () => {
      await confirmRemoveLogo()
    }
    
    const handleLogoSuccess = (message) => {
      console.log('Logo success:', message)
    }
    
    const handleModeChanged = (data) => {
      console.log('Logo mode changed:', data)
    }
    
    const handleSettingsChanged = (data) => {
      console.log('Logo settings changed:', data)
    }
    
    const showRemoveLogoDialog = () => {
      showRemoveLogoConfirm.value = true
    }
    
    const confirmRemoveLogo = async () => {
      isRemovingLogo.value = true
      
      try {
        // 如果正在編輯現有組織，立即移除 Logo
        if (editingOrganization.value) {
          // 立即發送請求到後端移除 Logo
          const formDataToSend = new FormData()
          formDataToSend.append('name', formData.name)
          formDataToSend.append('description', formData.description || '')
          formDataToSend.append('remove_avatar', '1')
          
          await axios.post(`/api/organizations/${editingOrganization.value.id}`, formDataToSend, {
            headers: { 'Content-Type': 'multipart/form-data' },
            params: { '_method': 'PUT' }
          })
          
          // 刷新組織列表
          await fetchOrganizations()
        }
        
        // 清空本地狀態
        logoFile.value = null
        formData.remove_avatar = false
        
        // 關閉對話框
        showRemoveLogoConfirm.value = false
        
      } catch (error) {
        console.error('移除 Logo 失敗:', error)
        const errorMsg = error.response?.data?.message || '移除 Logo 失敗，請稍後再試'
        alert(errorMsg)
      } finally {
        isRemovingLogo.value = false
      }
    }
    
    const cancelRemoveLogo = () => {
      showRemoveLogoConfirm.value = false
    }

    const editOrganization = (org) => {
      editingOrganization.value = org
      formData.name = org.name
      formData.description = org.description || ''
      logoFile.value = null
    }
    
    const cancelEdit = () => {
      showCreateModal.value = false
      editingOrganization.value = null
      formData.name = ''
      formData.description = ''
      formData.remove_avatar = false
      logoFile.value = null
    }
    
    const saveOrganization = async () => {
      try {
        const formDataToSend = new FormData()
        formDataToSend.append('name', formData.name)
        formDataToSend.append('description', formData.description || '')
        
        if (logoFile.value) {
          formDataToSend.append('avatar', logoFile.value)
        }
        if (formData.remove_avatar) {
          formDataToSend.append('remove_avatar', '1')
        }
        
        if (editingOrganization.value) {
          // 更新
          await axios.post(`/api/organizations/${editingOrganization.value.id}`, formDataToSend, {
            headers: { 'Content-Type': 'multipart/form-data' },
            params: { '_method': 'PUT' }
          })
        } else {
          // 新增
          await axios.post('/api/organizations', formDataToSend, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        }
        
        await fetchOrganizations(currentPage.value)
        cancelEdit()
      } catch (error) {
        console.error('Failed to save organization:', error)
        alert('操作失敗，請稍後再試')
      }
    }
    
    const showDeleteDialog = (org) => {
      deleteTarget.value = org
      showDeleteConfirm.value = true
    }
    
    const confirmDeleteOrganization = async () => {
      if (!deleteTarget.value) return
      
      isDeleting.value = true
      
      try {
        await axios.delete(`/api/organizations/${deleteTarget.value.id}`)
        await fetchOrganizations(currentPage.value)
        showDeleteConfirm.value = false
        deleteTarget.value = null
      } catch (error) {
        console.error('Failed to delete organization:', error)
        alert('刪除失敗，請稍後再試')
      } finally {
        isDeleting.value = false
      }
    }
    
    const cancelDeleteOrganization = () => {
      showDeleteConfirm.value = false
      deleteTarget.value = null
    }
    
    const changePage = (page) => {
      fetchOrganizations(page)
    }
    
    const changePerPage = (newPerPage) => {
      perPage.value = newPerPage
      currentPage.value = 1
      fetchOrganizations(1)
    }
    
    onMounted(() => {
      fetchOrganizations()
    })
    
    return {
      organizations,
      isLoading,
      searchQuery,
      showCreateModal,
      editingOrganization,
      formData,
      filteredOrganizations,
      formatDate,
      editOrganization,
      cancelEdit,
      saveOrganization,
      handleLogoSelected,
      handleLogoError,
      handleLogoRemove,
      handleLogoSuccess,
      handleModeChanged,
      handleSettingsChanged,
      showDeleteDialog,
      confirmDeleteOrganization,
      cancelDeleteOrganization,
      showDeleteConfirm,
      isDeleting,
      deleteTarget,
      pagination,
      currentPage,
      perPage,
      changePage,
      changePerPage
    }
  }
}
</script>