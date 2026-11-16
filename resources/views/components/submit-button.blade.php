@props([
    'loadingText' => null,
    'spinnerClass' => 'h-4 w-4 text-current',
    'type' => 'submit'
])

<button {{ $attributes->merge([
    'type' => $type,
    'class' => 'btn-submit-with-spinner relative inline-flex items-center justify-center px-4 py-2.5 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 border border-transparent rounded-xl font-semibold text-sm text-white tracking-wide shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-75 disabled:cursor-not-allowed'
]) }}
    @if($loadingText) data-loading-text="{{ $loadingText }}" @endif
>
    <span class="btn-spinner hidden inline-flex items-center mr-2">
        <svg class="animate-spin {{ $spinnerClass }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </span>
    <span class="btn-content inline-flex items-center gap-2">
        {{ $slot }}
    </span>
</button>
