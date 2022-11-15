@props(['active'])

@if($active)
    <span
        class="absolute inset-y-0 left-0 w-1 bg-green-700 rounded-tr-lg rounded-br-lg"
        aria-hidden="true"
    ></span>
@endif
{{-- <a {{ $attributes->merge(['class' => 'inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800']) }}>
    {{ $icon ?? '' }}
    <span class="inline-flex items-center">
        <span class="ml-4"></span>
    </span>
    <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
    </svg>
</a> --}}

<button
    {{ $attributes->merge(['class' => 'inline-flex items-center text-gray-800 justify-between w-full text-sm font-semibold transition-colors duration-150']) }}
    type="button"
    data-bs-toggle="collapse"
    aria-expanded="false"
    >
    <span class="inline-flex items-center">
        {{ $icon ?? '' }}
        <span class="ml-4">{{ $slot }}</span>
    </span>
    <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
    </svg>
    </button>
