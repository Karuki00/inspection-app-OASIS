<x-app-layout title="Inspection Reports">
	<div class="dashboard reports-page">
		<x-sidebar-component />

		<main class="dashboard-main">
			<header class="dashboard-header">
				<div>
					<h1>Inspection Reports</h1>
					<p class="dashboard-breadcrumb">Apartemen OASIS Mitra Sarana • Portal Inspeksi &amp; Fasilitas</p>
				</div>
				<div class="header-actions">
					<div class="search-box">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
						<input type="search" placeholder="Search facility or schedule..." aria-label="Search facility or schedule">
					</div>
					<button class="icon-btn" type="button" aria-label="Notifications">!</button>
				</div>
			</header>

			<section class="dashboard-cards">
				@foreach($stats as $stat)
					<x-stat-card :stat="$stat" />
				@endforeach
			</section>

			<x-panel title="Inspection Reports History">
				<div class="report-panel-actions">
					<form method="POST" action="{{ route('inspection-reports.import-zip') }}" enctype="multipart/form-data">
						@csrf
						<label class="filter-btn">Import ZIP <input type="file" name="report_zip" accept=".zip,application/zip" required></label>
						<button class="add-facility-btn" type="submit">Import Report</button>
					</form>
				</div>
				@if(session('status'))
					<div class="form-success">{{ session('status') }}</div>
				@endif
				@if($errors->has('report_zip'))
					<div class="form-error">{{ $errors->first('report_zip') }}</div>
				@endif
				<div class="admin-table-wrap">
					<table class="schedule-table admin-table reports-table">
						<thead><tr><th>Report ID</th><th>Facility / Location Name</th><th>Inspector Name</th><th>Inspection Date</th><th>Type</th><th>Findings</th><th>Status</th><th>Actions</th></tr></thead>
						<tbody>
							@foreach($reports as $report)
								<tr>
									<td><strong>{{ $report['code'] }}</strong></td><td>{{ $report['facility'] }}</td><td>{{ $report['inspector'] }}</td><td>{{ $report['date'] }}</td>
									<td><x-badge :type="$report['type_class']" :label="$report['type']" /></td><td>{{ $report['findings'] }}</td><td><x-badge :type="$report['status_class']" :label="$report['status']" /></td>
									<td><a class="table-link" href="{{ route('inspection-reports.pdf', $report['id']) }}">▣ PDF</a></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</x-panel>

			<div class="dashboard-bottom-grid">
				<section class="dashboard-panel summary-panel">
					<div class="panel-header"><h2>Reports by Category</h2><button class="panel-link" type="button">View breakdown</button></div>
					@foreach($categories as $category)
						<div class="progress-row"><div><span>{{ $category['name'] }}</span><strong>{{ $category['count'] }}</strong></div><div class="progress-track"><span class="progress-fill {{ $category['tone'] }} {{ $category['width_class'] }}"></span></div></div>
					@endforeach
				</section>
				<section class="dashboard-panel summary-panel">
					<div class="panel-header"><h2>Recent Activity</h2></div>
					@foreach($activity as $item)
						<div class="activity-row"><div><strong>{{ $item['text'] }}</strong><small>{{ $item['time'] }}</small></div><x-badge :type="$item['status_class']" :label="$item['status']" /></div>
					@endforeach
				</section>
			</div>
		</main>
	</div>
</x-app-layout>
