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

// Regular Icons (外框線) - 僅導入確實存在的圖示
import {
  faUser, faBell, faBuilding, faHome, 
  faFileAlt as faFileText, faEdit, faEye, faEyeSlash, 
  faEnvelope, faCalendar, faImage, faUserCircle
} from '@fortawesome/free-regular-svg-icons';

// 從 Solid 導入 Regular 版本不存在的圖示
import {
  faUsers, faCog, faClipboardList, faSearch, faShieldAlt, 
  faExclamationCircle, faDownload, faUserCheck, faUserPlus, faBars,
  faTrash, faPhone, faMapMarkerAlt
} from '@fortawesome/free-solid-svg-icons';

// 將圖示添加到 Font Awesome 庫
library.add(
  // Solid icons (功能性圖示)
  faSpinner, faChevronDown, faPlus, faUpload, faChartBar,
  faSignOutAlt, faTimesCircle, faCheckCircle,
  faUsers, faCog, faClipboardList, faSearch, faShieldAlt,
  faExclamationCircle, faDownload, faUserCheck, faUserPlus, faBars,
  faTrash, faPhone, faMapMarkerAlt,
  // Regular icons (裝飾性圖示)  
  faUser, faBell, faBuilding, faHome, faFileText, faEdit,
  faEye, faEyeSlash, faEnvelope, faCalendar, faImage, faUserCircle
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
