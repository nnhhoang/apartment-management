@extends('layouts.app')

@section('title', 'Chỉnh sửa khoản thu')

@section('header', 'Chỉnh sửa khoản thu')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Thông tin khoản thu</h6>
    </div>
    <div class="card-body">
        <form action="{{ url('/room_fees/' . $roomFeeCollection->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="tenant_contract_id" value="{{ $roomFeeCollection->tenant_contract_id }}">
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Phòng trọ</label>
                    <input type="text" class="form-control" value="{{ $roomFeeCollection->room->apartment->name }} - Phòng {{ $roomFeeCollection->room->room_number }}" readonly>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Người thuê</label>
                    <input type="text" class="form-control" value="{{ $roomFeeCollection->tenant->name }}" readonly>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Thông tin số điện</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="electricity_number_before" class="form-label">Số điện đầu kỳ <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('electricity_number_before') is-invalid @enderror" id="electricity_number_before" name="electricity_number_before" value="{{ old('electricity_number_before', $roomFeeCollection->electricity_number_before) }}" min="0" required>
                            @error('electricity_number_before')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="electricity_number_after" class="form-label">Số điện cuối kỳ <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('electricity_number_after') is-invalid @enderror" id="electricity_number_after" name="electricity_number_after" value="{{ old('electricity_number_after', $roomFeeCollection->electricity_number_after) }}" min="0" required>
                            @error('electricity_number_after')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="electricity_image" class="form-label">Ảnh đồng hồ điện</label>
                        <input type="file" class="form-control @error('electricity_image') is-invalid @enderror" id="electricity_image" name="electricity_image">
                        <div class="form-text">Định dạng: jpg, jpeg, png. Dung lượng tối đa: 2MB</div>
                        @error('electricity_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($roomFeeCollection->electricity_image)
                            <div class="mt-2">
                                <label class="form-label">Ảnh hiện tại:</label>
                                <div>
                                    <img src="{{ asset('storage/' . $roomFeeCollection->electricity_image) }}" alt="Ảnh đồng hồ điện" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Thông tin số nước</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="water_number_before" class="form-label">Số nước đầu kỳ <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('water_number_before') is-invalid @enderror" id="water_number_before" name="water_number_before" value="{{ old('water_number_before', $roomFeeCollection->water_number_before) }}" min="0" required>
                            @error('water_number_before')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="water_number_after" class="form-label">Số nước cuối kỳ <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('water_number_after') is-invalid @enderror" id="water_number_after" name="water_number_after" value="{{ old('water_number_after', $roomFeeCollection->water_number_after) }}" min="0" required>
                            @error('water_number_after')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="water_image" class="form-label">Ảnh đồng hồ nước</label>
                        <input type="file" class="form-control @error('water_image') is-invalid @enderror" id="water_image" name="water_image">
                        <div class="form-text">Định dạng: jpg, jpeg, png. Dung lượng tối đa: 2MB</div>
                        @error('water_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($roomFeeCollection->water_image)
                            <div class="mt-2">
                                <label class="form-label">Ảnh hiện tại:</label>
                                <div>
                                    <img src="{{ asset('storage/' . $roomFeeCollection->water_image) }}" alt="Ảnh đồng hồ nước" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Thông tin thanh toán</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="charge_date" class="form-label">Ngày thu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control datepicker @error('charge_date') is-invalid @enderror" id="charge_date" name="charge_date" value="{{ old('charge_date', $roomFeeCollection->charge_date->format('Y-m-d')) }}" required>
                            @error('charge_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="total_paid" class="form-label">Số tiền đã thanh toán (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('total_paid') is-invalid @enderror" id="total_paid" name="total_paid" value="{{ old('total_paid', $roomFeeCollection->total_paid) }}" min="0" required>
                            @error('total_paid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Thông tin về lịch sử thanh toán sẽ được cập nhật dựa trên số tiền đã thanh toán. Nếu bạn thay đổi số tiền đã thanh toán, lịch sử thanh toán cũ sẽ bị xóa và tạo lại.
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Lưu thay đổi
                </button>
                <a href="{{ url('/room_fees/' . $roomFeeCollection->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </form>
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