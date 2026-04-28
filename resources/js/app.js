import axios from 'axios';
import Alpine from 'alpinejs';
import { initAllCharts } from './charts';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Alpine = Alpine;

// Export chart init for dashboard use
window.initAllCharts = initAllCharts;

Alpine.start();
