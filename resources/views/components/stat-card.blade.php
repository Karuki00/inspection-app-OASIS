@props(['stat'])

@php
$subtextClass = match($stat['tone'] ?? '') {
'green' => 'positive',
'red' => 'negative',
default => '',
};
@endphp

<div class="dashboard-card">
    <div class="card-top">
        <span class="card-label">{{ $stat['label'] }}</span>
        <div class="card-icon-badge {{ $stat['tone'] }}">
            <svg width="18" height="18" viewBox="{{ $stat['viewBox'] ?? '0 0 18 18' }}" fill="none" aria-hidden="true">
                {!! $stat['icon'] !!}
            </svg>
        </div>
    </div>
    <div>
        <div class="card-value">{{ $stat['value'] }}</div>
        <div class="card-subtext {{ $subtextClass }}">
            {{ $stat['meta'] }}
        </div>
    </div>
</div>