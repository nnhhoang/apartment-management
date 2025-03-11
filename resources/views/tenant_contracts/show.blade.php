@extends('layouts.app')

@section('title', 'Chi tiết hợp đồng')

@section('header', 'Chi tiết hợp đồng')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Thông tin hợp đồng</h6>
                <div>
                    <a href="{{ url('/tenant_contracts/' . $tenantContract->id . '/edit') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Sửa
                    </a>
                    @if($tenantContract->feeCollections->isEmpty())
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i>Xóa
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 40%;">Phòng:</th>
                        <td>
                            <a href="{{ url('/apartment_rooms/' . $tenantContract->room->id) }}">
                                {{ $tenantContract->room->apartment->name }} - Phòng {{ $tenantContract->room->room_number }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Người thuê:</th>
                        <td>
                            <a href="{{ url('/tenants/' . $tenantContract->tenant->id) }}">
                                {{ $tenantContract->tenant->name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Số điện thoại:</th>
                        <td>{{ $tenantContract->tenant->tel }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $tenantContract->tenant->email ?? 'Không có' }}</td>
                    </tr>
                    <tr>
                        <th>Số CMND/CCCD:</th>
                        <td>{{ $tenantContract->tenant->identity_card_number ?? 'Không có' }}</td>
                    </tr>
                    <tr>
                        <th>Giá thuê:</th>
                        <td>{{ number_format($tenantContract->price, 0, ',', '.') }} VNĐ</td>
                    </tr>
                    <tr>
                        <th>Kỳ hạn thanh toán:</th>
                        <td>
                            @if($tenantContract->pay_period == 1)
                                1 tháng
                            @elseif($tenantContract->pay_period == 3)
                                3 tháng
                            @elseif($tenantContract->pay_period == 6)
                                6 tháng
                            @elseif($tenantContract->pay_period == 12)
                                1 năm
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày bắt đầu:</th>
                        <td>{{ $tenantContract->start_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Ngày kết thúc:</th>
                        <td>
                            @if($tenantContract->end_date)
                                {{ $tenantContract->end_date->format('d/m/Y') }}
                                @if($tenantContract->end_date->isPast())
                                    <span class="badge bg-danger">Đã kết thúc</span>
                                @else
                                    <span class="badge bg-warning">Có thời hạn</span>
                                @endif
                            @else
                                <span class="badge bg-success">Đang hiệu lực</span>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" data-bs-toggle="modal" data-bs-target="#endContractModal">
                                    Kết thúc
                                </button>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Số người ở:</th>
                        <td>{{ $tenantContract->number_of_tenant_current }} người</td>
                    </tr>
                    <tr>
                        <th>Ngày tạo:</th>
                        <td>{{ $tenantContract->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Thông tin tiền điện, nước</h6>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 40%;">Cách trả tiền điện:</th>
                        <td>
                            @if($tenantContract->electricity_pay_type == 1)
                                Trả theo đầu người
                            @elseif($tenantContract->electricity_pay_type == 2)
                                Trả cố định theo phòng
                            @elseif($tenantContract->electricity_pay_type == 3)
                                Trả theo số điện sử dụng
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Giá điện:</th>
                        <td>{{ number_format($tenantContract->electricity_price, 0, ',', '.') }} VNĐ</td>
                    </tr>
                    <tr>
                        <th>Số điện ban đầu:</th>
                        <td>{{ $tenantContract->electricity_number_start }}</td>
                    </tr>
                    <tr>
                        <th>Cách trả tiền nước:</th>
                        <td>
                            @if($tenantContract->water_pay_type == 1)
                                Trả theo đầu người
                            @elseif($tenantContract->water_pay_type == 2)
                                Trả cố định theo phòng
                            @elseif($tenantContract->water_pay_type == 3)
                                Trả theo số nước sử dụng
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Giá nước:</th>
                        <td>{{ number_format($tenantContract->water_price, 0, ',', '.') }} VNĐ</td>
                    </tr>
                    <tr>
                        <th>Số nước ban đầu:</th>
                        <td>{{ $tenantContract->water_number_start }}</td>
                    </tr>
                </table>
                
                @if($tenantContract->note)
                <div class="mt-3">
                    <h6 class="fw-bold">Ghi chú:</h6>
                    <p>{{ $tenantContract->note }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Lịch sử thu tiền</h6>
                @if(!$tenantContract->end_date || !$tenantContract->end_date->isPast())
                <a href="{{ url('/room_fees/create?room_id=' . $tenantContract->room->id . '&contract_id=' . $tenantContract->id) }}" class="btn btn-primary btn-sm">
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
                            @forelse($tenantContract->feeCollections as $feeCollection)
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
        
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Tính tiền hằng tháng</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Khoản mục</th>
                                <th>Chi tiết tính toán</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tiền thuê phòng</td>
                                <td>{{ number_format($tenantContract->price, 0, ',', '.') }} VNĐ</td>
                                <td>{{ number_format($tenantContract->price, 0, ',', '.') }} VNĐ</td>
                            </tr>
                            <tr>
                                <td>Tiền điện</td>
                                <td>
                                    @if($tenantContract->electricity_pay_type == 1)
                                        {{ number_format($tenantContract->electricity_price, 0, ',', '.') }} VNĐ × {{ $tenantContract->number_of_tenant_current }} người
                                    @elseif($tenantContract->electricity_pay_type == 2)
                                        {{ number_format($tenantContract->electricity_price, 0, ',', '.') }} VNĐ (cố định)
                                    @elseif($tenantContract->electricity_pay_type == 3)
                                        {{ number_format($tenantContract->electricity_price, 0, ',', '.') }} VNĐ/số × [Số điện sử dụng]
                                    @endif
                                </td>
                                <td>
                                    @if($tenantContract->electricity_pay_type == 1)
                                        {{ number_format($tenantContract->electricity_price * $tenantContract->number_of_tenant_current, 0, ',', '.') }} VNĐ
                                    @elseif($tenantContract->electricity_pay_type == 2)
                                        {{ number_format($tenantContract->electricity_price, 0, ',', '.') }} VNĐ
                                    @else
                                        <em>Tùy theo số điện sử dụng</em>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Tiền nước</td>
                                <td>
                                    @if($tenantContract->water_pay_type == 1)
                                        {{ number_format($tenantContract->water_price, 0, ',', '.') }} VNĐ × {{ $tenantContract->number_of_tenant_current }} người
                                    @elseif($tenantContract->water_pay_type == 2)
                                        {{ number_format($tenantContract->water_price, 0, ',', '.') }} VNĐ (cố định)
                                    @elseif($tenantContract->water_pay_type == 3)
                                        {{ number_format($tenantContract->water_price, 0, ',', '.') }} VNĐ/số × [Số nước sử dụng]
                                    @endif
                                </td>
                                <td>
                                    @if($tenantContract->water_pay_type == 1)
                                        {{ number_format($tenantContract->water_price * $tenantContract->number_of_tenant_current, 0, ',', '.') }} VNĐ
                                    @elseif($tenantContract->water_pay_type == 2)
                                        {{ number_format($tenantContract->water_price, 0, ',', '.') }} VNĐ
                                    @else
                                        <em>Tùy theo số nước sử dụng</em>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-light fw-bold">
                                <td>Tổng cộng (tối thiểu)</td>
                                <td></td>
                                <td>
                                    @php
                                        $minTotal = $tenantContract->price;
                                        
                                        if ($tenantContract->electricity_pay_type == 1) {
                                            $minTotal += $tenantContract->electricity_price * $tenantContract->number_of_tenant_current;
                                        } elseif ($tenantContract->electricity_pay_type == 2) {
                                            $minTotal += $tenantContract->electricity_price;
                                        }
                                        
                                        if ($tenantContract->water_pay_type == 1) {
                                            $minTotal += $tenantContract->water_price * $tenantContract->number_of_tenant_current;
                                        } elseif ($tenantContract->water_pay_type == 2) {
                                            $minTotal += $tenantContract->water_price;
                                        }
                                    @endphp
                                    {{ number_format($minTotal, 0, ',', '.') }} VNĐ
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i> 
                    Số tiền trên là ước tính tối thiểu. Tiền điện, nước sẽ được tính chính xác theo thực tế sử dụng khi tạo khoản thu.
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end mt-4">
            <a href="{{ url('/tenant_contracts/' . $tenantContract->id . '/ai-assistant') }}" class="btn btn-outline-primary">
                <i class="fas fa-robot me-2"></i>Trợ lý AI
            </a>
        </div>
    </div>
</div>

<!-- Delete Modal -->
@if($tenantContract->feeCollections->isEmpty())
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa hợp đồng của <strong>{{ $tenantContract->tenant->name }}</strong> cho phòng <strong>{{ $tenantContract->room->room_number }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ url('/tenant_contracts/' . $tenantContract->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- End Contract Modal -->
@if(!$tenantContract->end_date)
<div class="modal fade" id="endContractModal" tabindex="-1" aria-labelledby="endContractModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="endContractModalLabel">Kết thúc hợp đồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/tenant_contracts/' . $tenantContract->id . '/end') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Bạn đang kết thúc hợp đồng của <strong>{{ $tenantContract->tenant->name }}</strong> cho phòng <strong>{{ $tenantContract->room->room_number }}</strong>.</p>
                    
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" id="end_date" name="end_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="end_reason" class="form-label">Lý do kết thúc</label>
                        <textarea class="form-control" id="end_reason" name="end_reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Kết thúc hợp đồng</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize datepickers
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });
</script>
@endsection