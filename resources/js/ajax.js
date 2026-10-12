// Lightweight AJAX helper used across the admin/app panels.
//
// All requests send the CSRF token (from the <meta name="csrf-token"> tag),
// ask for JSON, and are treated as XHR by Laravel. Validation failures (422)
// are surfaced as a rejected promise carrying the field errors so callers can
// render them inline.

function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

export class ValidationError extends Error {
    constructor(errors, message) {
        super(message || 'Validation failed');
        this.name = 'ValidationError';
        this.errors = errors || {};
    }
}

async function request(method, url, body) {
    const opts = {
        method,
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
        },
        credentials: 'same-origin',
    };

    if (body instanceof FormData) {
        opts.body = body;
    } else if (body !== undefined && body !== null) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(body);
    }

    const res = await fetch(url, opts);

    if (res.status === 204) {
        return null;
    }

    const isJson = (res.headers.get('content-type') || '').includes('application/json');
    const payload = isJson ? await res.json().catch(() => ({})) : await res.text();

    if (res.status === 422 && isJson) {
        throw new ValidationError(payload.errors || {}, payload.message);
    }

    if (!res.ok) {
        throw new Error((isJson && payload.message) || `Request failed (${res.status})`);
    }

    return payload;
}

export const http = {
    get: (url) => request('GET', url),
    post: (url, body) => request('POST', url, body),
    put: (url, body) => request('PUT', url, body),
    delete: (url) => request('DELETE', url),
};

// --- UI helpers -----------------------------------------------------------

// Toast: a transient status message in the top-right corner.
export function toast(message, type = 'success') {
    const colors = {
        success: 'bg-success-600',
        error: 'bg-error-600',
        info: 'bg-brand-600',
    };
    const el = document.createElement('div');
    el.className =
        `pointer-events-none fixed right-5 top-5 z-[100000] rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-theme-lg transition-all duration-300 ${colors[type] || colors.success}`;
    el.style.opacity = '0';
    el.style.transform = 'translateY(-8px)';
    el.textContent = message;
    document.body.appendChild(el);
    requestAnimationFrame(() => {
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
    });
    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        setTimeout(() => el.remove(), 320);
    }, 2600);
}

// Render validation errors for a form. Looks for [data-error="field.key"]
// elements and fills them; clears all first.
export function showErrors(form, errors) {
    form.querySelectorAll('[data-error]').forEach((el) => {
        el.textContent = '';
        el.classList.add('hidden');
    });
    Object.entries(errors).forEach(([key, messages]) => {
        const el = form.querySelector(`[data-error="${CSS.escape(key)}"]`);
        if (el) {
            el.textContent = Array.isArray(messages) ? messages[0] : messages;
            el.classList.remove('hidden');
        }
    });
}

export function clearErrors(form) {
    form.querySelectorAll('[data-error]').forEach((el) => {
        el.textContent = '';
        el.classList.add('hidden');
    });
}
