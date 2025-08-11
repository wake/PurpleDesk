<template>
  <div>
    <!-- 篩選欄 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex space-x-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="搜尋成員..."
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          />
        </div>
        <select
          v-model="selectedTeam"
          class="px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
        >
          <option value="">所有團隊</option>
          <option v-for="team in teams" :key="team.id" :value="team.id">
            {{ team.name }}
          </option>
        </select>
        <select
          v-model="selectedRole"
          class="px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
        >
          <option value="">所有角色</option>
          <option value="owner">擁有者</option>
          <option value="admin">管理員</option>
          <option value="member">成員</option>
        </select>
        <button 
          v-if="showInviteButton"
          @click="$emit('show-invite')"
          class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap"
        >
          邀請成員
        </button>
      </div>
    </div>

    <!-- 成員列表 -->
    <div class="overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              成員
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              角色
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              所屬團隊
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              加入時間
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              操作
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="member in filteredMembers" :key="member.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center overflow-hidden">
                  <img
                    v-if="member.avatar_url"
                    :src="member.avatar_url"
                    :alt="member.name"
                    class="h-full w-full object-cover"
                  />
                  <span v-else class="text-white font-medium">
                    {{ getUserInitials(member) }}
                  </span>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ member.display_name || member.name }}
                  </div>
                  <div class="text-sm text-gray-500">{{ member.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                  member.pivot.role === 'owner' 
                    ? 'bg-red-100 text-red-800' 
                    : member.pivot.role === 'admin'
                    ? 'bg-yellow-100 text-yellow-800'
                    : 'bg-blue-100 text-blue-800'
                ]"
              >
                {{ member.pivot.role === 'owner' ? '擁有者' : member.pivot.role === 'admin' ? '管理員' : '成員' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="space-y-1">
                <span 
                  v-for="team in getMemberTeams(member)" 
                  :key="team.id"
                  :class="[
                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mr-1',
                    team.pivot.role === 'lead' 
                      ? 'bg-yellow-100 text-yellow-800' 
                      : 'bg-blue-100 text-blue-800'
                  ]"
                >
                  {{ team.name }}
                  <span v-if="team.pivot.role === 'lead'" class="ml-1">👑</span>
                </span>
                <div v-if="getMemberTeams(member).length === 0" class="text-sm text-gray-400">
                  無團隊
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(member.pivot.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button 
                @click="editMember(member)"
                class="text-primary-600 hover:text-primary-900 mr-3"
              >
                編輯
              </button>
              <button 
                @click="removeMember(member)"
                :disabled="member.pivot.role === 'owner'"
                class="text-red-600 hover:text-red-900 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                移除
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 載入狀態 -->
    <LoadingSpinner v-if="isLoading" />

    <!-- 空狀態 -->
    <div v-else-if="filteredMembers.length === 0" class="text-center py-12">
      <div class="mx-auto h-12 w-12 text-gray-400 flex items-center justify-center">
        <i class="bi bi-person-x-fill text-4xl"></i>
      </div>
      <h3 class="mt-2 text-sm font-medium text-gray-900">沒有找到成員</h3>
      <p class="mt-1 text-sm text-gray-500">請嘗試調整篩選條件或邀請新成員</p>
    </div>

    <!-- 分頁導航 -->
    <div v-if="membersPagination.last_page > 1" class="px-6 py-4 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="changeMembersPage(currentMembersPage - 1)"
            :disabled="currentMembersPage <= 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
          >
            上一頁
          </button>
          <button
            @click="changeMembersPage(currentMembersPage + 1)"
            :disabled="currentMembersPage >= membersPagination.last_page"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
          >
            下一頁
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              顯示第
              <span class="font-medium">{{ (currentMembersPage - 1) * membersPagination.per_page + 1 }}</span>
              到
              <span class="font-medium">{{ Math.min(currentMembersPage * membersPagination.per_page, membersPagination.total) }}</span>
              筆，共
              <span class="font-medium">{{ membersPagination.total }}</span>
              筆成員
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
              <button
                @click="changeMembersPage(currentMembersPage - 1)"
                :disabled="currentMembersPage <= 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
              >
                ‹
              </button>
              <button
                v-for="page in Math.min(membersPagination.last_page, 5)"
                :key="page"
                @click="changeMembersPage(page)"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                  page === currentMembersPage
                    ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="changeMembersPage(currentMembersPage + 1)"
                :disabled="currentMembersPage >= membersPagination.last_page"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
              >
                ›
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- 邀請成員 Modal -->
    <div v-if="showInviteModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              邀請成員
            </h3>
            
            <div class="space-y-4">
              <div class="relative">
                <label class="block text-sm font-medium text-gray-700">選擇成員 *</label>
                <div class="relative">
                  <input
                    v-model="inviteForm.email"
                    @input="searchUsersForInvite"
                    @focus="showUserDropdown = true"
                    @blur="hideUserDropdown"
                    type="text"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    placeholder="輸入名稱或郵件地址搜尋..."
                    autocomplete="off"
                  />
                  
                  <!-- 自動完成下拉清單 -->
                  <div 
                    v-if="showUserDropdown && (searchUsers.length > 0 || isSearching)"
                    class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-300 max-h-60 overflow-auto"
                  >
                    <div v-if="isSearching" class="px-4 py-2 text-sm text-gray-500">
                      搜尋中...
                    </div>
                    <div v-else-if="searchUsers.length === 0" class="px-4 py-2 text-sm text-gray-500">
                      找不到符合的使用者
                    </div>
                    <div v-else>
                      <button
                        v-for="user in searchUsers"
                        :key="user.id"
                        @mousedown="selectUser(user)"
                        class="w-full px-4 py-2 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none"
                      >
                        <div class="flex items-center">
                          <div class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center overflow-hidden mr-3">
                            <img
                              v-if="user.avatar_url"
                              :src="user.avatar_url"
                              :alt="user.name"
                              class="h-full w-full object-cover"
                            />
                            <span v-else class="text-white text-xs font-medium">
                              {{ getUserInitials(user) }}
                            </span>
                          </div>
                          <div>
                            <div class="text-sm font-medium text-gray-900">
                              {{ user.display_name || user.name }}
                            </div>
                            <div class="text-sm text-gray-500">{{ user.email }}</div>
                          </div>
                        </div>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">角色</label>
                <select
                  v-model="inviteForm.role"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                >
                  <option value="member">成員</option>
                  <option value="admin">管理員</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="sendInvite"
              :disabled="isInviting"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ isInviting ? '發送中...' : '發送邀請' }}
            </button>
            <button
              @click="$emit('close-invite')"
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
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import LoadingSpinner from '../../common/LoadingSpinner.vue'

export default {
  name: 'OrganizationMembers',
  components: {
    LoadingSpinner
  },
  props: {
    organization: {
      type: Object,
      required: true
    },
    showInviteButton: {
      type: Boolean,
      default: true
    },
    showInviteModal: {
      type: Boolean,
      default: false
    }
  },
  emits: ['refresh', 'success', 'show-invite', 'close-invite'],
  setup(props, { emit }) {
    const isLoading = ref(false)
    const searchQuery = ref('')
    const selectedTeam = ref('')
    const selectedRole = ref('')
    const members = ref([])
    const membersPagination = ref({})
    const currentMembersPage = ref(1)
    const teams = ref([])
    const isInviting = ref(false)
    const searchUsers = ref([])
    const isSearching = ref(false)
    const showUserDropdown = ref(false)
    
    const inviteForm = ref({
      email: '',
      role: 'member'
    })
    
    const filteredMembers = computed(() => {
      if (!members.value) return []
      
      let filtered = [...members.value]
      
      // 搜尋篩選
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(member => 
          member.name.toLowerCase().includes(query) ||
          member.email.toLowerCase().includes(query) ||
          (member.display_name && member.display_name.toLowerCase().includes(query))
        )
      }
      
      // 角色篩選
      if (selectedRole.value) {
        filtered = filtered.filter(member => member.pivot.role === selectedRole.value)
      }
      
      // 團隊篩選
      if (selectedTeam.value) {
        filtered = filtered.filter(member => 
          member.teams && member.teams.some(team => team.id == selectedTeam.value)
        )
      }
      
      return filtered
    })
    
    const getUserInitials = (user) => {
      if (!user) return ''
      const name = user.display_name || user.name
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }
    
    const getMemberTeams = (member) => {
      return member.teams || []
    }
    
    const formatDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('zh-TW')
    }
    
    const fetchMembers = async (page = 1) => {
      if (!props.organization?.id) return
      
      isLoading.value = true
      try {
        const response = await axios.get(`/api/organizations/${props.organization.id}?page=${page}`)
        
        // 處理分頁後的成員數據
        if (response.data.users && response.data.users.data) {
          members.value = response.data.users.data
          membersPagination.value = {
            current_page: response.data.users.current_page,
            last_page: response.data.users.last_page,
            per_page: response.data.users.per_page,
            total: response.data.users.total
          }
          currentMembersPage.value = response.data.users.current_page
        } else {
          // 兼容舊版本的非分頁響應
          members.value = response.data.users || []
        }
        
        teams.value = response.data.teams || []
      } catch (error) {
        console.error('Failed to fetch members:', error)
      } finally {
        isLoading.value = false
      }
    }
    
    
    const removeMember = async (member) => {
      if (!confirm(`確定要移除 ${member.display_name || member.name} 嗎？`)) {
        return
      }
      
      try {
        await axios.delete(`/api/admin/organizations/${props.organization.id}/members/${member.id}`)
        emit('success', `已移除成員 ${member.display_name || member.name}`)
        await fetchMembers(currentMembersPage.value)
        emit('refresh')
      } catch (error) {
        console.error('Failed to remove member:', error)
        // TODO: 改用統一的錯誤提示
        alert('移除成員失敗')
      }
    }
    
    let searchTimeout = null
    
    const searchUsersForInvite = async () => {
      const query = inviteForm.value.email.trim()
      if (query.length < 2) {
        searchUsers.value = []
        return
      }
      
      // 防抖動搜尋
      if (searchTimeout) clearTimeout(searchTimeout)
      
      searchTimeout = setTimeout(async () => {
        isSearching.value = true
        try {
          const response = await axios.get(`/api/admin/users/search?q=${encodeURIComponent(query)}&exclude_organization=${props.organization.id}`)
          searchUsers.value = response.data.users || []
        } catch (error) {
          console.error('Failed to search users:', error)
          searchUsers.value = []
        } finally {
          isSearching.value = false
        }
      }, 300)
    }
    
    const selectUser = (user) => {
      inviteForm.value.email = user.email
      inviteForm.value.selectedUser = user
      searchUsers.value = []
      showUserDropdown.value = false
    }
    
    const hideUserDropdown = () => {
      setTimeout(() => {
        showUserDropdown.value = false
      }, 200)
    }
    
    const sendInvite = async () => {
      if (!inviteForm.value.email) {
        // TODO: 改用統一的錯誤提示
        alert('請選擇要邀請的使用者')
        return
      }
      
      isInviting.value = true
      try {
        await axios.post(`/api/admin/organizations/${props.organization.id}/invites`, {
          email: inviteForm.value.email,
          role: inviteForm.value.role
        })
        emit('close-invite')
        inviteForm.value = { email: '', role: 'member' }
        searchUsers.value = []
        emit('success', '成員邀請已發送')
        await fetchMembers(currentMembersPage.value)
        emit('refresh')
      } catch (error) {
        console.error('Failed to send invite:', error)
        const message = error.response?.data?.message || '發送邀請失敗'
        // TODO: 改用統一的錯誤提示
        alert(message)
      } finally {
        isInviting.value = false
      }
    }
    
    const editMember = (member) => {
      // 實作編輯成員功能
      console.log('Edit member:', member)
    }
    
    const changeMembersPage = (page) => {
      fetchMembers(page)
    }
    
    watch(() => props.organization, () => {
      if (props.organization) {
        fetchMembers()
      }
    }, { immediate: true })
    
    return {
      isLoading,
      searchQuery,
      selectedTeam,
      selectedRole,
      members,
      teams,
      filteredMembers,
      isInviting,
      searchUsers,
      isSearching,
      showUserDropdown,
      showInviteModal: computed(() => props.showInviteModal),
      inviteForm,
      getUserInitials,
      getMemberTeams,
      formatDate,
      removeMember,
      sendInvite,
      editMember,
      searchUsersForInvite,
      selectUser,
      hideUserDropdown,
      membersPagination,
      currentMembersPage,
      changeMembersPage
    }
  }
}
</script>