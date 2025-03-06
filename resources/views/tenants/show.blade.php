@extends('layouts.app')

@section('title', 'Chi tiết người thuê')

@section('header', 'Chi tiết người thuê')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Thông tin người thuê</h6>
                <div>
                    <a href="{{ url('/tenants/' . $tenant->id . '/edit') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Sửa
                    </a>
                    @if($tenant->contracts->isEmpty())
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i>Xóa
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 30%;">Họ tên:</th>
                        <td>{{ $tenant->name }}</td>
                    </tr>
                    <tr>
                        <th>Số điện thoại:</th>
                        <td>{{ $tenant->tel }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $tenant->email ?? 'Không có' }}</td>
                    </tr>
                    <tr>
                        <th>Số CMND/CCCD:</th>
                        <td>{{ $tenant->identity_card_number ?? 'Không có' }}</td>
                    </tr>
                    <tr>
                        <th>Ngày tạo:</th>
                        <td>{{ $tenant->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Danh sách hợp đồng</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Phòng</th>
                                <th>Tòa nhà</th>
                                <th>Giá thuê</th>
                                <th>Ngày bắt đầu</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenant->contracts as $contract)
                                <tr>
                                    <td>{{ $contract->room->room_number }}</td>
                                    <td>{{ $contract->room->apartment->name }}</td>
                                    <td>{{ number_format($contract->price, 0, ',', '.') }} VNĐ</td>
                                    <td>{{ $contract->start_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($contract->end_date)
                                            <span class="badge bg-danger">Đã kết thúc</span>
                                        @else
                                            <span class="badge bg-success">Đang hiệu lực</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('/tenant_contracts/' . $contract->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có hợp đồng nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Lịch sử thu tiền gần đây</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Phòng</th>
                                <th>Tháng</th>
                                <th>Tổng tiền</th>
                                <th>Đã thanh toán</th>
                                <th>Còn nợ</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenant->feeCollections->take(5) as $fee)
                                <tr>
                                    <td>{{ $fee->room->room_number }} - {{ $fee->room->apartment->name }}</td>
                                    <td>{{ $fee->charge_date->format('m/Y') }}</td>
                                    <td>{{ number_format($fee->total_price, 0, ',', '.') }} VNĐ</td>
                                    <td>{{ number_format($fee->total_paid, 0, ',', '.') }} VNĐ</td>
                                    <td>
                                        @if($fee->total_paid < $fee->total_price)
                                            <span class="text-danger">{{ number_format($fee->total_price - $fee->total_paid, 0, ',', '.') }} VNĐ</span>
                                        @else
                                            <span class="text-success">0 VNĐ</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('/room_fees/' . $fee->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có khoản thu nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
@if($tenant->contracts->isEmpty())
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa người thuê <strong>{{ $tenant->name }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ url('/tenants/' . $tenant->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection