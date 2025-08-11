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

    <!-- 組織列表 -->
    <div class="overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              組織資訊
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              成員數量
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              建立時間
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              操作
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="org in filteredOrganizations" :key="org.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded bg-primary-100 flex items-center justify-center overflow-hidden">
                  <img
                    v-if="org.logo_url"
                    :src="org.logo_url"
                    :alt="org.name"
                    class="h-full w-full object-cover"
                  />
                  <OfficeBuildingIcon v-else class="h-6 w-6 text-primary-600" />
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ org.name }}
                  </div>
                  <div class="text-sm text-gray-500">{{ org.description }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ org.users_count || 0 }} 位成員
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(org.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
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
    <div v-else-if="filteredOrganizations.length === 0" class="text-center py-12">
      <OfficeBuildingIcon class="mx-auto h-12 w-12 text-gray-400" />
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
              <!-- Logo 上傳 -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">組織 Logo</label>
                <div class="flex items-start space-x-4">
                  <div class="h-16 w-16 bg-primary-100 rounded flex items-center justify-center overflow-hidden">
                    <img
                      v-if="logoPreview || (editingOrganization?.logo_url)"
                      :src="logoPreview || editingOrganization.logo_url"
                      :alt="formData.name"
                      class="h-full w-full object-cover"
                    />
                    <OfficeBuildingIcon v-else class="h-8 w-8 text-primary-600" />
                  </div>
                  
                  <div class="flex-1">
                    <div
                      ref="logoDropZone"
                      @drop="handleLogoDrop"
                      @dragover="handleLogoDragOver"
                      @dragenter="handleLogoDragEnter"
                      @dragleave="handleLogoDragLeave"
                      :class="{
                        'border-primary-500 bg-primary-50': isLogoDragOver,
                        'border-gray-300': !isLogoDragOver
                      }"
                      class="border-2 border-dashed rounded-lg p-3 text-center transition-colors cursor-pointer hover:border-primary-400 hover:bg-primary-25"
                      @click="$refs.logoFileInput.click()"
                    >
                      <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                      </svg>
                      <p class="mt-1 text-xs text-gray-600">
                        <span class="font-medium text-primary-500">點擊上傳</span>
                        或拖曳檔案至此
                      </p>
                      <p class="text-xs text-gray-500">支援 JPG, PNG 格式，檔案大小不超過 2MB</p>
                    </div>
                    
                    <input
                      ref="logoFileInput"
                      type="file"
                      accept="image/*"
                      @change="handleLogoFileChange"
                      class="hidden"
                    />
                    
                    <!-- 移除 Logo 按鈕 -->
                    <div v-if="logoPreview || editingOrganization?.logo_url" class="mt-3">
                      <button
                        type="button"
                        @click="showRemoveLogoDialog"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                      >
                        <i class="bi bi-trash mr-2"></i>
                        移除 Logo
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
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
    
    <!-- 移除 Logo 確認對話框 -->
    <ConfirmDialog
      :show="showRemoveLogoConfirm"
      type="danger"
      title="移除組織 Logo"
      message="確定要移除組織 Logo 嗎？此操作無法復原。"
      confirm-text="移除"
      cancel-text="取消"
      :loading="isRemovingLogo"
      @confirm="confirmRemoveLogo"
      @cancel="cancelRemoveLogo"
    />
    
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
import { OfficeBuildingIcon } from '@heroicons/vue/outline'
import ConfirmDialog from '../common/ConfirmDialog.vue'
import LoadingSpinner from '../common/LoadingSpinner.vue'
import PaginationControl from '../common/PaginationControl.vue'

export default {
  name: 'AdminOrganizations',
  components: {
    OfficeBuildingIcon,
    ConfirmDialog,
    LoadingSpinner,
    PaginationControl
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
    const logoPreview = ref(null)
    const isLogoDragOver = ref(false)
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
          organizations.value = response.data.data
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
    
    const validateAndProcessLogoFile = (file) => {
      // 驗證檔案大小 (2MB)
      if (file.size > 2 * 1024 * 1024) {
        alert('檔案大小不能超過 2MB')
        return false
      }
      
      // 驗證檔案類型
      if (!file.type.startsWith('image/')) {
        alert('請選擇圖片檔案')
        return false
      }
      
      logoFile.value = file
      
      // 產生預覽圖
      const reader = new FileReader()
      reader.onload = (e) => {
        logoPreview.value = e.target.result
      }
      reader.readAsDataURL(file)
      
      return true
    }
    
    const handleLogoFileChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        validateAndProcessLogoFile(file)
      }
    }
    
    // Logo 拖曳事件處理
    const handleLogoDragEnter = (e) => {
      e.preventDefault()
      isLogoDragOver.value = true
    }
    
    const handleLogoDragOver = (e) => {
      e.preventDefault()
      isLogoDragOver.value = true
    }
    
    const handleLogoDragLeave = (e) => {
      e.preventDefault()
      isLogoDragOver.value = false
    }
    
    const handleLogoDrop = (e) => {
      e.preventDefault()
      isLogoDragOver.value = false
      
      const files = e.dataTransfer.files
      if (files.length > 0) {
        validateAndProcessLogoFile(files[0])
      }
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
        logoPreview.value = null
        
        // 清空檔案輸入
        const fileInput = document.querySelector('input[type="file"][accept="image/*"]')
        if (fileInput) {
          fileInput.value = ''
        }
        
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
      logoPreview.value = null
      logoFile.value = null
    }
    
    const cancelEdit = () => {
      showCreateModal.value = false
      editingOrganization.value = null
      formData.name = ''
      formData.description = ''
      formData.remove_avatar = false
      logoPreview.value = null
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
      logoPreview,
      isLogoDragOver,
      filteredOrganizations,
      formatDate,
      editOrganization,
      cancelEdit,
      saveOrganization,
      handleLogoFileChange,
      handleLogoDragEnter,
      handleLogoDragOver,
      handleLogoDragLeave,
      handleLogoDrop,
      showRemoveLogoDialog,
      confirmRemoveLogo,
      cancelRemoveLogo,
      showRemoveLogoConfirm,
      isRemovingLogo,
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