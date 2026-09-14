<x-app-layout title="Locations">
	<div class="dashboard locations-page">
		<x-sidebar-component />

		<main class="dashboard-main">
			<header class="dashboard-header">
				<div>
					<h1>Locations</h1>
					<p class="dashboard-breadcrumb">Manage inspection locations across the property.</p>
				</div>
				<div class="header-actions">
					<div class="search-box">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
						<input type="search" placeholder="Search location or zone..." aria-label="Search location or zone">
					</div>
					<button class="icon-btn" type="button" aria-label="Notifications">!</button>
					<a class="add-facility-btn" href="{{ route('locations.create') }}"><span aria-hidden="true">+</span> Add Location</a>
				</div>
			</header>

			<section class="dashboard-cards">
				@foreach($stats as $stat)
					<x-stat-card :stat="$stat" />
				@endforeach
			</section>
			@if(session('status'))
				<div class="form-status">{{ session('status') }}</div>
			@endif
			@if($errors->has('location'))
				<div class="form-error">{{ $errors->first('location') }}</div>
			@endif

			<x-panel title="Location Directory" action-label="Filter: All Locations">
				<div class="admin-table-wrap">
					<table class="schedule-table admin-table">
						<thead>
							<tr>
								<th>Location ID</th>
								<th>Location Name</th>
								<th>Type</th>
								<th>Facilities</th>
								<th>Last Inspection</th>
								<th>Status</th>
									<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@foreach($locations as $location)
								<tr>
									<td><strong>{{ $location['code'] }}</strong></td>
									<td><strong>{{ $location['name'] }}</strong></td>
									<td>{{ $location['type'] }}</td>
									<td>{{ $location['facilities'] }}</td>
									<td>{{ $location['last_inspection'] }}</td>
									<td><x-badge :type="$location['status_class']" :label="$location['status']" /></td>
									<td><a class="table-link" href="{{ route('locations.edit', $location['id']) }}">Edit</a></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</x-panel>

			<section class="dashboard-panel category-panel">
				<div class="panel-header"><h2>Location Categories</h2></div>
				<div class="category-grid">
					@foreach($categories as $category)
						<article class="category-card">
							<div class="category-icon {{ $category['tone'] }}" aria-hidden="true">●</div>
							<div><h3>{{ $category['name'] }}</h3><p>{{ $category['count'] }}</p></div>
						</article>
					@endforeach
				</div>
			</section>
		</main>
	</div>
</x-app-layout>
