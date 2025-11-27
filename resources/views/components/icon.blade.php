@props(['name' => null, 'class' => 'w-5 h-5'])

@php
    // Normalize name (allow 'tabler:list' or 'list')
    $n = strtolower(str_replace('tabler:', '', $name ?? '') );
@endphp

@if($n === 'list' || $n === 'tabler:list')
    {{-- List / document icon (outline) --}}
    <svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 6h10M7 20h10a2 2 0 0 0 2-2V8l-4-4H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
    </svg>
@elseif($n === 'clock' || $n === 'tabler:clock')
    {{-- Clock / pending --}}
    <svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3M12 6a8 8 0 100 16 8 8 0 000-16z" />
    </svg>
@elseif($n === 'check' || $n === 'tabler:check')
    {{-- Check / success --}}
    <svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
    </svg>
@elseif($n === 'credit-card' || $n === 'tabler:credit-card' || $n === 'card')
    {{-- Credit card / money --}}
    <svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        <rect x="2" y="5" width="20" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round"></rect>
        <path stroke-linecap="round" stroke-linejoin="round" d="M2 10h20" />
    </svg>
@else
    {{-- Fallback: simple square --}}
    <svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        <rect x="3" y="3" width="18" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round"></rect>
    </svg>
@endif
