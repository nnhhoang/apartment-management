@extends('layouts.app')

@section('title', 'Chỉnh sửa hợp đồng')

@section('header', 'Chỉnh sửa hợp đồng')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Thông tin hợp đồng thuê</h6>
    </div>
    <div class="card-body">
        <form action="{{ url('/tenant_contracts/' . $tenantContract->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Phòng trọ</label>
                <input type="text" class="form-control" value="{{ $tenantContract->room->apartment->name }} - Phòng {{ $tenantContract->room->room_number }}" readonly>
                <input type="hidden" name="apartment_room_id" value="{{ $tenantContract->apartment_room_id }}">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Người thuê</label>
                <input type="text" class="form-control" value="{{ $tenantContract->tenant->name }} - {{ $tenantContract->tenant->tel }}" readonly>
                <input type="hidden" name="tenant_id" value="{{ $tenantContract->tenant_id }}">
            </div>
            
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Thông tin hợp đồng</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label">Giá thuê (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $tenantContract->price) }}" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="pay_period" class="form-label">Kỳ hạn thanh toán <span class="text-danger">*</span></label>
                            <select class="form-select @error('pay_period') is-invalid @enderror" id="pay_period" name="pay_period" required>
                                <option value="">-- Chọn kỳ hạn --</option>
                                <option value="1" {{ old('pay_period', $tenantContract->pay_period) == 1 ? 'selected' : '' }}>1 tháng</option>
                                <option value="3" {{ old('pay_period', $tenantContract->pay_period) == 3 ? 'selected' : '' }}>3 tháng</option>
                                <option value="6" {{ old('pay_period', $tenantContract->pay_period) == 6 ? 'selected' : '' }}>6 tháng</option>
                                <option value="12" {{ old('pay_period', $tenantContract->pay_period) == 12 ? 'selected' : '' }}>1 năm</option>
                            </select>
                            @error('pay_period')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control datepicker @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $tenantContract->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Ngày kết thúc</label>
                            <input type="text" class="form-control datepicker @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $tenantContract->end_date ? $tenantContract->end_date->format('Y-m-d') : '') }}">
                            <div class="form-text">Để trống nếu hợp đồng không có thời hạn cố định</div>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="number_of_tenant_current" class="form-label">Số người ở <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('number_of_tenant_current') is-invalid @enderror" id="number_of_tenant_current" name="number_of_tenant_current" value="{{ old('number_of_tenant_current', $tenantContract->number_of_tenant_current) }}" min="1" required>
                            @error('number_of_tenant_current')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Thông tin tiền điện</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="electricity_pay_type" class="form-label">Cách trả tiền điện <span class="text-danger">*</span></label>
                            <select class="form-select @error('electricity_pay_type') is-invalid @enderror" id="electricity_pay_type" name="electricity_pay_type" required>
                                <option value="">-- Chọn cách trả --</option>
                                <option value="1" {{ old('electricity_pay_type', $tenantContract->electricity_pay_type) == 1 ? 'selected' : '' }}>Trả theo đầu người</option>
                                <option value="2" {{ old('electricity_pay_type', $tenantContract->electricity_pay_type) == 2 ? 'selected' : '' }}>Trả cố định theo phòng</option>
                                <option value="3" {{ old('electricity_pay_type', $tenantContract->electricity_pay_type) == 3 ? 'selected' : '' }}>Trả theo số điện sử dụng</option>
                            </select>
                            @error('electricity_pay_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="electricity_price" class="form-label">Giá điện (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('electricity_price') is-invalid @enderror" id="electricity_price" name="electricity_price" value="{{ old('electricity_price', $tenantContract->electricity_price) }}" min="0" required>
                            @error('electricity_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="electricity_number_start" class="form-label">Số điện ban đầu <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('electricity_number_start') is-invalid @enderror" id="electricity_number_start" name="electricity_number_start" value="{{ old('electricity_number_start', $tenantContract->electricity_number_start) }}" min="0" required>
                            @error('electricity_number_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Thông tin tiền nước</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="water_pay_type" class="form-label">Cách trả tiền nước <span class="text-danger">*</span></label>
                            <select class="form-select @error('water_pay_type') is-invalid @enderror" id="water_pay_type" name="water_pay_type" required>
                                <option value="">-- Chọn cách trả --</option>
                                <option value="1" {{ old('water_pay_type', $tenantContract->water_pay_type) == 1 ? 'selected' : '' }}>Trả theo đầu người</option>
                                <option value="2" {{ old('water_pay_type', $tenantContract->water_pay_type) == 2 ? 'selected' : '' }}>Trả cố định theo phòng</option>
                                <option value="3" {{ old('water_pay_type', $tenantContract->water_pay_type) == 3 ? 'selected' : '' }}>Trả theo số nước sử dụng</option>
                            </select>
                            @error('water_pay_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="water_price" class="form-label">Giá nước (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('water_price') is-invalid @enderror" id="water_price" name="water_price" value="{{ old('water_price', $tenantContract->water_price) }}" min="0" required>
                            @error('water_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="water_number_start" class="form-label">Số nước ban đầu <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('water_number_start') is-invalid @enderror" id="water_number_start" name="water_number_start" value="{{ old('water_number_start', $tenantContract->water_number_start) }}" min="0" required>
                            @error('water_number_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="note" class="form-label">Ghi chú</label>
                <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3">{{ old('note', $tenantContract->note) }}</textarea>
                @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Lưu thay đổi
                </button>
                <a href="{{ url('/tenant_contracts/' . $tenantContract->id) }}" class="btn btn-secondary">
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