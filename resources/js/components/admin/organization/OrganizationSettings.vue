<template>
  <div>
    <form @submit.prevent="saveSettings" class="space-y-6 p-6">
      <!-- Logo 設定 -->
      <AvatarField
        ref="logoField"
        label="組織 Logo"
        :current-image-url="organization?.logo_url"
        :image-alt="organization?.name"
        size="large"
        shape="rounded"
        remove-button-text="移除 Logo"
        remove-confirm-title="移除組織 Logo"
        remove-confirm-message="確定要移除組織 Logo 嗎？此操作無法復原。"
        :uploading="isSaving"
        :removing="isRemovingLogo"
        @file-selected="handleLogoSelected"
        @file-error="handleLogoError"
        @remove="handleLogoRemove"
        @success="handleLogoSuccess"
        @error="handleLogoError"
      >
        <template #placeholder>
          <svg class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
        </template>
      </AvatarField>

      <!-- 基本資訊 -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
          <label for="name" class="block text-sm font-medium text-gray-700">組織名稱 *</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          />
          <span v-if="errors.name" class="text-sm text-red-600">{{ errors.name[0] }}</span>
        </div>
        
        <div class="md:col-span-2">
          <label for="description" class="block text-sm font-medium text-gray-700">組織描述</label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            placeholder="描述組織的使命和目標..."
          ></textarea>
        </div>
      </div>

      <!-- 組織統計 -->
      <div class="border-t border-gray-200 pt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">組織統計</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-2xl font-semibold text-gray-900">{{ stats.members }}</p>
                <p class="text-sm text-gray-600">總成員數</p>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-2xl font-semibold text-gray-900">{{ stats.teams }}</p>
                <p class="text-sm text-gray-600">團隊數量</p>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8V9m-4 2v6m8-6v6m-4 6H5l.835-2.507A1.5 1.5 0 017.312 15h1.376a1.5 1.5 0 011.477 1.193L10.5 18H12" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-2xl font-semibold text-gray-900">{{ stats.admins }}</p>
                <p class="text-sm text-gray-600">管理員數</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 危險區域 -->
      <div class="border-t border-gray-200 pt-6">
        <h3 class="text-lg font-medium text-red-600 mb-4">危險區域</h3>
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">刪除組織</h3>
              <div class="mt-2 text-sm text-red-700">
                <p>刪除組織後，所有相關的團隊、成員關聯和數據都將永久刪除，此操作無法復原。</p>
              </div>
              <div class="mt-4">
                <button
                  @click="showDeleteConfirm = true"
                  type="button"
                  :disabled="stats.members > 0"
                  class="bg-red-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  刪除組織
                </button>
                <p v-if="stats.members > 0" class="mt-1 text-xs text-red-600">
                  請先移除所有成員後才能刪除組織
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 操作按鈕 -->
      <div class="border-t border-gray-200 pt-6 flex justify-end space-x-3">
        <router-link
          to="/admin/organizations"
          class="bg-white border border-gray-300 rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
          取消
        </router-link>
        <button
          type="submit"
          :disabled="isSaving"
          class="bg-primary-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ isSaving ? '儲存中...' : '儲存變更' }}
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
            <h3 class="text-sm font-medium text-red-800">更新失敗</h3>
            <div class="mt-2 text-sm text-red-700">{{ errorMessage }}</div>
          </div>
        </div>
      </div>
    </div>


    <!-- 刪除確認 Modal -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">刪除組織</h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    您確定要刪除組織「{{ organization?.name }}」嗎？此操作將永久刪除組織及其所有數據，無法復原。
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="deleteOrganization"
              :disabled="isDeleting"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ isDeleting ? '刪除中...' : '確認刪除' }}
            </button>
            <button
              @click="showDeleteConfirm = false"
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
import { ref, reactive, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import ConfirmDialog from '../../common/ConfirmDialog.vue'
import AvatarField from '../../common/AvatarField.vue'

export default {
  name: 'OrganizationSettings',
  components: {
    ConfirmDialog,
    AvatarField
  },
  props: {
    organization: {
      type: Object,
      required: true
    }
  },
  emits: ['refresh', 'success'],
  setup(props, { emit }) {
    const router = useRouter()
    const isSaving = ref(false)
    const isDeleting = ref(false)
    const successMessage = ref('')
    const errorMessage = ref('')
    const errors = ref({})
    const showDeleteConfirm = ref(false)
    const showRemoveLogoConfirm = ref(false)
    const isRemovingLogo = ref(false)
    
    const form = reactive({
      name: '',
      description: '',
      avatar: null,
      remove_avatar: false
    })
    
    const stats = computed(() => {
      if (!props.organization) return { members: 0, teams: 0, admins: 0 }
      
      return {
        members: props.organization.users?.length || 0,
        teams: props.organization.teams?.length || 0,
        admins: props.organization.users?.filter(u => ['admin', 'owner'].includes(u.pivot?.role)).length || 0
      }
    })
    
    const handleLogoSelected = (file) => {
      form.avatar = file
      errorMessage.value = '' // 清除任何現有錯誤訊息
    }
    
    const handleLogoError = (error) => {
      errorMessage.value = error
    }
    
    const removeLogo = () => {
      showRemoveLogoConfirm.value = true
    }
    
    const handleLogoRemove = async () => {
      await confirmRemoveLogo()
    }
    
    const handleLogoSuccess = (message) => {
      emit('success', message)
    }
    
    const confirmRemoveLogo = async () => {
      isRemovingLogo.value = true
      
      try {
        // 立即發送請求到後端移除 Logo
        const formData = new FormData()
        formData.append('name', form.name)
        formData.append('description', form.description || '')
        formData.append('remove_avatar', '1')
        
        const response = await axios.post(`/api/organizations/${props.organization.id}?_method=PUT`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        // 清空本地狀態
        form.avatar = null
        form.remove_avatar = false
        
        // 發送成功訊息和刷新事件
        emit('success', 'Logo 已成功移除')
        emit('refresh')
        
        // 關閉對話框
        showRemoveLogoConfirm.value = false
        
      } catch (error) {
        console.error('移除 Logo 失敗:', error)
        const errorMsg = error.response?.data?.message || '移除 Logo 失敗，請稍後再試'
        errorMessage.value = errorMsg
      } finally {
        isRemovingLogo.value = false
      }
    }
    
    const cancelRemoveLogo = () => {
      showRemoveLogoConfirm.value = false
    }
    
    const saveSettings = async () => {
      if (isSaving.value || !props.organization?.id) return
      
      isSaving.value = true
      errorMessage.value = ''
      successMessage.value = ''
      errors.value = {}
      
      try {
        const formData = new FormData()
        formData.append('name', form.name)
        formData.append('description', form.description || '')
        
        if (form.avatar) {
          formData.append('avatar', form.avatar)
        }
        if (form.remove_avatar) {
          formData.append('remove_avatar', '1')
        }
        
        const response = await axios.post(`/api/organizations/${props.organization.id}?_method=PUT`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        emit('success', '組織設定已成功更新')
        
        form.avatar = null
        form.remove_avatar = false
        
        emit('refresh')
        
      } catch (error) {
        if (error.response?.status === 422) {
          errors.value = error.response.data.errors || {}
        } else {
          errorMessage.value = error.response?.data?.message || '更新失敗，請稍後再試'
        }
      } finally {
        isSaving.value = false
      }
    }
    
    const deleteOrganization = async () => {
      if (isDeleting.value || !props.organization?.id) return
      
      isDeleting.value = true
      try {
        await axios.delete(`/api/organizations/${props.organization.id}`)
        router.push('/admin/organizations')
      } catch (error) {
        console.error('Failed to delete organization:', error)
        // TODO: 改用統一的錯誤提示
        alert('刪除組織失敗')
      } finally {
        isDeleting.value = false
        showDeleteConfirm.value = false
      }
    }
    
    watch(() => props.organization, (newOrg) => {
      if (newOrg) {
        form.name = newOrg.name || ''
        form.description = newOrg.description || ''
      }
    }, { immediate: true })
    
    return {
      form,
      stats,
      isSaving,
      isDeleting,
      successMessage,
      errorMessage,
      errors,
      showDeleteConfirm,
      showRemoveLogoConfirm,
      isRemovingLogo,
      handleLogoSelected,
      handleLogoError,
      handleLogoRemove,
      handleLogoSuccess,
      removeLogo,
      saveSettings,
      deleteOrganization
    }
  }
}
</script>