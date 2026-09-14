@props(['type' => 'default', 'label', 'variant' => 'badge'])

{{-- Supported variants: 'badge' or 'severity-tag' --}}
<span class="{{ $variant }} {{ $type }}">
    {{ $label }}
</span>