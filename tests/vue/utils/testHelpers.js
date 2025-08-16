import { mount, shallowMount } from '@vue/test-utils';
import { createPinia } from 'pinia';
import { createRouter, createWebHistory } from 'vue-router';

/**
 * 建立測試用的 Pinia store
 */
export function createTestPinia() {
  return createPinia();
}

/**
 * 建立測試用的 Vue Router
 */
export function createTestRouter(routes = []) {
  const defaultRoutes = [
    { path: '/', name: 'home', component: { template: '<div>Home</div>' } },
    { path: '/login', name: 'login', component: { template: '<div>Login</div>' } }
  ];
  
  return createRouter({
    history: createWebHistory(),
    routes: [...defaultRoutes, ...routes]
  });
}

/**
 * 建立完整的組件掛載選項
 */
export function createMountOptions(options = {}) {
  const pinia = createTestPinia();
  const router = createTestRouter(options.routes);
  
  return {
    global: {
      plugins: [pinia, router],
      stubs: options.stubs || {},
      mocks: options.mocks || {}
    },
    ...options
  };
}

/**
 * 等待 Vue 的下一次 tick 和所有 Promise
 */
export async function flushPromises() {
  return new Promise(resolve => setTimeout(resolve, 0));
}

/**
 * 模擬 API 回應
 */
export function mockApiResponse(data, status = 200) {
  return Promise.resolve({
    status,
    data,
    headers: {},
    config: {},
    statusText: status === 200 ? 'OK' : 'Error'
  });
}

/**
 * 模擬 API 錯誤
 */
export function mockApiError(message = 'API Error', status = 500) {
  const error = new Error(message);
  error.response = {
    status,
    data: { message },
    headers: {},
    config: {},
    statusText: 'Error'
  };
  return Promise.reject(error);
}