@props(['name', 'class' => 'w-5 h-5'])

@php
    $path = base_path("node_modules/lucide/icons/{$name}.svg");
@endphp

@if (file_exists($path))
    {!! str_replace('<svg', '<svg class="'. $class .'"', file_get_contents($path)) !!}
@else
    <!-- fallback jika icon tidak ditemukan -->
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" stroke-width="2"/>
        <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
        <line x1="12" y1="16" x2="12" y2="16" stroke-width="2"/>
    </svg>
@endif
