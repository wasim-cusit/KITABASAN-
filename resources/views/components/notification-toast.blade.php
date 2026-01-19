<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-6 right-4 z-50 flex flex-col gap-3 max-w-md" style="pointer-events: none;"></div>

<script>
(function() {
    'use strict';

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.style.pointerEvents = 'auto';

        // Set colors based on type
        const colors = {
            success: {
                bg: 'bg-green-500',
                border: 'border-green-600',
                icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                iconColor: 'text-green-600'
            },
            error: {
                bg: 'bg-red-500',
                border: 'border-red-600',
                icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                iconColor: 'text-red-600'
            },
            warning: {
                bg: 'bg-yellow-500',
                border: 'border-yellow-600',
                icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                iconColor: 'text-yellow-600'
            },
            info: {
                bg: 'bg-blue-500',
                border: 'border-blue-600',
                icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                iconColor: 'text-blue-600'
            }
        };

        const config = colors[type] || colors.success;

        toast.innerHTML = `
            <div class="${config.bg} ${config.border} border-l-4 text-white shadow-xl rounded-lg p-4 flex items-start gap-3 animate-slide-in-right" role="alert">
                <div class="bg-white rounded-full p-1.5 flex-shrink-0">
                    <svg class="w-5 h-5 ${config.iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${config.icon}"></path>
                    </svg>
                </div>
                <div class="flex-1 pt-0.5 min-w-0">
                    <p class="font-medium text-sm break-words">${escapeHtml(message)}</p>
                </div>
                <button onclick="window.closeToast(this)" class="flex-shrink-0 text-white/80 hover:text-white transition-colors ml-2" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        container.appendChild(toast);

        // Auto remove after 5 seconds
        const autoClose = setTimeout(() => {
            closeToastElement(toast);
        }, 5000);

        // Store timeout ID on toast element
        toast._autoClose = autoClose;
    }

    function closeToastElement(toast) {
        if (!toast) return;

        // Clear auto-close timeout if exists
        if (toast._autoClose) {
            clearTimeout(toast._autoClose);
        }

        toast.style.animation = 'slide-out-right 0.3s ease-out';
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }

    window.closeToast = function(button) {
        const toast = button ? button.closest('.toast-notification') : null;
        if (toast) {
            closeToastElement(toast);
        }
    };

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Make function globally available
    window.showToast = showToast;

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToasts);
    } else {
        initToasts();
    }

    function initToasts() {
        // Check for flash messages and show toast
        @if(session('success'))
            showToast(@json(session('success')), 'success');
        @endif

        @if(session('error'))
            showToast(@json(session('error')), 'error');
        @endif

        @if(session('warning'))
            showToast(@json(session('warning')), 'warning');
        @endif

        @if(session('info'))
            showToast(@json(session('info')), 'info');
        @endif

        // Show validation errors
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast(@json($error), 'error');
            @endforeach
        @endif
    }
})();
</script>

<style>
@keyframes slide-in-right {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slide-out-right {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.animate-slide-in-right {
    animation: slide-in-right 0.3s ease-out;
}

@media (max-width: 640px) {
    #toast-container {
        top: 1.5rem;
        right: 1rem;
        left: 1rem;
        max-width: none;
    }
}
</style>
