<x-app-layout title="Problem Statuses">
	<div class="dashboard problems-page">
		<x-sidebar-component />

		<main class="dashboard-main">
			<header class="dashboard-header">
				<div><h1>Problem Statuses</h1><p class="dashboard-breadcrumb">Apartemen OASIS Mitra Sarana • Portal Inspeksi &amp; Fasilitas</p></div>
				<div class="header-actions"><div class="search-box"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg><input type="search" placeholder="Search location or schedule..." aria-label="Search location or schedule"></div><button class="icon-btn" type="button" aria-label="Notifications">!</button></div>
			</header>

			<section class="dashboard-cards">
				@foreach($stats as $stat)<x-stat-card :stat="$stat" />@endforeach
			</section>

			<x-panel title="Problem Tracker" action-label="Filter: All Problems">
				<div class="admin-table-wrap">
					<table class="schedule-table admin-table problems-table">
						<thead><tr><th>Problem ID</th><th>Location</th><th>Category</th><th>Severity</th><th>Reported</th><th>Assigned To</th><th>Status</th><th>Days Open</th></tr></thead>
						<tbody>
							@foreach($problems as $problem)
								<tr><td><strong>{{ $problem['code'] }}</strong></td><td>{{ $problem['location'] }}</td><td><span class="category-pill">{{ $problem['category'] }}</span></td><td><span class="severity-dot {{ $problem['severity_class'] }}">● {{ $problem['severity'] }}</span></td><td>{{ $problem['reported'] }}</td><td>{{ $problem['assigned'] }}</td><td><x-badge :type="$problem['status_class']" :label="$problem['status']" /></td><td><strong>{{ $problem['days_open'] }}</strong></td></tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</x-panel>

			<div class="dashboard-bottom-grid">
				<section class="dashboard-panel summary-panel"><div class="panel-header"><h2>Problems by Severity</h2><button class="panel-link" type="button">View Distribution</button></div>@foreach($severitySummary as $item)<div class="severity-summary-row"><span><i class="severity-dot {{ $item['tone'] }}">●</i>{{ $item['label'] }}</span><strong>{{ $item['count'] }}</strong></div>@endforeach</section>
				<section class="dashboard-panel summary-panel"><div class="panel-header"><h2>Resolution Timeline</h2></div>@foreach($resolutionTimeline as $item)<div class="progress-row"><div><span>{{ $item['label'] }}</span><strong>{{ $item['days'] }}</strong></div><div class="progress-track"><span class="progress-fill yellow {{ $item['width_class'] }}"></span></div></div>@endforeach</section>
			</div>
		</main>
	</div>
</x-app-layout>
