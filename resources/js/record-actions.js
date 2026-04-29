const API_HEADERS = {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
};

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function renderRow(resource, rowHtml) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(`<table><tbody>${rowHtml}</tbody></table>`, 'text/html');
    return doc.querySelector('tbody')?.firstElementChild ?? null;
}

function getRowSelector(resource, id) {
    return resource === 'task'
        ? `[data-task-id="${id}"]`
        : `[data-expense-id="${id}"]`;
}

function isAjaxResource(resource) {
    return resource === 'task' || resource === 'expense';
}

function getTbody(resource) {
    return resource === 'task'
        ? document.querySelector('#tasks-table-body')
        : document.querySelector('#expenses-table-body');
}

function setLoadingState(form, isLoading) {
    form.classList.toggle('ajax-loading', isLoading);

    const submitButton = form.querySelector('button[type="submit"]');
    if (!submitButton) return;

    if (isLoading) {
        if (!submitButton.dataset.originalHtml) {
            submitButton.dataset.originalHtml = submitButton.innerHTML;
        }

        const label = submitButton.dataset.loadingLabel ?? 'Loading...';
        submitButton.innerHTML = `<span class="inline-flex items-center gap-2"><span class="ajax-spinner"></span>${label}</span>`;
        submitButton.disabled = true;
        return;
    }

    if (submitButton.dataset.originalHtml) {
        submitButton.innerHTML = submitButton.dataset.originalHtml;
    }
    submitButton.disabled = false;
}

function closeModal(resource) {
    const storeName = resource === 'task' ? 'taskModal' : 'expenseModal';
    if (window.Alpine?.store) {
        const store = window.Alpine.store(storeName);
        if (store) {
            store.open = false;
        }
    }
}

function resetModalForm(form, resource) {
    if (resource === 'task') {
        const store = window.Alpine?.store?.('taskModal');
        if (store) {
            store.task = {
                title: '',
                description: '',
                priority: 'medium',
                status: 'not_started',
                due_date: '',
            };
            store.mode = 'create';
        }
    }

    if (resource === 'expense') {
        const store = window.Alpine?.store?.('expenseModal');
        if (store) {
            store.expense = {
                amount: '',
                category: '',
                description: '',
                date: new Date().toISOString().split('T')[0],
                payment_method: 'card',
                status: 'confirmed',
            };
            store.mode = 'create';
        }
    }

    form.reset();
}

async function submitAjaxForm(form, resource, formData) {
    const response = await fetch(form.action, {
        method: 'POST',
        headers: {
            ...API_HEADERS,
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: formData,
    });

    const contentType = response.headers.get('content-type') || '';
    const data = contentType.includes('application/json') ? await response.json() : null;

    if (!response.ok) {
        if (response.status === 422 && data?.errors) {
            const errorMessages = Object.values(data.errors).flat().join('\n');
            throw new Error(errorMessages);
        }

        throw new Error(data?.message || 'Unable to save changes.');
    }

    return data ?? {};
}

function dispatchToast(type, title, message) {
    window.dispatchEvent(new CustomEvent('app:toast', {
        detail: { type, title, message },
    }));
}

function upsertRow(resource, rowHtml, recordId, shouldPrepend = false) {
    const tbody = getTbody(resource);
    if (!tbody) return;

    const newRow = renderRow(resource, rowHtml);
    if (!newRow) return;

    const selector = getRowSelector(resource, recordId);
    const existingRow = document.querySelector(selector);

    if (existingRow) {
        existingRow.replaceWith(newRow);
        return;
    }

    const emptyState = tbody.querySelector('[data-empty-state]');
    if (emptyState) {
        tbody.innerHTML = '';
    }

    if (shouldPrepend && tbody.firstElementChild) {
        tbody.prepend(newRow);
    } else {
        tbody.appendChild(newRow);
    }
}

function removeRow(resource, recordId) {
    const row = document.querySelector(getRowSelector(resource, recordId));
    if (row) {
        row.remove();
    }
}

function syncExpenseSummary() {
    const rows = document.querySelectorAll('#expenses-table-body tr[data-expense-id]');
    const values = Array.from(rows).reduce((summary, row) => {
        const amount = Number(row.dataset.expenseAmount ?? 0);
        const status = row.dataset.expenseStatus;

        summary.total += amount;

        if (status === 'confirmed') {
            summary.confirmed += amount;
        }

        if (status === 'pending') {
            summary.pending += amount;
        }

        return summary;
    }, { total: 0, confirmed: 0, pending: 0 });

    const formatMoney = (value) => `$${value.toFixed(2)}`;

    const totalTarget = document.getElementById('expense-summary-total');
    const confirmedTarget = document.getElementById('expense-summary-confirmed');
    const pendingTarget = document.getElementById('expense-summary-pending');

    if (totalTarget) totalTarget.textContent = formatMoney(values.total);
    if (confirmedTarget) confirmedTarget.textContent = formatMoney(values.confirmed);
    if (pendingTarget) pendingTarget.textContent = formatMoney(values.pending);
}

function wireAjaxForms() {
    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;

        const resource = form.dataset.ajaxForm || form.dataset.ajaxRowForm;
        if (!isAjaxResource(resource)) return;

        event.preventDefault();
        const formData = new FormData(form);

        form.querySelectorAll('button, input, select, textarea').forEach((element) => {
            element.disabled = true;
        });
        setLoadingState(form, true);

        try {
            const data = await submitAjaxForm(form, resource, formData);

            if (data.row_html) {
                upsertRow(resource, data.row_html, data.task?.id ?? data.expense?.id ?? form.dataset.recordId, form.dataset.ajaxForm ? true : false);
            }

            if (data.message === 'task-created') {
                dispatchToast('success', 'Task created', 'Your task was saved successfully.');
            }

            if (data.message === 'task-updated') {
                dispatchToast('success', 'Task updated', 'Your task was updated successfully.');
            }

            if (data.message === 'expense-created') {
                dispatchToast('success', 'Expense created', 'Your expense was saved successfully.');
            }

            if (data.message === 'expense-updated') {
                dispatchToast('success', 'Expense updated', 'Your expense was updated successfully.');
            }

            if (data.task_id) {
                removeRow(resource, data.task_id);
                dispatchToast('success', 'Task deleted', 'The task was removed successfully.');
            }

            if (data.expense_id) {
                removeRow(resource, data.expense_id);
                dispatchToast('success', 'Expense deleted', 'The expense was removed successfully.');
            }

            if (resource === 'expense' && (data.row_html || data.expense_id)) {
                syncExpenseSummary();
            }

            if (form.dataset.ajaxForm) {
                closeModal(resource);
                resetModalForm(form, resource);
            }
        } catch (error) {
            const message = error?.message || 'Unable to save changes.';
            dispatchToast('error', 'Save failed', message);
        } finally {
            setLoadingState(form, false);
            form.querySelectorAll('button, input, select, textarea').forEach((element) => {
                element.disabled = false;
            });
        }
    });
}

wireAjaxForms();
