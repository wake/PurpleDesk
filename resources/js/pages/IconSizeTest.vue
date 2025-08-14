<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h1 class="text-2xl font-bold text-gray-900">Icon 系統三層級尺寸調整工具</h1>
          <p class="mt-2 text-sm text-gray-600">
            針對每種背景尺寸、每種 icon 類型進行個別精細調整
          </p>
        </div>

        <div class="p-6 space-y-8">
          
          <!-- 載入狀態 -->
          <div v-if="loading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>

          <!-- 三層級配置系統 -->
          <div v-else>
            <section class="space-y-6">
              <h2 class="text-xl font-semibold text-gray-900">三層級配置系統</h2>
              
              <!-- 三層級配置系統 -->
              <div class="bg-gray-50 p-6 rounded-lg space-y-6">
                <h3 class="text-lg font-medium text-gray-900">三層級尺寸配置系統</h3>
                
                <!-- 層級選擇 -->
                <div class="flex space-x-4 mb-4">
                  <button 
                    v-for="level in [{ key: 'global', name: '全域預設' }, { key: 'size', name: '單一尺寸' }, { key: 'sizeAndType', name: '尺寸+類型' }, { key: 'all', name: '完整總覽' }]"
                    :key="level.key"
                    @click="currentEditLevel = level.key"
                    :class="[
                      'px-4 py-2 text-sm font-medium rounded-md',
                      currentEditLevel === level.key 
                        ? 'bg-indigo-600 text-white' 
                        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                    ]"
                  >
                    {{ level.name }}
                  </button>
                </div>
                
                <!-- 層級 1: 全域配置 -->
                <div v-if="currentEditLevel === 'global'" class="space-y-4">
                  <h4 class="font-medium text-gray-800">全域預設配置 (基準: 80x80px)</h4>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">文字大小 (px)</label>
                      <input 
                        v-model.number="configSystem.global.text.fontSize" 
                        type="number" 
                        min="10" 
                        max="60" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      >
                    </div>
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">Emoji 大小 (rem)</label>
                      <input 
                        v-model.number="configSystem.global.emoji.fontSize" 
                        type="number" 
                        min="1" 
                        max="5" 
                        step="0.25" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      >
                    </div>
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">Icon 大小 (rem)</label>
                      <input 
                        v-model.number="configSystem.global.icon.size" 
                        type="number" 
                        min="1" 
                        max="5" 
                        step="0.25" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      >
                    </div>
                  </div>
                </div>
                
                <!-- 層級 2: 單一尺寸配置 -->
                <div v-if="currentEditLevel === 'size'" class="space-y-4">
                  <div class="flex items-center space-x-4">
                    <h4 class="font-medium text-gray-800">單一尺寸配置</h4>
                    <select v-model="currentEditSize" class="rounded-md border-gray-300 text-sm">
                      <option v-for="size in allSizes" :key="size" :value="size">{{ size }}</option>
                    </select>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">文字大小 (px)</label>
                      <input 
                        v-model.number="getSizeConfig(currentEditSize).text.fontSize" 
                        type="number" 
                        min="10" 
                        max="60" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        @input="updateSizeConfig(currentEditSize, 'text', 'fontSize', $event.target.value)"
                      >
                    </div>
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">Emoji 大小 (rem)</label>
                      <input 
                        v-model.number="getSizeConfig(currentEditSize).emoji.fontSize" 
                        type="number" 
                        min="1" 
                        max="5" 
                        step="0.25" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        @input="updateSizeConfig(currentEditSize, 'emoji', 'fontSize', $event.target.value)"
                      >
                    </div>
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">Icon 大小 (rem)</label>
                      <input 
                        v-model.number="getSizeConfig(currentEditSize).icon.size" 
                        type="number" 
                        min="1" 
                        max="5" 
                        step="0.25" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        @input="updateSizeConfig(currentEditSize, 'icon', 'size', $event.target.value)"
                      >
                    </div>
                  </div>
                </div>
                
                <!-- 層級 3: 尺寸+類型配置 -->
                <div v-if="currentEditLevel === 'sizeAndType'" class="space-y-4">
                  <div class="flex items-center space-x-4">
                    <h4 class="font-medium text-gray-800">尺寸+類型配置</h4>
                    <select v-model="currentEditSize" class="rounded-md border-gray-300 text-sm">
                      <option v-for="size in allSizes" :key="size" :value="size">{{ size }}</option>
                    </select>
                    <select v-model="currentEditType" class="rounded-md border-gray-300 text-sm">
                      <option value="text">文字</option>
                      <option value="emoji">Emoji</option>
                      <option value="hero_icon">Hero Icon</option>
                      <option value="bootstrap_icon">Bootstrap Icon</option>
                      <option value="image">圖片</option>
                    </select>
                  </div>
                  <div class="space-y-4">
                    <div v-if="currentEditType === 'text'" class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">文字大小 (px)</label>
                      <input 
                        v-model.number="getSizeTypeConfig(currentEditSize, currentEditType).fontSize" 
                        type="number" 
                        min="10" 
                        max="60" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        @input="updateSizeTypeConfig(currentEditSize, currentEditType, 'fontSize', $event.target.value)"
                      >
                    </div>
                    <div v-if="currentEditType === 'emoji'" class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">Emoji 大小 (rem)</label>
                      <input 
                        v-model.number="getSizeTypeConfig(currentEditSize, currentEditType).fontSize" 
                        type="number" 
                        min="1" 
                        max="5" 
                        step="0.25" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        @input="updateSizeTypeConfig(currentEditSize, currentEditType, 'fontSize', $event.target.value)"
                      >
                    </div>
                    <div v-if="['hero_icon', 'bootstrap_icon'].includes(currentEditType)" class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">Icon 大小 (rem)</label>
                      <input 
                        v-model.number="getSizeTypeConfig(currentEditSize, currentEditType).size" 
                        type="number" 
                        min="1" 
                        max="5" 
                        step="0.25" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        @input="updateSizeTypeConfig(currentEditSize, currentEditType, 'size', $event.target.value)"
                      >
                    </div>
                  </div>
                </div>
                
                <!-- 層級 4: 完整總覽配置 -->
                <div v-if="currentEditLevel === 'all'" class="space-y-6">
                  <h4 class="font-medium text-gray-800">完整配置總覽 - 所有層級一次性調整</h4>
                  
                  <!-- 層級 1: 全域設定 -->
                  <div class="border border-gray-200 rounded-lg p-4">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">層級 1: 全域預設配置</h5>
                    <div class="grid grid-cols-3 gap-4">
                      <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">文字 (px)</label>
                        <input 
                          v-model.number="configSystem.global.text.fontSize" 
                          type="number" 
                          min="10" 
                          max="60" 
                          class="w-full text-sm rounded border-gray-300"
                        >
                      </div>
                      <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Emoji (rem)</label>
                        <input 
                          v-model.number="configSystem.global.emoji.fontSize" 
                          type="number" 
                          min="1" 
                          max="5" 
                          step="0.25" 
                          class="w-full text-sm rounded border-gray-300"
                        >
                      </div>
                      <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Icon (rem)</label>
                        <input 
                          v-model.number="configSystem.global.icon.size" 
                          type="number" 
                          min="1" 
                          max="5" 
                          step="0.25" 
                          class="w-full text-sm rounded border-gray-300"
                        >
                      </div>
                    </div>
                  </div>
                  
                  <!-- 層級 2: 單一尺寸設定 -->
                  <div class="border border-gray-200 rounded-lg p-4">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">層級 2: 單一尺寸配置 (覆蓋全域)</h5>
                    <div class="space-y-3">
                      <div v-for="size in allSizes" :key="size" class="grid grid-cols-4 gap-3 items-center">
                        <div class="text-sm font-mono font-medium text-gray-800">{{ size }}</div>
                        <input 
                          v-model.number="getSizeConfig(size).text.fontSize" 
                          type="number" 
                          min="10" 
                          max="60" 
                          placeholder="文字(px)"
                          @input="updateSizeConfig(size, 'text', 'fontSize', $event.target.value)"
                          class="text-xs rounded border-gray-300"
                        >
                        <input 
                          v-model.number="getSizeConfig(size).emoji.fontSize" 
                          type="number" 
                          min="1" 
                          max="5" 
                          step="0.25" 
                          placeholder="Emoji(rem)"
                          @input="updateSizeConfig(size, 'emoji', 'fontSize', $event.target.value)"
                          class="text-xs rounded border-gray-300"
                        >
                        <input 
                          v-model.number="getSizeConfig(size).icon.size" 
                          type="number" 
                          min="1" 
                          max="5" 
                          step="0.25" 
                          placeholder="Icon(rem)"
                          @input="updateSizeConfig(size, 'icon', 'size', $event.target.value)"
                          class="text-xs rounded border-gray-300"
                        >
                      </div>
                    </div>
                  </div>
                  
                  <!-- 層級 3: 尺寸+類型設定 -->
                  <div class="border border-gray-200 rounded-lg p-4">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">層級 3: 尺寸+類型配置 (最高優先級)</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      
                      <!-- 文字類型 -->
                      <div>
                        <h6 class="text-xs font-medium text-gray-600 mb-2">文字類型 (px)</h6>
                        <div class="space-y-2">
                          <div v-for="size in allSizes" :key="size" class="grid grid-cols-2 gap-2 items-center">
                            <label class="text-xs font-mono text-gray-700">{{ size }}</label>
                            <input 
                              v-model.number="getSizeTypeConfig(size, 'text').fontSize" 
                              type="number" 
                              min="10" 
                              max="60" 
                              @input="updateSizeTypeConfig(size, 'text', 'fontSize', $event.target.value)"
                              class="text-xs rounded border-gray-300"
                            >
                          </div>
                        </div>
                      </div>
                      
                      <!-- Emoji 類型 -->
                      <div>
                        <h6 class="text-xs font-medium text-gray-600 mb-2">Emoji 類型 (rem)</h6>
                        <div class="space-y-2">
                          <div v-for="size in allSizes" :key="size" class="grid grid-cols-2 gap-2 items-center">
                            <label class="text-xs font-mono text-gray-700">{{ size }}</label>
                            <input 
                              v-model.number="getSizeTypeConfig(size, 'emoji').fontSize" 
                              type="number" 
                              min="1" 
                              max="5" 
                              step="0.25" 
                              @input="updateSizeTypeConfig(size, 'emoji', 'fontSize', $event.target.value)"
                              class="text-xs rounded border-gray-300"
                            >
                          </div>
                        </div>
                      </div>
                      
                      <!-- Hero Icon 類型 -->
                      <div>
                        <h6 class="text-xs font-medium text-gray-600 mb-2">Hero Icon 類型 (rem)</h6>
                        <div class="space-y-2">
                          <div v-for="size in allSizes" :key="size" class="grid grid-cols-2 gap-2 items-center">
                            <label class="text-xs font-mono text-gray-700">{{ size }}</label>
                            <input 
                              v-model.number="getSizeTypeConfig(size, 'hero_icon').size" 
                              type="number" 
                              min="1" 
                              max="5" 
                              step="0.25" 
                              @input="updateSizeTypeConfig(size, 'hero_icon', 'size', $event.target.value)"
                              class="text-xs rounded border-gray-300"
                            >
                          </div>
                        </div>
                      </div>
                      
                      <!-- Bootstrap Icon 類型 -->
                      <div>
                        <h6 class="text-xs font-medium text-gray-600 mb-2">Bootstrap Icon 類型 (rem)</h6>
                        <div class="space-y-2">
                          <div v-for="size in allSizes" :key="size" class="grid grid-cols-2 gap-2 items-center">
                            <label class="text-xs font-mono text-gray-700">{{ size }}</label>
                            <input 
                              v-model.number="getSizeTypeConfig(size, 'bootstrap_icon').size" 
                              type="number" 
                              min="1" 
                              max="5" 
                              step="0.25" 
                              @input="updateSizeTypeConfig(size, 'bootstrap_icon', 'size', $event.target.value)"
                              class="text-xs rounded border-gray-300"
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- 動作按鈕 -->
                <div class="flex space-x-3 pt-4 border-t border-gray-200">
                  <button 
                    @click="resetToDefaults" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                  >
                    重設為預設值
                  </button>
                  <button 
                    @click="exportSizeConfig" 
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700"
                  >
                    匯出完整配置
                  </button>
                  <button 
                    @click="clearCustomConfigs" 
                    class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-md hover:bg-red-50"
                  >
                    清除自訂配置
                  </button>
                </div>
              </div>
            </section>
            
            <!-- 完整尺寸測試矩陣 -->
            <section class="space-y-6">
              <h2 class="text-xl font-semibold text-gray-900">完整尺寸測試矩陣</h2>
              
              <!-- 所有尺寸列表 -->
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-3">測試尺寸：</p>
                <div class="flex flex-wrap gap-2">
                  <span v-for="size in allSizes" :key="size" class="px-2 py-1 bg-white rounded text-xs font-mono">{{ size }}</span>
                </div>
              </div>
              
              <!-- 各類型完整測試 -->
              <div class="space-y-8">
                
                <!-- 文字類型測試 -->
                <div class="border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">文字類型 (最多2字)</h3>
                  <div class="space-y-4">
                    <div v-for="textExample in textExamples" :key="textExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ textExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <IconDisplay 
                            :icon-data="textExample.data" 
                            :size="size" 
                            :custom-config="getCurrentSizeConfig(size, 'text')"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Emoji 類型測試 -->
                <div class="border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Emoji 類型</h3>
                  <div class="space-y-4">
                    <div v-for="emojiExample in emojiExamples" :key="emojiExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ emojiExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <IconDisplay 
                            :icon-data="emojiExample.data" 
                            :size="size" 
                            :custom-config="getCurrentSizeConfig(size, 'emoji')"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Hero Icons 測試 -->
                <div class="border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Hero Icons</h3>
                  <div class="space-y-4">
                    <div v-for="heroExample in heroExamples" :key="heroExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ heroExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <IconDisplay 
                            :icon-data="heroExample.data" 
                            :size="size" 
                            :custom-config="getCurrentSizeConfig(size, 'hero_icon')"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Bootstrap Icons 測試 -->
                <div class="border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Bootstrap Icons</h3>
                  <div class="space-y-4">
                    <div v-for="bsExample in bsExamples" :key="bsExample.key" class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">{{ bsExample.name }}</h4>
                      <div class="flex flex-wrap gap-3 items-end">
                        <div v-for="size in allSizes" :key="size" class="text-center">
                          <IconDisplay 
                            :icon-data="bsExample.data" 
                            :size="size" 
                            :custom-config="getCurrentSizeConfig(size, 'bootstrap_icon')"
                          />
                          <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- 圖片類型測試 -->
                <div class="border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">圖片類型</h3>
                  <div class="space-y-3">
                    <h4 class="text-sm font-medium text-gray-700">示例圖片 (placeholder)</h4>
                    <div class="flex flex-wrap gap-3 items-end">
                      <div v-for="size in allSizes" :key="size" class="text-center">
                        <IconDisplay 
                          :icon-data="imageExample.data" 
                          :size="size" 
                          :custom-config="getCurrentSizeConfig(size, 'image')"
                        />
                        <p class="text-xs text-gray-500 mt-1">{{ size }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            
            <!-- 現有資料測試 -->
            <section class="space-y-4">
              <h2 class="text-xl font-semibold text-gray-900">現有測試資料</h2>
              
              <!-- 用戶資料 -->
              <div class="space-y-3">
                <h3 class="text-lg font-medium text-gray-700">用戶頭像</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div 
                    v-for="user in users" 
                    :key="user.id"
                    class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg"
                  >
                    <div class="flex space-x-2">
                      <IconDisplay 
                        :icon-data="user.avatar_data" 
                        size="xs" 
                        :title="user.full_name"
                        :custom-config="getCurrentSizeConfig('xs', user.avatar_data?.type)"
                      />
                      <IconDisplay 
                        :icon-data="user.avatar_data" 
                        size="sm" 
                        :title="user.full_name"
                        :custom-config="getCurrentSizeConfig('sm', user.avatar_data?.type)"
                      />
                      <IconDisplay 
                        :icon-data="user.avatar_data" 
                        size="md" 
                        :title="user.full_name"
                        :custom-config="getCurrentSizeConfig('md', user.avatar_data?.type)"
                      />
                      <IconDisplay 
                        :icon-data="user.avatar_data" 
                        size="lg" 
                        :title="user.full_name"
                        :custom-config="getCurrentSizeConfig('lg', user.avatar_data?.type)"
                      />
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        {{ user.full_name }}
                      </p>
                      <p class="text-xs text-gray-500 truncate">
                        {{ getAvatarTypeDescription(user.avatar_data) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 組織資料 -->
              <div class="space-y-3">
                <h3 class="text-lg font-medium text-gray-700">組織頭像</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div 
                    v-for="org in organizations" 
                    :key="org.id"
                    class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg"
                  >
                    <div class="flex space-x-2">
                      <IconDisplay 
                        :icon-data="org.avatar_data" 
                        size="sm" 
                        :title="org.name"
                        :custom-config="getCurrentSizeConfig('sm', org.avatar_data?.type)"
                      />
                      <IconDisplay 
                        :icon-data="org.avatar_data" 
                        size="md" 
                        :title="org.name"
                        :custom-config="getCurrentSizeConfig('md', org.avatar_data?.type)"
                      />
                      <IconDisplay 
                        :icon-data="org.avatar_data" 
                        size="lg" 
                        :title="org.name"
                        :custom-config="getCurrentSizeConfig('lg', org.avatar_data?.type)"
                      />
                      <IconDisplay 
                        :icon-data="org.avatar_data" 
                        size="xl" 
                        :title="org.name"
                        :custom-config="getCurrentSizeConfig('xl', org.avatar_data?.type)"
                      />
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        {{ org.name }}
                      </p>
                      <p class="text-xs text-gray-500 truncate">
                        {{ getAvatarTypeDescription(org.avatar_data) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 團隊資料 -->
              <div class="space-y-3">
                <h3 class="text-lg font-medium text-gray-700">團隊頭像</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div 
                    v-for="team in teams" 
                    :key="team.id"
                    class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg"
                  >
                    <div class="flex space-x-2">
                      <IconDisplay 
                        :icon-data="team.avatar_data" 
                        size="sm" 
                        :title="team.name"
                        :custom-config="getCurrentSizeConfig('sm', team.avatar_data?.type)"
                      />
                      <IconDisplay 
                        :icon-data="team.avatar_data" 
                        size="md" 
                        :title="team.name"
                        :custom-config="getCurrentSizeConfig('md', team.avatar_data?.type)"
                      />
                      <IconDisplay 
                        :icon-data="team.avatar_data" 
                        size="lg" 
                        :title="team.name"
                        :custom-config="getCurrentSizeConfig('lg', team.avatar_data?.type)"
                      />
                      <IconDisplay 
                        :icon-data="team.avatar_data" 
                        size="xl" 
                        :title="team.name"
                        :custom-config="getCurrentSizeConfig('xl', team.avatar_data?.type)"
                      />
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        {{ team.name }}
                      </p>
                      <p class="text-xs text-gray-500 truncate">
                        {{ getAvatarTypeDescription(team.avatar_data) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </section>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, reactive } from 'vue'
import { useRouter } from 'vue-router'
import IconDisplay from '@/components/common/IconDisplay.vue'
import axios from 'axios'

export default {
  name: 'IconSizeTest',
  components: {
    IconDisplay
  },
  setup() {
    const loading = ref(true)
    const users = ref([])
    const organizations = ref([])
    const teams = ref([])
    const router = useRouter()
    
    // 所有可用尺寸
    const allSizes = ['4', '5', '6', '8', '10', '12', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl']
    const allTypes = ['text', 'emoji', 'hero_icon', 'bootstrap_icon', 'image']
    
    // 三層級配置系統
    const configSystem = reactive({
      // 層級 1: 全域預設配置 (跨所有類型和尺寸)
      global: {
        text: { fontSize: 28 }, // px
        emoji: { fontSize: 2.75 }, // rem
        icon: { size: 2.75 } // rem
      },
      
      // 層級 2: 單一尺寸配置 (跨所有類型)
      bySize: {},
      
      // 層級 3: 單一尺寸+類型配置 (最細粒度)
      bySizeAndType: {}
    })
    
    // 當前編輯的配置層級
    const currentEditLevel = ref('global') // 'global' | 'size' | 'sizeAndType'
    const currentEditSize = ref('md')
    const currentEditType = ref('text')
    
    // 測試範例資料
    const textExamples = [
      {
        key: 'chinese_1',
        name: '中文 1 字',
        data: { type: 'text', text: '李', backgroundColor: '#f0fdf4', textColor: '#374151' }
      },
      {
        key: 'chinese_2', 
        name: '中文 2 字',
        data: { type: 'text', text: '李華', backgroundColor: '#fef2f2', textColor: '#374151' }
      },
      {
        key: 'english_1',
        name: '英文 1 字母',
        data: { type: 'text', text: 'A', backgroundColor: '#eff6ff', textColor: '#374151' }
      },
      {
        key: 'english_2',
        name: '英文 2 字母',
        data: { type: 'text', text: 'AB', backgroundColor: '#faf5ff', textColor: '#374151' }
      },
      {
        key: 'mixed',
        name: '混合字符',
        data: { type: 'text', text: 'A李', backgroundColor: '#fff7ed', textColor: '#374151' }
      }
    ]
    
    const emojiExamples = [
      {
        key: 'simple',
        name: '簡單 Emoji',
        data: { type: 'emoji', emoji: '😀', backgroundColor: '#fef2f2' }
      },
      {
        key: 'complex',
        name: '複雜 Emoji',
        data: { type: 'emoji', emoji: '👨‍💻', backgroundColor: '#f0f9ff' }
      },
      {
        key: 'skin_tone',
        name: '膚色修飾',
        data: { type: 'emoji', emoji: '👋🏻', backgroundColor: '#fff7ed' }
      }
    ]
    
    const heroExamples = [
      {
        key: 'outline',
        name: 'Outline 樣式',
        data: { type: 'hero_icon', icon: 'user', backgroundColor: '#f0f9ff', iconColor: '#0ea5e9', style: 'outline' }
      },
      {
        key: 'solid',
        name: 'Solid 樣式',
        data: { type: 'hero_icon', icon: 'heart', backgroundColor: '#fdf2f8', iconColor: '#ec4899', style: 'solid' }
      }
    ]
    
    const bsExamples = [
      {
        key: 'outline',
        name: 'Outline 樣式',
        data: { type: 'bootstrap_icon', icon: 'star', backgroundColor: '#fffbeb', iconColor: '#f59e0b', style: 'outline' }
      },
      {
        key: 'fill',
        name: 'Fill 樣式',
        data: { type: 'bootstrap_icon', icon: 'lightning', backgroundColor: '#f7fee7', iconColor: '#84cc16', style: 'fill' }
      }
    ]
    
    const imageExample = {
      name: '圖片類型',
      data: { type: 'image', path: 'https://via.placeholder.com/80x80/6366f1/ffffff?text=IMG' }
    }
    
    const loadData = async () => {
      try {
        loading.value = true
        
        // 載入測試用戶
        const usersResponse = await axios.get('/api/admin/users', {
          params: { search: 'test', per_page: 20 }
        })
        users.value = usersResponse.data.data
        
        // 載入測試組織
        const orgsResponse = await axios.get('/api/admin/organizations', {
          params: { search: '測試組織', per_page: 20 }
        })
        organizations.value = orgsResponse.data.data
        
        // 載入測試團隊 (透過組織)
        if (organizations.value.length > 0) {
          for (const org of organizations.value) {
            try {
              const teamsResponse = await axios.get(`/api/organizations/${org.id}/teams`)
              // 檢查回應結構
              if (teamsResponse.data && Array.isArray(teamsResponse.data.data)) {
                teams.value.push(...teamsResponse.data.data)
              } else if (teamsResponse.data && Array.isArray(teamsResponse.data)) {
                teams.value.push(...teamsResponse.data)
              }
            } catch (teamError) {
              console.warn(`無法載入組織 ${org.id} 的團隊:`, teamError)
            }
          }
        }
        
      } catch (error) {
        console.error('載入測試資料失敗:', error)
        if (error.response?.status === 401) {
          router.push('/login')
        }
      } finally {
        loading.value = false
      }
    }
    
    // 取得某個尺寸的配置 (層級 2)
    const getSizeConfig = (size) => {
      if (!configSystem.bySize[size]) {
        configSystem.bySize[size] = {
          text: { fontSize: configSystem.global.text.fontSize },
          emoji: { fontSize: configSystem.global.emoji.fontSize },
          icon: { size: configSystem.global.icon.size }
        }
      }
      return configSystem.bySize[size]
    }
    
    // 更新某個尺寸的配置
    const updateSizeConfig = (size, type, property, value) => {
      if (!configSystem.bySize[size]) {
        getSizeConfig(size) // 初始化
      }
      configSystem.bySize[size][type][property] = Number(value)
    }
    
    // 取得某個尺寸+類型的配置 (層級 3)
    const getSizeTypeConfig = (size, type) => {
      const key = `${size}-${type}`
      if (!configSystem.bySizeAndType[key]) {
        // 優先級: 層級 2 > 層級 1
        const sizeConfig = configSystem.bySize[size] || configSystem.global
        const baseConfig = sizeConfig[type === 'hero_icon' || type === 'bootstrap_icon' ? 'icon' : type]
        
        if (type === 'text') {
          configSystem.bySizeAndType[key] = { fontSize: baseConfig.fontSize }
        } else if (type === 'emoji') {
          configSystem.bySizeAndType[key] = { fontSize: baseConfig.fontSize }
        } else if (type === 'hero_icon' || type === 'bootstrap_icon') {
          configSystem.bySizeAndType[key] = { size: baseConfig.size }
        } else {
          configSystem.bySizeAndType[key] = {}
        }
      }
      return configSystem.bySizeAndType[key]
    }
    
    // 更新某個尺寸+類型的配置
    const updateSizeTypeConfig = (size, type, property, value) => {
      const key = `${size}-${type}`
      if (!configSystem.bySizeAndType[key]) {
        getSizeTypeConfig(size, type) // 初始化
      }
      configSystem.bySizeAndType[key][property] = Number(value)
    }
    
    // 取得當前的結合配置 (用於 IconDisplay)
    const getCurrentSizeConfig = (size = 'md', type = null) => {
      const result = {
        text: { fontSize: `${configSystem.global.text.fontSize}px` },
        emoji: { fontSize: `${configSystem.global.emoji.fontSize}rem` },
        icon: { size: `${configSystem.global.icon.size}rem` }
      }
      
      // 層級 2: 單一尺寸覆蓋
      if (configSystem.bySize[size]) {
        const sizeConfig = configSystem.bySize[size]
        if (sizeConfig.text) result.text.fontSize = `${sizeConfig.text.fontSize}px`
        if (sizeConfig.emoji) result.emoji.fontSize = `${sizeConfig.emoji.fontSize}rem`
        if (sizeConfig.icon) result.icon.size = `${sizeConfig.icon.size}rem`
      }
      
      // 層級 3: 尺寸+類型覆蓋
      if (type) {
        const key = `${size}-${type}`
        if (configSystem.bySizeAndType[key]) {
          const typeConfig = configSystem.bySizeAndType[key]
          if (type === 'text' && typeConfig.fontSize) {
            result.text.fontSize = `${typeConfig.fontSize}px`
          } else if (type === 'emoji' && typeConfig.fontSize) {
            result.emoji.fontSize = `${typeConfig.fontSize}rem`
          } else if ((type === 'hero_icon' || type === 'bootstrap_icon') && typeConfig.size) {
            result.icon.size = `${typeConfig.size}rem`
          }
        }
      }
      
      return result
    }
    
    const getAvatarTypeDescription = (avatarData) => {
      if (!avatarData) return '無頭像數據'
      
      switch (avatarData.type) {
        case 'text':
          return `文字: "${avatarData.text}"`
        case 'emoji':
          return `Emoji: ${avatarData.emoji}`
        case 'hero_icon':
          return `Hero Icon: ${avatarData.icon} (${avatarData.style})`
        case 'bootstrap_icon':
          return `Bootstrap Icon: ${avatarData.icon} (${avatarData.style})`
        case 'image':
          return `圖片: ${avatarData.path || avatarData.url}`
        default:
          return '未知類型'
      }
    }
    
    const resetToDefaults = () => {
      configSystem.global.text.fontSize = 28
      configSystem.global.emoji.fontSize = 2.75
      configSystem.global.icon.size = 2.75
    }
    
    const clearCustomConfigs = () => {
      configSystem.bySize = {}
      configSystem.bySizeAndType = {}
    }
    
    const exportSizeConfig = () => {
      const config = {
        timestamp: new Date().toISOString(),
        baseContainer: '80x80px',
        
        // 層級 1: 全域配置
        global: {
          text: { fontSize: configSystem.global.text.fontSize },
          emoji: { fontSize: configSystem.global.emoji.fontSize },
          icon: { size: configSystem.global.icon.size }
        },
        
        // 層級 2: 單一尺寸配置
        bySize: { ...configSystem.bySize },
        
        // 層級 3: 尺寸+類型配置
        bySizeAndType: { ...configSystem.bySizeAndType },
        
        // 總結計算結果 (所有尺寸和類型的最終值)
        computed: {}
      }
      
      // 計算所有尺寸和類型的最終配置
      allSizes.forEach(size => {
        config.computed[size] = {}
        allTypes.forEach(type => {
          config.computed[size][type] = getCurrentSizeConfig(size, type)
        })
      })
      
      console.log('=== 三層級 Icon 尺寸配置系統 ===')
      console.log(JSON.stringify(config, null, 2))
      alert('完整配置已匯出到 Console，請檢查瀏覽器開發者工具')
    }
    
    onMounted(() => {
      loadData()
    })
    
    return {
      loading,
      users,
      organizations,
      teams,
      allSizes,
      allTypes,
      configSystem,
      currentEditLevel,
      currentEditSize,
      currentEditType,
      textExamples,
      emojiExamples,
      heroExamples,
      bsExamples,
      imageExample,
      getAvatarTypeDescription,
      getSizeConfig,
      updateSizeConfig,
      getSizeTypeConfig,
      updateSizeTypeConfig,
      getCurrentSizeConfig,
      resetToDefaults,
      clearCustomConfigs,
      exportSizeConfig
    }
  }
}
</script>