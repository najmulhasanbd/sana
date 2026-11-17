<style>
    .btn-spinner.hidden,
    .btn-spinner.d-none {
        display: none !important;
    }
    @keyframes btn-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: btn-spin 1s linear infinite !important;
        display: inline-block;
    }
</style>

<script>
    /**
     * Reusable Submit Button Spinner Trait
     * Automatically handles loading spinner and double-click prevention on ALL form submit buttons.
     */
    (function () {
        if (window.__submitSpinnerTraitInitialized) return;
        window.__submitSpinnerTraitInitialized = true;

        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            // HTML5 Form Validation check
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                return; // Let browser show native validation tooltip, do not spin
            }

            // Prevent duplicate submissions
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }

            const submitButtons = form.querySelectorAll(
                'button[type="submit"], input[type="submit"], button:not([type="button"]):not([type="reset"])'
            );

            if (!submitButtons || submitButtons.length === 0) return;

            form.dataset.submitting = 'true';

            submitButtons.forEach(function (btn) {
                if (btn.dataset.noSpinner !== undefined) return;

                // Disable button
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');

                // Check for existing spinner or create one
                let spinner = btn.querySelector('.btn-spinner');
                if (spinner) {
                    spinner.classList.remove('hidden', 'd-none');
                    spinner.style.display = 'inline-flex';
                } else {
                    spinner = document.createElement('span');
                    spinner.className = 'btn-spinner spinner-border spinner-border-sm me-2 inline-flex items-center';
                    spinner.setAttribute('role', 'status');
                    spinner.setAttribute('aria-hidden', 'true');
                    btn.prepend(spinner);
                }

                // Update text if loading-text is configured
                const loadingText = btn.dataset.loadingText;
                if (loadingText) {
                    const textSpan = btn.querySelector('.btn-content') || btn.querySelector('.btn-text');
                    if (textSpan) {
                        textSpan.textContent = loadingText;
                    }
                }
            });
        }, true);

        // Reset if user navigates back via BFCache (Back-Forward Cache)
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) {
                document.querySelectorAll('form[data-submitting="true"]').forEach(function (form) {
                    form.removeAttribute('data-submitting');
                    form.querySelectorAll('button, input[type="submit"]').forEach(function (btn) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                        const spinner = btn.querySelector('.btn-spinner');
                        if (spinner && !btn.classList.contains('always-spin')) {
                            spinner.classList.add('hidden', 'd-none');
                            spinner.style.display = 'none';
                        }
                    });
                });
            }
        });
    })();
</script>
