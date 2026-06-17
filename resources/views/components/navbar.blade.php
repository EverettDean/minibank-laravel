<header class="top-nav" style="display: flex; align-items: center; justify-content: space-between;">

    <div class="header-left" style="display: flex; align-items: center;">

        <button id="sidebarToggle" class="burger-btn">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" placeholder="Cari data..." class="search-input">
        </div>

    </div>

    <div class="header-right">

        <!-- <div class="notif-wrapper" style="position: relative; display: inline-block; cursor: pointer;">
            <a href="javascript:void(0)"
                onclick="toggleNotif(event)"
                style="text-decoration: none; color: inherit; position: relative;">

                <i class="fa-solid fa-bell" style="font-size: 1.2em;"></i>

                @if(isset($unreadCount) && $unreadCount > 0)
                <span class="notif-badge" style="position: absolute; top: -8px; right: -8px; background: #e53e3e; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold;">
                    {{ $unreadCount }}
                </span>
                @endif
            </a>

            <div id="notif-dropdown"
                style="display: none; position: absolute; right: 0; top: 30px; width: 280px; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); z-index: 9999;">

                <div style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; font-size: 14px;">Notifikasi</div>

                <div style="max-height: 300px; overflow-y: auto;">
                    @php
                    $latestNotifs = DB::table('notifications')
                    ->where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                    @endphp

                    @forelse($latestNotifs as $n)
                    <div style="padding: 10px; border-bottom: 1px solid #f9f9f9; font-size: 13px;">
                        <div style="font-weight: 600; color: #333;">{{ $n->judul }}</div>
                        <div style="color: #666; margin-top: 4px;">{{ $n->pesan }}</div>
                    </div>
                    @empty
                    <div style="padding: 15px; text-align: center; color: #999; font-size: 13px;">Tidak ada notifikasi</div>
                    @endforelse
                </div>
            </div>
        </div> -->

        <div class="notif-wrapper" style="position: relative; display: inline-block; cursor: pointer;">
            <!-- <a href="javascript:void(0)"
                onclick="toggleNotif(event)"
                style="text-decoration: none; color: inherit; position: relative;">

                <i class="fa-solid fa-bell" style="font-size: 1.2em;"></i>

                @if(isset($unreadCount) && $unreadCount > 0)
                <span class="notif-badge" style="position: absolute; top: -8px; right: -8px; background: #e53e3e; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold;">
                    {{ $unreadCount }}
                </span>
                @endif
            </a> -->

            <a href="javascript:void(0)"
                onclick="toggleNotif(event)"
                style="text-decoration: none; color: inherit; position: relative;"> <i class="fa-solid fa-bell" style="font-size: 1.2em;"></i>

                @if(isset($unreadCount) && $unreadCount > 0)
                <span class="notif-badge" style="position: absolute; top: -8px; right: -8px; background: #e53e3e; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold;">{{ $unreadCount }}</span>
                @endif
            </a>

            <div id="notif-dropdown"
                style="display: none; position: absolute; right: 0; top: 30px; width: 280px; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); z-index: 9999;">

                <div style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; font-size: 14px;">Notifikasi</div>

                <div style="max-height: 300px; overflow-y: auto;">
                    @php
                    $latestNotifs = DB::table('notifications')
                    ->where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                    @endphp

                    @forelse($latestNotifs as $n)
                    <div style="padding: 10px; border-bottom: 1px solid #f9f9f9; font-size: 13px; background-color: {{ $n->is_read ? '#ffffff' : '#ebf8ff' }};">
                        <div style="font-weight: 600; color: #333;">
                            {{ $n->judul }}
                            @if(!$n->is_read)
                            <span style="color: #e53e3e; font-size: 10px; margin-left: 5px;" title="Baru">●</span>
                            @endif
                        </div>
                        <div style="color: #666; margin-top: 4px;">{{ $n->pesan }}</div>
                    </div>
                    @empty
                    <div style="padding: 15px; text-align: center; color: #999; font-size: 13px;">Tidak ada notifikasi</div>
                    @endforelse
                </div>

                <div style="padding: 10px; text-align: center; border-top: 1px solid #e2e8f0; background: #f8fafc; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                    <button onclick="tandaiSemuaDibaca(this)"
                        data-url="{{ route('nasabah.notifikasi.read') }}"
                        data-token="{{ csrf_token() }}"
                        style="background-color: #3182ce; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; width: 100%; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 5px; transition: 0.2s;">
                        <i class="fa-solid fa-check-double"></i> Tandai Sudah Dibaca
                    </button>
                </div>

            </div>
        </div>

        <div class="user-profile" id="profileDropdown">

            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>

            <div class="user-info">
                <span class="user-name">{{ Auth::user()->name }}</span>

                <span class="user-role">
                    @if(Auth::user()->role == 'superadmin')
                    Superadmin
                    @elseif(Auth::user()->role == 'admin')
                    Admin TU
                    @else
                    Nasabah
                    @endif
                </span>
            </div>

            <i class="fa-solid fa-chevron-down dropdown-icon"></i>

            <div class="profile-menu" id="dropdownMenu">
                <a href="{{ route('profile.index') }}">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Pengaturan Profile</span>
                </a>

                <a href="javascript:void(0);" class="logout-item" id="btn-logout-navbar">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Keluar Sistem</span>
                </a>
            </div>

            <form id="logout-form-action" action="/logout" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

    </div>
</header>