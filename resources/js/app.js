import axios from 'axios';
import Alpine from 'alpinejs';
import { initAllCharts, initMoodTrackerWeeklyChart } from './charts';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Alpine = Alpine;

// Export chart init for dashboard use
window.initAllCharts = initAllCharts;
window.initMoodTrackerWeeklyChart = initMoodTrackerWeeklyChart;

Alpine.start();
