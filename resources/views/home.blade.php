@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Tòa nhà</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalApartments }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Tổng số phòng</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalRooms }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-door-open fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">Phòng đã thuê</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $occupiedRooms }} / {{ $totalRooms }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Phòng trống</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $vacantRooms }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-door-closed fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Doanh thu tháng này</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($currentMonthIncome, 0, ',', '.') }} VNĐ</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Doanh thu dự kiến/tháng</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($expectedIncome, 0, ',', '.') }} VNĐ</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Tổng nợ chưa thu</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($totalDebt, 0, ',', '.') }} VNĐ</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Danh sách tòa nhà</h6>
                <a href="{{ url('/apartments/create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tên tòa nhà</th>
                                <th>Địa chỉ</th>
                                <th>Số phòng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apartments as $apartment)
                            <tr>
                                <td>{{ $apartment->name }}</td>
                                <td>{{ $apartment->address }}</td>
                                <td>{{ $apartment->rooms_count }}</td>
                                <td>
                                    <a href="{{ url('/apartments/' . $apartment->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Phòng chưa thanh toán đủ</h6>
                <a href="{{ url('/statistics/unpaid') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> Xem tất cả
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Phòng</th>
                                <th>Tòa nhà</th>
                                <th>Ngày thu</th>
                                <th>Còn nợ</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unpaidRooms as $room)
                            <tr>
                                <td>{{ $room->room->room_number }}</td>
                                <td>{{ $room->room->apartment->name }}</td>
                                <td>{{ $room->charge_date->format('d/m/Y') }}</td>
                                <td>{{ number_format($room->total_price - $room->total_paid, 0, ',', '.') }} VNĐ</td>
                                <td>
                                    <a href="{{ url('/room_fees/' . $room->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection