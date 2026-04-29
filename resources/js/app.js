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

	let activeIndex = -1;
	function render(items, query) {
		const c = ensureContainer();
		if (!items || items.length === 0) {
			c.style.display = 'none';
			c.innerHTML = '';
			activeIndex = -1;
			return;
		}

		const escaped = items.map((i, idx) => {
			const label = highlight(i, query);
			return `<a href="/search?q=${encodeURIComponent(i)}" role="option" data-index="${idx}" class="suggestion-item block px-3 py-2 text-sm hover:bg-slate-50" tabindex="-1">${label}</a>`;
		}).join('');

		c.innerHTML = `<div role="listbox">${escaped}</div>`;
		c.style.display = 'block';
		activeIndex = -1;
	}

	function escapeHtml(str) {
		return String(str).replace(/[&<>\"]/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s]));
	}

	function highlight(text, q) {
		if (!q) return escapeHtml(text);
		const re = new RegExp(`(${q.replace(/[-\\/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'ig');
		return escapeHtml(text).replace(re, '<mark class="bg-yellow-100 rounded">$1</mark>');
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
				render(json, q);
			} catch (err) {
				render([]);
			}
		}, 220);
	});

	// Keyboard navigation on the input
	input.addEventListener('keydown', (ev) => {
		const c = container;
		if (!c || c.style.display === 'none') return;

		const items = Array.from(c.querySelectorAll('.suggestion-item'));
		if (items.length === 0) return;

		if (ev.key === 'ArrowDown') {
			ev.preventDefault();
			activeIndex = Math.min(activeIndex + 1, items.length - 1);
			updateActive(items);
		} else if (ev.key === 'ArrowUp') {
			ev.preventDefault();
			activeIndex = Math.max(activeIndex - 1, 0);
			updateActive(items);
		} else if (ev.key === 'Enter') {
			if (activeIndex >= 0 && items[activeIndex]) {
				ev.preventDefault();
				const href = items[activeIndex].getAttribute('href');
				window.location.href = href;
			}
		} else if (ev.key === 'Escape') {
			c.style.display = 'none';
			activeIndex = -1;
		}
	});

	function updateActive(items) {
		items.forEach((it, i) => {
			if (i === activeIndex) {
				it.classList.add('bg-slate-100');
				it.setAttribute('aria-selected', 'true');
				it.focus();
			} else {
				it.classList.remove('bg-slate-100');
				it.removeAttribute('aria-selected');
			}
		});
	}

	document.addEventListener('click', (ev) => {
		const c = container || document.querySelector('.shell-nav-search > div > div');
		if (!c) return;
		if (!input.contains(ev.target) && !c.contains(ev.target)) {
			if (container) container.style.display = 'none';
		}
	});
})();
