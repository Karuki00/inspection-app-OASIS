<aside class="sidebar">

    <!-- Top section -->
    <div class="sidebar-top">

        <div class="sidebar-logo">
            <img src="{{ asset('images/LogoOASIS.png') }}" alt="Logo">
            <div class="sidebar-logo-text">
                <h3>Inspection App</h3>
                <span>Management Panel</span>
            </div>
        </div>

        <nav class="sidebar-menu">

            <!-- Dashboard -->
            <a href="{{ route('dashboard.index') }}"
                class="sidebar-item {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.5 1H1.75C1.33579 1 1 1.33579 1 1.75V7C1 7.41421 1.33579 7.75 1.75 7.75H5.5C5.91421 7.75 6.25 7.41421 6.25 7V1.75C6.25 1.33579 5.91421 1 5.5 1Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    <path d="M13.75 1H10C9.58579 1 9.25 1.33579 9.25 1.75V4C9.25 4.41421 9.58579 4.75 10 4.75H13.75C14.1642 4.75 14.5 4.41421 14.5 4V1.75C14.5 1.33579 14.1642 1 13.75 1Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    <path d="M13.75 7.75H10C9.58579 7.75 9.25 8.08579 9.25 8.5V13.75C9.25 14.1642 9.58579 14.5 10 14.5H13.75C14.1642 14.5 14.5 14.1642 14.5 13.75V8.5C14.5 8.08579 14.1642 7.75 13.75 7.75Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    <path d="M5.5 10.75H1.75C1.33579 10.75 1 11.0858 1 11.5V13.75C1 14.1642 1.33579 14.5 1.75 14.5H5.5C5.91421 14.5 6.25 14.1642 6.25 13.75V11.5C6.25 11.0858 5.91421 10.75 5.5 10.75Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Security Accounts -->
            <a href="{{ route('security-accounts.index') }}"
                class="sidebar-item {{ request()->routeIs('security-accounts.*') ? 'active' : '' }}">
                <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.25437 15.9633C10.3741 14.8758 12.9988 13.0007 12.9988 9.2506V4.00043C12.9988 3.80152 12.9198 3.61074 12.7792 3.47009C12.6385 3.32943 12.4478 3.25041 12.2489 3.25041C10.749 3.25041 8.88171 2.35788 7.56934 1.21034C7.41047 1.07459 7.20836 1 6.9994 1C6.79044 1 6.58833 1.07459 6.42946 1.21034C5.12459 2.35038 3.24977 3.25041 1.74993 3.25041C1.55103 3.25041 1.36029 3.32943 1.21965 3.47009C1.07901 3.61074 1 3.80152 1 4.00043V9.2506C1 13.0007 3.62474 14.8758 6.75192 15.9558C6.91356 16.016 7.09101 16.0187 7.25437 15.9633Z" stroke="#8C8478" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Security Accounts</span>
            </a>

            <!-- Facilities -->
            <a href="{{ route('facilities.index') }}"
                class="sidebar-item {{ request()->routeIs('facilities.*') ? 'active' : '' }}">
                <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.9994 7.00048H7.0069M6.9994 10.0007H7.0069M6.9994 4.00024H7.0069M9.9991 7.00048H10.0066M9.9991 10.0007H10.0066M9.9991 4.00024H10.0066M3.9997 7.00048H4.0072M3.9997 10.0007H4.0072M3.9997 4.00024H4.0072M4.74963 16.0012V13.751C4.74963 13.5521 4.82863 13.3613 4.96927 13.2206C5.10991 13.08 5.30066 13.001 5.49955 13.001H8.49925C8.69814 13.001 8.88889 13.08 9.02953 13.2206C9.17017 13.3613 9.24918 13.5521 9.24918 13.751V16.0012M2.49985 1H11.499C12.3273 1 12.9988 1.67163 12.9988 2.50012V14.5011C12.9988 15.3296 12.3273 16.0012 11.499 16.0012H2.49985C1.67151 16.0012 1 15.3296 1 14.5011V2.50012C1 1.67163 1.67151 1 2.49985 1Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Facilities</span>
            </a>

            <!-- Inspection Schedules -->
            <a href="{{ route('inspection-schedules.index') }}"
                class="sidebar-item {{ request()->routeIs('inspection-schedules.*') ? 'active' : '' }}">
                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.75 1V4.00024M10.75 1V4.00024M1 7.00048H14.5M2.5 2.50012H13C13.8284 2.50012 14.5 3.17175 14.5 4.00024V14.5011C14.5 15.3296 13.8284 16.0012 13 16.0012H2.5C1.67157 16.0012 1 15.3296 1 14.5011V4.00024C1 3.17175 1.67157 2.50012 2.5 2.50012Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Inspection Schedules</span>
            </a>

            <!-- Locations -->
            <a href="{{ route('locations.index') }}"
                class="sidebar-item {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.4501 15.8506C8.84497 14.646 12.9988 10.7457 12.9988 7.00056C12.9988 5.40911 12.3667 3.88284 11.2416 2.75752C10.1165 1.6322 8.59054 1 6.9994 1C5.40826 1 3.88229 1.6322 2.75718 2.75752C1.63208 3.88284 1 5.40911 1 7.00056C1 10.7457 5.15383 14.646 6.5487 15.8506C6.67864 15.9484 6.83682 16.0012 6.9994 16.0012C7.16198 16.0012 7.32016 15.9484 7.4501 15.8506Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Locations</span>
            </a>

            <!-- Inspection Reports -->
            <a href="{{ route('inspection-reports.index') }}"
                class="sidebar-item {{ request()->routeIs('inspection-reports.*') ? 'active' : '' }}">
                <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.49925 1H2.49985C2.10206 1 1.72057 1.15805 1.4393 1.43938C1.15802 1.7207 1 2.10227 1 2.50012V14.5011C1 14.8989 1.15802 15.2805 1.4393 15.5618C1.72057 15.8432 2.10206 16.0012 2.49985 16.0012H11.4989C11.8967 16.0012 12.2782 15.8432 12.5595 15.5618C12.8408 15.2805 12.9988 14.8989 12.9988 14.5011V5.50036M8.49925 1C8.73664 0.999618 8.97176 1.04621 9.19108 1.13709C9.41039 1.22797 9.60956 1.36135 9.77712 1.52954L12.4679 4.22076C12.6365 4.3884 12.7702 4.5878 12.8613 4.80743C12.9525 5.02706 12.9992 5.26257 12.9988 5.50036M8.49925 1V4.7503C8.49925 4.94923 8.57826 5.14001 8.7189 5.28067C8.85953 5.42133 9.05028 5.50036 9.24917 5.50036L12.9988 5.50036M5.49955 6.25042H3.9997M9.9991 9.25066H3.9997M9.9991 12.2509H3.9997" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Inspection Reports</span>
            </a>

            <!-- Problem Statuses -->
            <a href="{{ route('problems.index') }}"
                class="sidebar-item {{ request()->routeIs('problems.*') ? 'active' : '' }}">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.5006 5.50036V8.5006M8.5006 11.5008H8.5081M16.0012 8.5006C16.0012 12.6431 12.6431 16.0012 8.5006 16.0012C4.35813 16.0012 1 12.6431 1 8.5006C1 4.35813 4.35813 1 8.5006 1C12.6431 1 16.0012 4.35813 16.0012 8.5006Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Problem Statuses</span>
            </a>

        </nav>

    </div>

    <!-- Bottom section -->
    <div class="sidebar-bottom">

        <div class="sidebar-user">
            <img src="{{ asset('icons/user.svg') }}" alt="User Profile">
            <div class="sidebar-user-info">
                <p>Admin</p>
                <span>Administrator</span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-item logout" style="background:none; border:none; width:100%; cursor:pointer;">
                <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.66667 3.66667L13 7L9.66667 10.3333M13 7H5M5 13H2.33333C1.97971 13 1.64057 12.8595 1.39052 12.6095C1.14048 12.3594 1 12.0203 1 11.6667V2.33333C1 1.97971 1.14048 1.64057 1.39052 1.39052C1.64057 1.14048 1.97971 1 2.33333 1H5" stroke="#8C8478" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Logout</span>
            </button>
        </form>

    </div>

</aside>