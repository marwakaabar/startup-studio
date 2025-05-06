<aside id="layout-menu" class="aside layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/dash/logo.png') }}" alt="">
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="menu-toggle-icon ti ti-text-wrap-disabled d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon">
                    <span class="iconify" data-icon="material-symbols:dashboard-outline-rounded" data-inline="false"></span>
                </i>
                <div data-i18n="Tableau de bord">Tableau de bord</div>
            </a>
        </li>

        <!-- Startups -->
        <li class="menu-item">
            <a href="{{ route('admin.users.startups') }}" class="menu-link">
                <i class="menu-icon">
                    <span class="iconify" data-icon="mdi:rocket-launch-outline" data-inline="false"></span>
                </i>
                <div data-i18n="Gestion des Startups">Gestion des Startups</div>
            </a>
        </li>

        <!-- Coachs -->
        <li class="menu-item">
            <a href="{{ route('admin.users.coachs') }}" class="menu-link">
                <i class="menu-icon">
                    <span class="iconify" data-icon="mdi:account-tie-outline" data-inline="false"></span>
                </i>
                <div data-i18n="Gestion des Coachs">Gestion des Coachs</div>
            </a>
        </li>

        <!-- Investors -->
        <li class="menu-item">
            <a href="{{ route('admin.users.investors') }}" class="menu-link">
                <i class="menu-icon">
                    <span class="iconify" data-icon="mdi:cash" data-inline="false"></span>
                </i>
                <div data-i18n="Gestion des Investisseurs">Gestion des Investisseurs</div>
            </a>
        </li>

        <!-- Reports -->
        <li class="menu-item">
            <a href="{{ route('admin.reports.index') }}" class="menu-link">
                <i class="menu-icon">
                    <span class="iconify" data-icon="material-symbols:flag" data-inline="false"></span>
                </i>
                <div data-i18n="Signalements">Signalements</div>
            </a>
        </li>

    </ul>
</aside>