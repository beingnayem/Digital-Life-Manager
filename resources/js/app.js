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

// Global search autocomplete
(() => {
	const input = document.getElementById('global-search');
	if (!input) return;

	let timeout = null;
	let container = null;

	function ensureContainer() {
		if (container) return container;
		container = document.createElement('div');
		container.className = 'absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md overflow-hidden';
		container.style.display = 'none';
		input.parentElement.style.position = 'relative';
		input.parentElement.appendChild(container);
		return container;
	}

	function render(items) {
		const c = ensureContainer();
		if (!items || items.length === 0) {
			c.style.display = 'none';
			c.innerHTML = '';
			return;
		}

		c.innerHTML = items.map(i => `<a href="/search?q=${encodeURIComponent(i)}" class="block px-3 py-2 text-sm hover:bg-slate-50">${escapeHtml(i)}</a>`).join('');
		c.style.display = 'block';
	}

	function escapeHtml(str) {
		return String(str).replace(/[&<>\"]/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s]));
	}

	input.addEventListener('input', (e) => {
		clearTimeout(timeout);
		const q = e.target.value.trim();
		if (q.length === 0) {
			render([]);
			return;
		}

		timeout = setTimeout(async () => {
			try {
				const res = await fetch(`/search/suggest?q=${encodeURIComponent(q)}`);
				if (!res.ok) return render([]);
				const json = await res.json();
				render(json);
			} catch (err) {
				render([]);
			}
		}, 220);
	});

	document.addEventListener('click', (ev) => {
		const c = container || document.querySelector('.shell-nav-search > div > div');
		if (!c) return;
		if (!input.contains(ev.target) && !c.contains(ev.target)) {
			if (container) container.style.display = 'none';
		}
	});
})();
