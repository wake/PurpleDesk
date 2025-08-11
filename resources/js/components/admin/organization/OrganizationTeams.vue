<template>
  <div>
    <!-- 搜尋欄 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex space-x-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="搜尋團隊..."
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          />
        </div>
        <button 
          v-if="showCreateButton"
          @click="$emit('show-create')"
          class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap"
        >
          建立團隊
        </button>
      </div>
    </div>

    <!-- 團隊列表 -->
    <div class="overflow-hidden">
      <table class="w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
          <tr>
            <th class="w-1/3 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              團隊資訊
            </th>
            <th class="w-1/5 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              管理者
            </th>
            <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
          <tr v-for="team in filteredTeams" :key="team.id">
            <td class="w-1/3 px-6 py-4">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded bg-blue-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                  <img
                    v-if="team.avatar_url"
                    :src="team.avatar_url"
                    :alt="team.name"
                    class="h-full w-full object-cover"
                  />
                  <svg v-else class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div class="ml-4 min-w-0 flex-1" style="max-width: calc(100% - 3rem);">
                  <div class="text-sm font-medium text-gray-900 truncate">
                    {{ team.name }}
                  </div>
                  <div class="text-sm text-gray-500 truncate">{{ team.description || '無描述' }}</div>
                </div>
              </div>
            </td>
            <td class="w-1/5 px-6 py-4">
              <UserAvatarGroup 
                :users="getTeamLeaders(team)" 
                theme="admin"
                member-text="個管理者"
                empty-text="無管理者"
              />
            </td>
            <td class="w-1/6 px-6 py-4 whitespace-nowrap">
              <UserAvatarGroup :users="getTeamMembers(team)" />
            </td>
            <td class="w-1/6 px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(team.created_at) }}
            </td>
            <td class="w-1/6 px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button 
                @click="manageTeam(team)"
                class="text-primary-600 hover:text-primary-900 mr-3"
              >
                管理
              </button>
              <button 
                @click="editTeam(team)"
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                編輯
              </button>
              <button 
                @click="deleteTeam(team)"
                class="text-red-600 hover:text-red-900"
                :disabled="team.users && team.users.length > 0"
                :class="{ 'opacity-50 cursor-not-allowed': team.users && team.users.length > 0 }"
              >
                刪除
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 空狀態 -->
    <div v-if="!isLoading && filteredTeams.length === 0" class="text-center py-12">
      <div class="mx-auto h-12 w-12 text-gray-400 flex items-center justify-center">
        <i class="bi bi-people-fill text-4xl opacity-50"></i>
      </div>
      <h3 class="mt-2 text-sm font-medium text-gray-900">沒有找到團隊</h3>
      <p class="mt-1 text-sm text-gray-500">請嘗試調整搜尋條件或建立新團隊</p>
    </div>

    <!-- 分頁導航 -->
    <PaginationControl 
      v-if="teamsPagination.last_page > 1" 
      :pagination="teamsPagination" 
      :is-loading="isLoading"
      @page-changed="changeTeamsPage" 
      @per-page-changed="changePerPage" 
    />

    <!-- 建立/編輯團隊 Modal -->
    <div v-if="showCreateModal || editingTeam" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ editingTeam ? '編輯團隊' : '建立團隊' }}
            </h3>
            
            <div class="space-y-4">
              <!-- 團隊頭像 -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">團隊頭像</label>
                <div class="flex items-start space-x-4">
                  <div class="h-16 w-16 bg-blue-100 rounded flex items-center justify-center overflow-hidden">
                    <img
                      v-if="avatarPreview || (editingTeam && editingTeam.avatar_url)"
                      :src="avatarPreview || editingTeam.avatar_url"
                      :alt="teamForm.name"
                      class="h-full w-full object-cover"
                    />
                    <svg v-else class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </div>
                  
                  <div class="flex-1">
                    <button
                      type="button"
                      @click="$refs.avatarInput.click()"
                      class="w-full flex justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                      選擇檔案
                    </button>
                    <p class="mt-1 text-sm text-gray-500">支援 JPG, PNG 格式，檔案大小不超過 2MB</p>
                    
                    <input
                      ref="avatarInput"
                      type="file"
                      accept="image/*"
                      @change="handleAvatarChange"
                      class="hidden"
                    />
                  </div>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">團隊名稱 *</label>
                <input
                  v-model="teamForm.name"
                  type="text"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">團隊描述</label>
                <textarea
                  v-model="teamForm.description"
                  rows="3"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  placeholder="描述團隊的職責和目標..."
                ></textarea>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="saveTeam"
              :disabled="isSaving"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ isSaving ? '儲存中...' : (editingTeam ? '更新' : '建立') }}
            </button>
            <button
              @click="closeModal"
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
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import LoadingSpinner from '../../common/LoadingSpinner.vue'
import PaginationControl from '../../common/PaginationControl.vue'
import UserAvatarGroup from '../../common/UserAvatarGroup.vue'

