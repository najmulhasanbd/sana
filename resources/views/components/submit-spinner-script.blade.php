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
                btn.classList.add('opacity-80', 'cursor-not-allowed');

                // Check for existing spinner or create one
                let spinner = btn.querySelector('.btn-spinner');
                if (spinner) {
                    spinner.classList.remove('hidden');
                } else {
                    spinner = document.createElement('span');
                    spinner.className = 'btn-spinner inline-flex items-center mr-2';
                    spinner.innerHTML = `
                        <svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    `;
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
                        btn.classList.remove('opacity-80', 'cursor-not-allowed');
                        const spinner = btn.querySelector('.btn-spinner');
                        if (spinner && !btn.classList.contains('always-spin')) {
                            spinner.classList.add('hidden');
                        }
                    });
                });
            }
        });
    })();
</script>
