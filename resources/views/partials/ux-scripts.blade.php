{{-- Shared UX Scripts: Auto-dismiss flash, form submit loading, dynamic flash helper --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ─── Auto-dismiss flash messages after 6 seconds ───
    document.querySelectorAll('.flash-message').forEach(function(el) {
        setTimeout(function() {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity = '0';
            setTimeout(function() { el.remove(); }, 500);
        }, 6000);
    });

    // ─── Form submit loading state ───
    document.querySelectorAll('form').forEach(function(form) {
        // Skip forms that opt out, AJAX forms, or search/filter forms
        if (form.dataset.noLoading || form.method === 'get' || form.method === 'GET') return;

        form.addEventListener('submit', function(e) {
            // Check if another script (like inline onsubmit="return confirm()") already prevented the submission
            if (e.defaultPrevented) return;

            const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            buttons.forEach(function(btn) {
                if (btn.disabled) return;
                btn.disabled = true;
                btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...';
                btn.classList.add('opacity-75', 'cursor-not-allowed');
            });

            // Re-enable after 10s as safety net (in case of validation errors that prevent redirect)
            setTimeout(function() {
                buttons.forEach(function(btn) {
                    if (btn.dataset.originalText) {
                        btn.disabled = false;
                        btn.innerHTML = btn.dataset.originalText;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    }
                });
            }, 10000);
        });
    });
});

// ─── Dynamic flash message helper (for AJAX responses) ───
function showFlashMessage(message, type) {
    type = type || 'error';
    var colors = {
        success: { bg: 'bg-green-50', border: 'border-green-200', text: 'text-green-700', icon: 'fa-check-circle' },
        error:   { bg: 'bg-red-50',   border: 'border-red-200',   text: 'text-red-700',   icon: 'fa-exclamation-circle' },
        warning: { bg: 'bg-yellow-50', border: 'border-yellow-200', text: 'text-yellow-700', icon: 'fa-exclamation-triangle' },
        info:    { bg: 'bg-blue-50',   border: 'border-blue-200',   text: 'text-blue-700',   icon: 'fa-info-circle' }
    };
    var c = colors[type] || colors.error;
    var el = document.createElement('div');
    el.className = 'flash-message mb-4 ' + c.bg + ' border ' + c.border + ' ' + c.text + ' px-4 py-3 rounded-xl text-sm flex items-center gap-2';
    el.setAttribute('role', 'alert');
    el.innerHTML = '<i class="fas ' + c.icon + ' flex-shrink-0"></i><span>' + message + '</span>' +
        '<button type="button" class="ml-auto ' + c.text + '" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>';
    var mainContent = document.getElementById('main-content');
    if (mainContent) {
        mainContent.insertBefore(el, mainContent.firstChild);
    }
    setTimeout(function() {
        el.style.transition = 'opacity 0.5s ease';
        el.style.opacity = '0';
        setTimeout(function() { el.remove(); }, 500);
    }, 6000);
}

// ─── Auto-submit GET filter forms on change ───
(function() {
    document.querySelectorAll('form[method="GET"], form[method="get"]').forEach(function(form) {
        // Skip forms that opt out
        if (form.dataset.noAutoSubmit) return;

        var debounceTimer;

        // Hide the Filter submit button (no longer needed since auto-submit is active)
        form.querySelectorAll('button[type="submit"]').forEach(function(btn) {
            var text = btn.textContent.trim().toLowerCase();
            if (text.indexOf('filter') !== -1 || text.indexOf('cari') !== -1 || text.indexOf('search') !== -1) {
                btn.style.display = 'none';
            }
        });

        // Auto-submit on select change (instant)
        form.querySelectorAll('select').forEach(function(sel) {
            sel.addEventListener('change', function() {
                form.submit();
            });
        });

        // Auto-submit on text input change (debounced 500ms)
        form.querySelectorAll('input[type="text"], input[type="search"]').forEach(function(input) {
            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    form.submit();
                }, 500);
            });
        });
    });
})();

// ─── Sidebar: scroll active menu item into view ───
(function() {
    // Find any sidebar element
    var sidebar = document.getElementById('admin-sidebar')
        || document.getElementById('guru-sidebar')
        || document.getElementById('siswa-sidebar')
        || document.getElementById('treasurer-sidebar')
        || document.getElementById('ortu-sidebar');

    if (!sidebar) return;

    // The scrollable container may be the sidebar itself or a child div
    var scrollContainer = sidebar;
    if (sidebar.scrollHeight <= sidebar.clientHeight) {
        // Try first scrollable child
        var inner = sidebar.querySelector('.overflow-y-auto, [style*="overflow"]');
        if (inner) scrollContainer = inner;
    }

    // Find the active menu item
    var activeItem = sidebar.querySelector('.menu-item.active, .sidebar-link.active, a.active');
    if (activeItem) {
        // Use requestAnimationFrame to ensure layout is ready
        requestAnimationFrame(function() {
            var containerRect = scrollContainer.getBoundingClientRect();
            var itemRect = activeItem.getBoundingClientRect();
            var scrollOffset = itemRect.top - containerRect.top - (containerRect.height / 3);
            scrollContainer.scrollTop += scrollOffset;
        });
    }
})();
</script>