export default {
  name: 'OrganizationTeams',
  components: {
    LoadingSpinner,
    PaginationControl,
    UserAvatarGroup
  },
  props: {
    organization: {
      type: Object,
      required: true
    },
    showCreateButton: {
      type: Boolean,
      default: true
    },
    showCreateModal: {
      type: Boolean,
      default: false
    }
  },
  emits: ['refresh', 'success', 'show-create', 'close-create'],
  setup(props, { emit }) {
    const isLoading = ref(false)
    const searchQuery = ref('')
    const teams = ref([])
    const teamsPagination = ref({})
    const currentTeamsPage = ref(1)
    const perPage = ref(10)
    const editingTeam = ref(null)
    const isSaving = ref(false)
    const avatarPreview = ref(null)
    
    const teamForm = ref({
      name: '',
      description: '',
      avatar: null
    })
    
    const filteredTeams = computed(() => {
      if (!teams.value) return []
      
      let filtered = [...teams.value]
      
      // 搜尋篩選
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(team => 
          team.name.toLowerCase().includes(query) ||
          (team.description && team.description.toLowerCase().includes(query))
        )
      }
      
      return filtered
    })
    
    const getTeamLeaders = (team) => {
      if (!team.users) return []
      return team.users.filter(user => user.pivot.role === 'lead')
    }
    
    const getTeamMembers = (team) => {
      if (!team.users) return []
      return team.users.filter(user => user.pivot.role !== 'lead')
    }
    
    
    const formatDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('zh-TW')
    }
    
    const fetchTeams = async (page = 1) => {
      if (!props.organization?.id) return
      
      isLoading.value = true
      try {
        const response = await axios.get(`/api/organizations/${props.organization.id}/teams?page=${page}&per_page=${perPage.value}`)
        
        // 處理分頁後的團隊數據
        if (response.data.teams && response.data.teams.data) {
          teams.value = response.data.teams.data
          teamsPagination.value = {
            current_page: response.data.teams.current_page,
            last_page: response.data.teams.last_page,
            per_page: response.data.teams.per_page,
            total: response.data.teams.total
          }
          currentTeamsPage.value = response.data.teams.current_page
        } else {
          // 兼容舊版本的非分頁響應
          teams.value = response.data.teams || []
        }
      } catch (error) {
        console.error('Failed to fetch teams:', error)
      } finally {
        isLoading.value = false
      }
    }
    
    const handleAvatarChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        // 驗證檔案大小和類型
        if (file.size > 2 * 1024 * 1024) {
          // TODO: 改用統一的錯誤提示
          alert('檔案大小不能超過 2MB')
          return
        }
        
        if (!file.type.startsWith('image/')) {
          // TODO: 改用統一的錯誤提示
          alert('請選擇圖片檔案')
          return
        }
        
        teamForm.value.avatar = file
        
        // 產生預覽
        const reader = new FileReader()
        reader.onload = (e) => {
          avatarPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
      }
    }
    
    const editTeam = (team) => {
      editingTeam.value = team
      teamForm.value = {
        name: team.name,
        description: team.description || '',
        avatar: null
      }
      avatarPreview.value = null
    }
    
    const manageTeam = (team) => {
      // 實作團隊詳細管理功能
      console.log('Manage team:', team)
      // TODO: 改用統一的提示
      alert('團隊詳細管理功能開發中...')
    }
    
    const saveTeam = async () => {
      if (!teamForm.value.name.trim()) {
        // TODO: 改用統一的錯誤提示
        alert('請輸入團隊名稱')
        return
      }
      
      isSaving.value = true
      try {
        const formData = new FormData()
        formData.append('name', teamForm.value.name)
        formData.append('description', teamForm.value.description || '')
        
        if (teamForm.value.avatar) {
          formData.append('avatar', teamForm.value.avatar)
        }
        
        if (editingTeam.value) {
          // 更新團隊
          await axios.post(`/api/organizations/${props.organization.id}/teams/${editingTeam.value.id}?_method=PUT`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        } else {
          // 建立團隊
          await axios.post(`/api/organizations/${props.organization.id}/teams`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        }
        
        closeModal()
        await fetchTeams(currentTeamsPage.value)
        emit('success', editingTeam.value ? '團隊已更新' : '團隊已建立')
        emit('refresh')
      } catch (error) {
        console.error('Failed to save team:', error)
        // TODO: 改用統一的錯誤提示
        alert(editingTeam.value ? '更新團隊失敗' : '建立團隊失敗')
      } finally {
        isSaving.value = false
      }
    }
    
    const deleteTeam = async (team) => {
      if (!confirm(`確定要刪除團隊「${team.name}」嗎？`)) {
        return
      }
      
      try {
        await axios.delete(`/api/organizations/${props.organization.id}/teams/${team.id}`)
        await fetchTeams(currentTeamsPage.value)
        emit('success', `團隊「${team.name}」已刪除`)
        emit('refresh')
      } catch (error) {
        console.error('Failed to delete team:', error)
        // TODO: 改用統一的錯誤提示
        alert('刪除團隊失敗')
      }
    }
    
    const closeModal = () => {
      emit('close-create')
      editingTeam.value = null
      teamForm.value = { name: '', description: '', avatar: null }
      avatarPreview.value = null
    }
    
    const changeTeamsPage = (page) => {
      fetchTeams(page)
    }
    
    const changePerPage = (newPerPage) => {
      perPage.value = newPerPage
      currentTeamsPage.value = 1
      fetchTeams(1)
    }
    
    watch(() => props.organization, () => {
      if (props.organization) {
        fetchTeams()
      }
    }, { immediate: true })
    
    return {
      isLoading,
      searchQuery,
      teams,
      filteredTeams,
      editingTeam,
      isSaving,
      avatarPreview,
      showCreateModal: computed(() => props.showCreateModal),
      teamForm,
      getTeamLeaders,
      getTeamMembers,
      formatDate,
      handleAvatarChange,
      editTeam,
      manageTeam,
      saveTeam,
      deleteTeam,
      closeModal,
      teamsPagination,
      currentTeamsPage,
      perPage,
      changeTeamsPage,
      changePerPage
    }
  }
}
</script>