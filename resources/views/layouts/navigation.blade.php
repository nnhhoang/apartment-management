<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/home') }}">
            <i class="fas fa-home me-2"></i>{{ config('app.name', 'Quản lý nhà trọ') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ url('/home') }}">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('apartments*') ? 'active' : '' }}" href="#" id="apartmentsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-building me-1"></i>Tòa nhà
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="apartmentsDropdown">
                        <li><a class="dropdown-item" href="{{ url('/apartments') }}">Danh sách tòa nhà</a></li>
                        <li><a class="dropdown-item" href="{{ url('/apartments/create') }}">Thêm tòa nhà mới</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('apartment_rooms*') ? 'active' : '' }}" href="#" id="roomsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-door-open me-1"></i>Phòng trọ
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="roomsDropdown">
                        <li><a class="dropdown-item" href="{{ url('/apartment_rooms') }}">Danh sách phòng</a></li>
                        <li><a class="dropdown-item" href="{{ url('/apartment_rooms/create') }}">Thêm phòng mới</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('tenants*') || request()->is('tenant_contracts*') ? 'active' : '' }}" href="#" id="tenantsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-users me-1"></i>Người thuê & Hợp đồng
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="tenantsDropdown">
                        <li><a class="dropdown-item" href="{{ url('/tenants') }}">Danh sách người thuê</a></li>
                        <li><a class="dropdown-item" href="{{ url('/tenants/create') }}">Thêm người thuê mới</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ url('/tenant_contracts') }}">Danh sách hợp đồng</a></li>
                        <li><a class="dropdown-item" href="{{ url('/tenant_contracts/create') }}">Tạo hợp đồng mới</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('room_fees*') ? 'active' : '' }}" href="#" id="feesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-money-bill-wave me-1"></i>Tiền trọ
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="feesDropdown">
                        <li><a class="dropdown-item" href="{{ url('/room_fees') }}">Danh sách thu tiền</a></li>
                        <li><a class="dropdown-item" href="{{ url('/room_fees/create') }}">Tạo khoản thu mới</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('statistics*') ? 'active' : '' }}" href="#" id="statsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-chart-line me-1"></i>Thống kê
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="statsDropdown">
                        <li><a class="dropdown-item" href="{{ url('/statistics') }}">Tổng quan</a></li>
                        <li><a class="dropdown-item" href="{{ url('/statistics/unpaid') }}">Phòng chưa thanh toán</a></li>
                        <li><a class="dropdown-item" href="{{ url('/statistics/apartments') }}">Thống kê theo tòa nhà</a></li>
                        <li><a class="dropdown-item" href="{{ url('/statistics/rooms') }}">Thống kê theo phòng</a></li>
                    </ul>
                </li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        {{-- <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Thông tin cá nhân</a></li> --}}
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>