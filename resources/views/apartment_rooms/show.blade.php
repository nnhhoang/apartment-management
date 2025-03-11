@extends('layouts.app')

@section('title', 'Chi tiết phòng trọ')

@section('header', 'Chi tiết phòng trọ')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Thông tin phòng trọ</h6>
                <div>
                    <a href="{{ url('/apartment_rooms/' . $apartmentRoom->id . '/edit') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Sửa
                    </a>
                    @if(!$apartmentRoom->activeContract)
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i>Xóa
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($apartmentRoom->image)
                        <img src="{{ asset('storage/' . $apartmentRoom->image) }}" alt="{{ $apartmentRoom->room_number }}" class="img-fluid rounded" style="max-height: 200px;">
                    @else
                        <div class="bg-light rounded p-4 text-center text-muted">
                            <i class="fas fa-door-open fa-4x mb-2"></i>
                            <p>Không có ảnh</p>
                        </div>
                    @endif
                </div>
                
                <table class="table">
                    <tr>
                        <th style="width: 35%;">Số phòng:</th>
                        <td>{{ $apartmentRoom->room_number }}</td>
                    </tr>
                    <tr>
                        <th>Tòa nhà:</th>
                        <td>
                            <a href="{{ url('/apartments/' . $apartmentRoom->apartment->id) }}">
                                {{ $apartmentRoom->apartment->name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Địa chỉ:</th>
                        <td>{{ $apartmentRoom->apartment->address }}</td>
                    </tr>
                    <tr>
                        <th>Giá thuê mặc định:</th>
                        <td>{{ number_format($apartmentRoom->default_price, 0, ',', '.') }} VNĐ</td>
                    </tr>
                    <tr>
                        <th>Số người ở tối đa:</th>
                        <td>{{ $apartmentRoom->max_tenant ?? 'Không giới hạn' }}</td>
                    </tr>
                    <tr>
                        <th>Trạng thái:</th>
                        <td>
                            @if($apartmentRoom->hasActiveContract())
                                <span class="badge bg-success">Đã thuê</span>
                            @else
                                <span class="badge bg-warning">Trống</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo:</th>
                        <td>{{ $apartmentRoom->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-7 mb-4">
        @if($apartmentRoom->hasActiveContract())
            @php $activeContract = $apartmentRoom->getActiveContract(); @endphp
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Thông tin người thuê hiện tại</h6>
                    <div>
                        <a href="{{ url('/tenant_contracts/' . $activeContract->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-file-contract me-1"></i>Xem hợp đồng
                        </a>
                        <a href="{{ url('/room_fees/create?room_id=' . $apartmentRoom->id . '&contract_id=' . $activeContract->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-money-bill-wave me-1"></i>Tạo khoản thu
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 35%;">Họ tên:</th>
                            <td>
                                <a href="{{ url('/tenants/' . $activeContract->tenant->id) }}">
                                    {{ $activeContract->tenant->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Số điện thoại:</th>
                            <td>{{ $activeContract->tenant->tel }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $activeContract->tenant->email ?? 'Không có' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày bắt đầu hợp đồng:</th>
                            <td>{{ $activeContract->start_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái hợp đồng:</th>
                            <td>
                                @if($activeContract->end_date)
                                    @if($activeContract->end_date->isPast())
                                        <span class="badge bg-danger">Đã kết thúc</span>
                                    @else
                                        <span class="badge bg-warning">Có thời hạn</span>
                                    @endif
                                @else
                                    <span class="badge bg-success">Đang hiệu lực</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Giá thuê hiện tại:</th>
                            <td>{{ number_format($activeContract->price, 0, ',', '.') }} VNĐ</td>
                        </tr>
                        <tr>
                            <th>Số người ở hiện tại:</th>
                            <td>{{ $activeContract->number_of_tenant_current }} người</td>
                        </tr>
                    </table>
                </div>
            </div>
        @else
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Thông tin người thuê</h6>
                </div>
                <div class="card-body">
                    <div class="text-center p-4">
                        <i class="fas fa-user-slash fa-4x mb-3 text-muted"></i>
                        <p class="mb-3">Phòng này hiện đang trống</p>
                        <a href="{{ url('/tenant_contracts/create?room_id=' . $apartmentRoom->id) }}" class="btn btn-success">
                            <i class="fas fa-file-contract me-1"></i>Tạo hợp đồng mới
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Lịch sử thu tiền</h6>
                @if($apartmentRoom->activeContract && (!$apartmentRoom->activeContract->end_date || !$apartmentRoom->activeContract->end_date->isPast()))
                <a href="{{ url('/room_fees/create?room_id=' . $apartmentRoom->id . '&contract_id=' . $apartmentRoom->activeContract->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Tạo khoản thu mới
                </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Tháng thu</th>
                                <th>Tổng tiền</th>
                                <th>Đã thanh toán</th>
                                <th>Còn nợ</th>
                                <th>Ngày thanh toán</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apartmentRoom->feeCollections->sortByDesc('charge_date') as $feeCollection)
                            <tr>
                                <td>{{ $feeCollection->charge_date->format('m/Y') }}</td>
                                <td>{{ number_format($feeCollection->total_price, 0, ',', '.') }} VNĐ</td>
                                <td>{{ number_format($feeCollection->total_paid, 0, ',', '.') }} VNĐ</td>
                                <td>
                                    @if($feeCollection->total_paid < $feeCollection->total_price)
                                    <span class="text-danger">{{ number_format($feeCollection->total_price - $feeCollection->total_paid, 0, ',', '.') }} VNĐ</span>
                                    @else
                                    <span class="text-success">0 VNĐ</span>
                                    @endif
                                </td>
                                <td>
                                    @if($feeCollection->histories->count() > 0)
                                    {{ $feeCollection->histories->sortByDesc('paid_date')->first()->paid_date->format('d/m/Y') }}
                                    @else
                                    <span class="text-muted">Chưa thanh toán</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('/room_fees/' . $feeCollection->id) }}" class="btn btn-sm btn-info">
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
@if(!$apartmentRoom->activeContract)
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa phòng <strong>{{ $apartmentRoom->room_number }}</strong> của tòa nhà <strong>{{ $apartmentRoom->apartment->name }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ url('/apartment_rooms/' . $apartmentRoom->id) }}" method="POST">
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