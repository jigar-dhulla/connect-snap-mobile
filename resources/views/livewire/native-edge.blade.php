<div>
    {{-- Top Bar --}}
    <native:top-bar
        :title="$title"
        :show-navigation-icon="false"
    >
        <native:top-bar-action
            id="logout"
            icon="arrow-right-start-on-rectangle"
            label="Logout"
            url="{{ route('logout') }}"
        />
    </native:top-bar>

    {{-- Bottom Navigation - 3 Tab Structure per PRD --}}
    <native:bottom-nav label-visibility="labeled">
        {{-- Tab 1: My QR Code --}}
        <native:bottom-nav-item
            id="home"
            label="My QR"
            url="{{ route('home') }}"
            icon="qrcode"
            :active="request()->routeIs('home')"
        />

        {{-- Tab 2: Scan --}}
        <native:bottom-nav-item
            id="scan"
            label="Scan"
            url="{{ route('scan') }}"
            icon="camera"
            :active="request()->routeIs('scan')"
        />

        {{-- Tab 3: Connections --}}
        <native:bottom-nav-item
            id="connections"
            label="Connections"
            url="{{ route('connections.index') }}"
            icon="users"
            badge="{{ $connectionsCount > 0 ? $connectionsCount : '' }}"
            :active="request()->routeIs('connections.*')"
        />
    </native:bottom-nav>
</div>