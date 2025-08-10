import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './components/App.vue';
import { useAuthStore } from './stores/auth';

// Font Awesome 配置
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Solid Icons (實心)
import { 
  faSpinner, faChevronDown, faPlus, faUpload, faChartBar,
  faSignOutAlt, faTimesCircle, faCheckCircle
} from '@fortawesome/free-solid-svg-icons';

// Regular Icons (外框線)
import {
  faUser, faUsers, faBell, faCog, faBuilding, faHome, 
  faFileText, faClipboardList, faSearch, faEdit, faTrash, 
  faEye, faEyeSlash, faEnvelope, faPhone, faMapMarkerAlt, 
  faCalendar, faShieldAlt, faExclamationCircle, faDownload, 
  faImage, faUserCheck, faUserPlus, faUserCircle, faBars
} from '@fortawesome/free-regular-svg-icons';

// 將圖示添加到 Font Awesome 庫
library.add(
  // Solid icons
  faSpinner, faChevronDown, faPlus, faUpload, faChartBar,
  faSignOutAlt, faTimesCircle, faCheckCircle,
  // Regular icons  
  faUser, faUsers, faBell, faCog, faBuilding, faHome,
  faFileText, faClipboardList, faSearch, faEdit, faTrash,
  faEye, faEyeSlash, faEnvelope, faPhone, faMapMarkerAlt,
  faCalendar, faShieldAlt, faExclamationCircle, faDownload,
  faImage, faUserCheck, faUserPlus, faUserCircle, faBars
);

const app = createApp(App);
const pinia = createPinia();

// 註冊 Font Awesome 組件
app.component('font-awesome-icon', FontAwesomeIcon);

app.use(pinia);
app.use(router);

// 初始化認證狀態
const authStore = useAuthStore();

// 等待認證初始化完成後再掛載應用
authStore.initializeAuth().then(() => {
  app.mount('#app');
});
