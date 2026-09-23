<x-app-layout title="Security Accounts">
    <div class="dashboard security-accounts-page">
        <x-sidebar-component />

        <main class="dashboard-main">
            <header class="dashboard-header security-header">
                <div>
                    <h1>Security Accounts</h1>
                    <p class="dashboard-breadcrumb">Manage security personnel assigned to the property</p>
                </div>
                <div class="header-actions">
                    <div class="search-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="search" placeholder="Search guard or badge..." aria-label="Search guard or badge">
                    </div>
                    <a class="add-facility-btn" href="{{ route('security-accounts.create') }}"><span aria-hidden="true">+</span> Add Security</a>
                </div>
            </header>
            <section class="dashboard-cards security-stats">
                @foreach($stats as $stat)<x-stat-card :stat="$stat" />@endforeach
            </section>
            @if(session('status'))
                <div class="form-status">{{ session('status') }}</div>
            @endif
            @if($errors->has('security_account'))
                <div class="form-error">{{ $errors->first('security_account') }}</div>
            @endif
            <x-panel title="Security Personnel" action-label="Filter: All Personnel">
                <x-security-personnel-table :personnel="$personnel" />
            </x-panel>
        </main>
    </div>
</x-app-layout>
