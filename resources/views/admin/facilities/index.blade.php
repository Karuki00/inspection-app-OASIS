<x-app-layout title="Facilities">
    <div class="dashboard facilities-page">
        <x-sidebar-component />

        <main class="dashboard-main">
            <header class="dashboard-header facilities-header">
                <div>
                    <h1>Facilities</h1>
                    <p class="dashboard-breadcrumb">Manage and monitor important facilities and equipment.</p>
                </div>

                <div class="header-actions">
                    <div class="search-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="search" placeholder="Search facility or hydrant..." aria-label="Search facility or hydrant">
                    </div>
                    <button class="icon-btn" aria-label="Notifications">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </button>
                    <a class="add-facility-btn" href="{{ route('facilities.create') }}">
                        <span aria-hidden="true">+</span>
                        Add Facility
                    </a>
                </div>
            </header>

            <section class="dashboard-cards facilities-stats">
                @foreach($stats as $stat)
                    <x-stat-card :stat="$stat" />
                @endforeach
            </section>

            @if(session('status'))
                <div class="form-status">{{ session('status') }}</div>
            @endif
            @if($errors->has('category'))
                <div class="form-error">{{ $errors->first('category') }}</div>
            @endif

            <x-panel title="Facilities &amp; Equipment (Box Hydrants)" action-label="Filter: All">
                <div class="facilities-table-wrap">
                    <table class="schedule-table facilities-table">
                        <thead>
                            <tr>
                                <th>Facility ID</th>
                                <th>Facility Name</th>
                                <th>Category</th>
                                <th>Location / Floor</th>
                                <th>Condition</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facilities as $facility)
                                <tr>
                                    <td><strong>{{ $facility['code'] }}</strong></td>
                                    <td>{{ $facility['name'] }}</td>
                                    <td>{{ $facility['category'] }}</td>
                                    <td>{{ $facility['location'] }}</td>
                                    <td><span class="condition-pill {{ $facility['condition_class'] }}">{{ $facility['condition'] }}</span></td>
                                    <td><x-badge :type="$facility['status_class']" :label="$facility['status']" /></td>
                                    <td>
                                        <a class="table-link" href="{{ route('facilities.edit', $facility['id']) }}">Edit</a>
                                        <form class="inline-form" method="POST" action="{{ route('facilities.destroy', $facility['id']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="table-link danger-link" type="submit">Archive</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-panel>

            <section class="dashboard-panel category-panel">
                <div class="panel-header">
                    <h2>Other Facility Categories</h2>
                    <a class="filter-btn" href="{{ route('facilities.categories.create') }}">Add Category</a>
                </div>
                <div class="category-grid">
                    @foreach($categories as $category)
                        <article class="category-card">
                            <div class="category-icon {{ $category['tone'] }}" aria-hidden="true">
                                <svg width="17" height="17" viewBox="0 0 18 18" fill="none">{!! $category['icon'] !!}</svg>
                            </div>
                            <div class="category-card-content">
                                <h3>{{ $category['name'] }}</h3>
                                <p>{{ $category['count'] }}</p>
                                <div class="category-card-actions">
                                    <a class="table-link" href="{{ route('facilities.categories.edit', $category['id']) }}">Edit</a>
                                    <form class="inline-form" method="POST" action="{{ route('facilities.categories.destroy', $category['id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="table-link danger-link" type="submit">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </main>
    </div>
</x-app-layout>