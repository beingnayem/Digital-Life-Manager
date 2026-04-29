import axios from 'axios';
import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';
import { initAllCharts, initMoodTrackerWeeklyChart } from './charts';
import './record-actions';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Chart.register(...registerables);
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.store('notifications', {
	toasts: [],
	push(toast) {
		const id = crypto.randomUUID();
		this.toasts.push({ id, ...toast });

		window.setTimeout(() => {
			this.remove(id);
		}, toast.duration ?? 4000);
	},
	remove(id) {
		this.toasts = this.toasts.filter((toast) => toast.id !== id);
	},
});

window.addEventListener('app:toast', (event) => {
	const detail = event.detail ?? {};

	Alpine.store('notifications').push({
		type: detail.type ?? 'success',
		title: detail.title ?? (detail.type === 'error' ? 'Something went wrong' : 'Saved'),
		message: detail.message ?? '',
		duration: detail.duration,
	});
});

// Export chart init for dashboard use
window.initAllCharts = initAllCharts;
window.initMoodTrackerWeeklyChart = initMoodTrackerWeeklyChart;

Alpine.start();
