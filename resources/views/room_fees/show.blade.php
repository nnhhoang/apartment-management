@extends('layouts.app')

@section('title', 'Chi tiết khoản thu')

@section('header', 'Chi tiết khoản thu')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Thông tin khoản thu</h6>
                <div>
                    <a href="{{ url('/room_fees/' . $roomFeeCollection->id . '/edit') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Sửa
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i>Xóa
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 40%;">Phòng trọ:</th>
                        <td>
                            <a href="{{ url('/apartment_rooms/' . $roomFeeCollection->room->id) }}">
                                {{ $roomFeeCollection->room->apartment->name }} - Phòng {{ $roomFeeCollection->room->room_number }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Người thuê:</th>
                        <td>
                            <a href="{{ url('/tenants/' . $roomFeeCollection->tenant->id) }}">
                                {{ $roomFeeCollection->tenant->name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Tháng thu:</th>
                        <td>{{ $roomFeeCollection->charge_date->format('m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Ngày tạo:</th>
                        <td>{{ $roomFeeCollection->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Thông tin điện, nước</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Thông tin điện:</h6>
                        <table class="table">
                            <tr>
                                <th>Số điện đầu kỳ:</th>
                                <td>{{ $roomFeeCollection->electricity_number_before }}</td>
                            </tr>
                            <tr>
                                <th>Số điện cuối kỳ:</th>
                                <td>{{ $roomFeeCollection->electricity_number_after }}</td>
                            </tr>
                            <tr>
                                <th>Số điện sử dụng:</th>
                                <td>{{ $roomFeeCollection->electricity_number_after - $roomFeeCollection->electricity_number_before }}</td>
                            </tr>
                        </table>
                        
                        @if($roomFeeCollection->electricity_image)
                        <div class="mt-2">
                            <h6 class="fw-bold">Ảnh đồng hồ điện:</h6>
                            <img src="{{ asset('storage/' . $roomFeeCollection->electricity_image) }}" alt="Ảnh đồng hồ điện" class="img-fluid img-thumbnail" style="max-height: 200px;">
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold">Thông tin nước:</h6>
                        <table class="table">
                            <tr>
                                <th>Số nước đầu kỳ:</th>
                                <td>{{ $roomFeeCollection->water_number_before }}</td>
                            </tr>
                            <tr>
                                <th>Số nước cuối kỳ:</th>
                                <td>{{ $roomFeeCollection->water_number_after }}</td>
                            </tr>
                            <tr>
                                <th>Số nước sử dụng:</th>
                                <td>{{ $roomFeeCollection->water_number_after - $roomFeeCollection->water_number_before }}</td>
                            </tr>
                        </table>
                        
                        @if($roomFeeCollection->water_image)
                        <div class="mt-2">
                            <h6 class="fw-bold">Ảnh đồng hồ nước:</h6>
                            <img src="{{ asset('storage/' . $roomFeeCollection->water_image) }}" alt="Ảnh đồng hồ nước" class="img-fluid img-thumbnail" style="max-height: 200px;">
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Chi tiết thanh toán</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="bg-light rounded p-3 h-100">
                            <h6 class="fw-bold">Tổng tiền:</h6>
                            <h4 class="text-primary">{{ number_format($roomFeeCollection->total_price, 0, ',', '.') }} VNĐ</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded p-3 h-100">
                            <h6 class="fw-bold">Đã thanh toán:</h6>
                            <h4 class="text-success">{{ number_format($roomFeeCollection->total_paid, 0, ',', '.') }} VNĐ</h4>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="bg-light rounded p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="fw-bold">Còn nợ:</h6>
                            <h5 class="{{ $roomFeeCollection->total_price > $roomFeeCollection->total_paid ? 'text-danger' : 'text-success' }}">
                                {{ number_format(max(0, $roomFeeCollection->total_price - $roomFeeCollection->total_paid), 0, ',', '.') }} VNĐ
                            </h5>
                        </div>
                        <div class="progress">
                            @php
                                $percentage = min(100, round(($roomFeeCollection->total_paid / $roomFeeCollection->total_price) * 100));
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">{{ $percentage }}%</div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">Lịch sử thanh toán</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Ngày thanh toán</th>
                                        <th>Số tiền (VNĐ)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roomFeeCollection->histories as $history)
                                    <tr>
                                        <td>{{ $history->paid_date->format('d/m/Y') }}</td>
                                        <td>{{ number_format($history->price, 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center">Chưa có lịch sử thanh toán</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                @if($roomFeeCollection->total_price > $roomFeeCollection->total_paid)
                <div class="card">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">Thêm khoản thanh toán</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('/room_fees/' . $roomFeeCollection->id . '/payment') }}" method="POST">
                            @csrf
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_date" class="form-label">Ngày thanh toán <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datepicker @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                    @error('payment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="payment_amount" class="form-label">Số tiền thanh toán (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('payment_amount') is-invalid @enderror" id="payment_amount" name="payment_amount" value="{{ max(0, $roomFeeCollection->total_price - $roomFeeCollection->total_paid) }}" min="1" required>
                                    @error('payment_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-money-bill-wave me-1"></i>Thêm thanh toán
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa khoản thu này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ url('/room_fees/' . $roomFeeCollection->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
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