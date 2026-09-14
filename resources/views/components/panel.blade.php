@props(['title', 'actionLabel' => null])

<section class="dashboard-panel">
    <div class="panel-header">
        <h2>{{ $title }}</h2>
        @if($actionLabel)
        <button class="filter-btn">{{ $actionLabel }}</button>
        @endif
    </div>

    {{ $slot }}
</section>