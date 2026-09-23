<x-app-layout title="Inspection Schedules">
    <div class="dashboard schedules-page">
        <x-sidebar-component />

        <main class="dashboard-main">
            <header class="dashboard-header schedules-header">
                <div>
                    <h1>Inspection Schedules</h1>
                    <p class="dashboard-breadcrumb">Security patrol schedules and inspection reports</p>
                </div>

                <div class="header-actions">
                    <div class="search-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="search" placeholder="Search schedule or officer..." aria-label="Search schedule or officer">
                    </div>
                    <a class="add-facility-btn" href="{{ route('inspection-schedules.create') }}"><span aria-hidden="true">+</span> Add Schedule</a>
                </div>
            </header>

            <section class="dashboard-cards schedules-stats">
                @foreach($stats as $stat)
                    <x-stat-card :stat="$stat" />
                @endforeach
            </section>

            @if(session('status'))
                <div class="form-status">{{ session('status') }}</div>
            @endif
            @if($errors->has('schedule'))
                <div class="form-error">{{ $errors->first('schedule') }}</div>
            @endif

            <x-panel title="Security Inspection Schedules" action-label="Filter: All Schedules">
                <div class="schedule-table-wrap">
                    <table class="schedule-table inspection-schedule-table">
                        <thead>
                            <tr>
                                <th>Schedule ID</th>
                                <th>Security Officer</th>
                                <th>Patrol Area / Zone</th>
                                <th>Date</th>
                                <th>Time (Shift)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td><strong>{{ $schedule['code'] }}</strong></td>
                                    <td>{{ $schedule['officer'] }}</td>
                                    <td>{{ $schedule['zone'] }}</td>
                                    <td>{{ $schedule['date'] }}</td>
                                    <td>{{ $schedule['time'] }}</td>
                                    <td><x-badge :type="$schedule['status_class']" :label="$schedule['status']" /></td>
                                    <td>
                                        <a class="table-link" href="{{ route('inspection-schedules.edit', $schedule['id']) }}">Edit</a>
                                        @if(in_array($schedule['status_class'], ['scheduled', 'overdue']))
                                            <form class="inline-form" method="POST" action="{{ route('inspection-schedules.destroy', $schedule['id']) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="table-link danger-link" type="submit">Cancel</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-panel>

            <section class="dashboard-panel recent-reports-panel">
                <div class="panel-header">
                    <h2>Recent Reports</h2>
                    <button class="panel-link" type="button">View All Reports</button>
                </div>
                <div class="recent-reports-list">
                    @foreach($reports as $report)
                        <article class="recent-report-row">
                            <div>
                                <h3>{{ $report['title'] }}</h3>
                                <p>{{ $report['detail'] }}</p>
                            </div>
                            <div class="recent-report-actions">
                                <x-badge :type="$report['status_class']" :label="$report['status']" />
                                <button class="table-link" type="button">View Report</button>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </main>
    </div>
</x-app-layout>
