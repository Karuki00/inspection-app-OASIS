@props(['title', 'subtitle' => ''])

<header class="dashboard-header">
    <div>
        <h1>{{ $title }}</h1>
        @if($subtitle)
        <p class="dashboard-breadcrumb">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="header-actions">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" placeholder="Search facility or schedule...">
        </div>

        <button class="icon-btn" aria-label="Notifications">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </button>
    </div>
</header>