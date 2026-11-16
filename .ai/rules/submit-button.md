# Submit Button Spinner Rule

All submit buttons across the application MUST use the submit button spinner trait/component.

## Rules:
1. When creating forms with submit buttons, ALWAYS use `<x-submit-button>` or `<x-primary-button>` / `<x-danger-button>`, or ensure the submit button has the spinner trait.
2. The spinner trait automatically:
   - Displays an animated loading spinner upon form submission.
   - Prevents double submissions by disabling the button and setting `data-submitting="true"`.
   - Optionally updates the button text when `data-loading-text="..."` is set (e.g. `data-loading-text="সংরক্ষণ হচ্ছে..."` or `data-loading-text="প্রসেসিং..."`).
3. Never create plain submit buttons without spinner feedback and double-click prevention.
