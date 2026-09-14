<x-app-layout title="Dashboard">
    <div class="dashboard">
        <x-sidebar-component />

        <div class="dashboard-main">
            <!-- Header -->
            <x-header title="Dashboard" subtitle="Welcome back, Admin." />

            <!-- Summary Cards -->
            <section class="dashboard-cards">
                @foreach($stats as $stat)
                <x-stat-card :stat="$stat" />
                @endforeach
            </section>

            <!-- Upcoming Schedules Table -->
            <x-panel title="Upcoming Inspection Schedules" action-label="Filter">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Facility Item</th>
                            <th>Location</th>
                            <th>Inspector</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                        <tr>
                            <td><strong>{{ $schedule['schedule_id'] }}</strong></td>
                            <td>{{ $schedule['facility'] }}</td>
                            <td>{{ $schedule['location'] }}</td>
                            <td>{{ $schedule['inspector'] }}</td>
                            <td>{{ $schedule['date'] }}</td>
                            <td>
                                <x-badge :type="$schedule['status_class']" :label="$schedule['status']" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-panel>

            <!-- Bottom Two Column Grid -->
            <div class="dashboard-bottom-grid">

                <!-- Reported Facility Issues -->
                <x-panel title="Reported Facility Issues" action-label="View All">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Issue Title</th>
                                <th>Location</th>
                                <th>Severity</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($problems as $problem)
                            <tr>
                                <td><strong>{{ $problem['title'] }}</strong></td>
                                <td>{{ $problem['location'] }}</td>
                                <td>
                                    <x-badge variant="severity-tag" :type="$problem['severity_class']" :label="$problem['severity']" />
                                </td>
                                <td>{{ $problem['updated'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-panel>

                <!-- System Health Summary -->
                <x-panel title="Facility Health">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($health as $item)
                            <tr>
                                <td><strong>{{ $item['label'] }}</strong></td>
                                <td>{{ $item['value'] }}</td>
                                <td>
                                    <x-badge type="completed" :label="$item['detail']" />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-panel>

            </div>
        </div>
    </div>
</x-app-layout>