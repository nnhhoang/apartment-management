@extends('layouts.app')

@section('title', 'Tạo hợp đồng mới')

@section('header', 'Tạo hợp đồng mới')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Thông tin hợp đồng thuê</h6>
    </div>
    <div class="card-body">
        <form action="{{ url('/tenant_contracts') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="apartment_room_id" class="form-label">Phòng trọ <span class="text-danger">*</span></label>
                <select class="form-select @error('apartment_room_id') is-invalid @enderror" id="apartment_room_id" name="apartment_room_id" required>
                    <option value="">-- Chọn phòng trọ --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ (old('apartment_room_id', $selectedRoomId) == $room->id) ? 'selected' : '' }}
                           data-price="{{ $room->default_price }}">
                            {{ $room->apartment->name }} - Phòng {{ $room->room_number }} ({{ number_format($room->default_price, 0, ',', '.') }} VNĐ)
                        </option>
                    @endforeach
                </select>
                @error('apartment_room_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Thông tin người thuê</h6>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="new_tenant" name="new_tenant" value="1" {{ old('new_tenant') ? 'checked' : '' }}>
                        <label class="form-check-label" for="new_tenant">
                            Tạo người thuê mới
                        </label>
                    </div>
                    
                    <div id="existing_tenant_section" class="mb-3 {{ old('new_tenant') ? 'd-none' : '' }}">
                        <label for="tenant_id" class="form-label">Chọn người thuê <span class="text-danger">*</span></label>
                        <select class="form-select @error('tenant_id') is-invalid @enderror" id="tenant_id" name="tenant_id" data-old-value="{{ old('tenant_id', $selectedTenantId ?? '') }}" {{ old('new_tenant') ? '' : 'required' }}>
                            <option value="">-- Chọn người thuê --</option>
                            <!-- Danh sách người thuê sẽ được thêm qua Ajax -->
                        </select>
                        @error('tenant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div id="new_tenant_section" class="{{ old('new_tenant') ? '' : 'd-none' }}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tenant_name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('tenant_name') is-invalid @enderror" id="tenant_name" name="tenant_name" value="{{ old('tenant_name') }}" {{ old('new_tenant') ? 'required' : '' }}>
                                @error('tenant_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tenant_tel" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('tenant_tel') is-invalid @enderror" id="tenant_tel" name="tenant_tel" value="{{ old('tenant_tel') }}" {{ old('new_tenant') ? 'required' : '' }}>
                                @error('tenant_tel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tenant_email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('tenant_email') is-invalid @enderror" id="tenant_email" name="tenant_email" value="{{ old('tenant_email') }}">
                                @error('tenant_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tenant_identity_card_number" class="form-label">Số CMND/CCCD</label>
                                <input type="text" class="form-control @error('tenant_identity_card_number') is-invalid @enderror" id="tenant_identity_card_number" name="tenant_identity_card_number" value="{{ old('tenant_identity_card_number') }}">
                                @error('tenant_identity_card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Thông tin hợp đồng</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label">Giá thuê (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="pay_period" class="form-label">Kỳ hạn thanh toán <span class="text-danger">*</span></label>
                            <select class="form-select @error('pay_period') is-invalid @enderror" id="pay_period" name="pay_period" required>
                                <option value="">-- Chọn kỳ hạn --</option>
                                <option value="1" {{ old('pay_period') == 1 ? 'selected' : '' }}>1 tháng</option>
                                <option value="3" {{ old('pay_period') == 3 ? 'selected' : '' }}>3 tháng</option>
                                <option value="6" {{ old('pay_period') == 6 ? 'selected' : '' }}>6 tháng</option>
                                <option value="12" {{ old('pay_period') == 12 ? 'selected' : '' }}>1 năm</option>
                            </select>
                            @error('pay_period')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control datepicker @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Ngày kết thúc</label>
                            <input type="text" class="form-control datepicker @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
                            <div class="form-text">Để trống nếu hợp đồng không có thời hạn cố định</div>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="number_of_tenant_current" class="form-label">Số người ở <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('number_of_tenant_current') is-invalid @enderror" id="number_of_tenant_current" name="number_of_tenant_current" value="{{ old('number_of_tenant_current', 1) }}" min="1" required>
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
                                <option value="1" {{ old('electricity_pay_type') == 1 ? 'selected' : '' }}>Trả theo đầu người</option>
                                <option value="2" {{ old('electricity_pay_type') == 2 ? 'selected' : '' }}>Trả cố định theo phòng</option>
                                <option value="3" {{ old('electricity_pay_type') == 3 ? 'selected' : '' }}>Trả theo số điện sử dụng</option>
                            </select>
                            @error('electricity_pay_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="electricity_price" class="form-label">Giá điện (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('electricity_price') is-invalid @enderror" id="electricity_price" name="electricity_price" value="{{ old('electricity_price') }}" min="0" required>
                            @error('electricity_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="electricity_number_start" class="form-label">Số điện ban đầu <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('electricity_number_start') is-invalid @enderror" id="electricity_number_start" name="electricity_number_start" value="{{ old('electricity_number_start', 0) }}" min="0" required>
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
                                <option value="1" {{ old('water_pay_type') == 1 ? 'selected' : '' }}>Trả theo đầu người</option>
                                <option value="2" {{ old('water_pay_type') == 2 ? 'selected' : '' }}>Trả cố định theo phòng</option>
                                <option value="3" {{ old('water_pay_type') == 3 ? 'selected' : '' }}>Trả theo số nước sử dụng</option>
                            </select>
                            @error('water_pay_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="water_price" class="form-label">Giá nước (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('water_price') is-invalid @enderror" id="water_price" name="water_price" value="{{ old('water_price') }}" min="0" required>
                            @error('water_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="water_number_start" class="form-label">Số nước ban đầu <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('water_number_start') is-invalid @enderror" id="water_number_start" name="water_number_start" value="{{ old('water_number_start', 0) }}" min="0" required>
                            @error('water_number_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="note" class="form-label">Ghi chú</label>
                <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Tạo hợp đồng
                </button>
                <a href="{{ url('/tenant_contracts') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize datepickers
    flatpickr(".datepicker", {
        dateFormat: "Y-m-d",
        allowInput: true
    });
    
    // Load tenant list via AJAX
    loadTenants();
    
    // Toggle new tenant form
    const newTenantCheckbox = document.getElementById('new_tenant');
    const existingTenantSection = document.getElementById('existing_tenant_section');
    const newTenantSection = document.getElementById('new_tenant_section');
    const tenantIdSelect = document.getElementById('tenant_id');
    const tenantNameInput = document.getElementById('tenant_name');
    const tenantTelInput = document.getElementById('tenant_tel');
    
    if (newTenantCheckbox) {
        newTenantCheckbox.addEventListener('change', function() {
            if (this.checked) {
                existingTenantSection.classList.add('d-none');
                newTenantSection.classList.remove('d-none');
                tenantIdSelect.removeAttribute('required');
                tenantNameInput.setAttribute('required', 'required');
                tenantTelInput.setAttribute('required', 'required');
            } else {
                existingTenantSection.classList.remove('d-none');
                newTenantSection.classList.add('d-none');
                tenantIdSelect.setAttribute('required', 'required');
                tenantNameInput.removeAttribute('required');
                tenantTelInput.removeAttribute('required');
            }
        });
    }
    
    // Load room price when room is selected
    const roomSelect = document.getElementById('apartment_room_id');
    const priceInput = document.getElementById('price');
    
    if (roomSelect && priceInput) {
        roomSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                if (price) {
                    priceInput.value = price;
                }
            }
        });
        
        // Trigger room change if a room is selected
        if (roomSelect.value) {
            roomSelect.dispatchEvent(new Event('change'));
        }
    }

    // Tự động tính ngày kết thúc dựa trên kỳ hạn thanh toán và ngày bắt đầu
    const payPeriodSelect = document.getElementById('pay_period');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (payPeriodSelect && startDateInput && endDateInput) {
        // Cập nhật ngày kết thúc khi kỳ hạn thanh toán hoặc ngày bắt đầu thay đổi
        const updateEndDate = function() {
            const payPeriod = parseInt(payPeriodSelect.value);
            const startDate = startDateInput.value;
            
            if (payPeriod && startDate) {
                // Tính toán ngày kết thúc dựa trên kỳ hạn thanh toán
                const startDateObj = new Date(startDate);
                startDateObj.setMonth(startDateObj.getMonth() + payPeriod);
                
                // Format lại để phù hợp với định dạng YYYY-MM-DD
                const year = startDateObj.getFullYear();
                const month = String(startDateObj.getMonth() + 1).padStart(2, '0');
                const day = String(startDateObj.getDate()).padStart(2, '0');
                const formattedDate = `${year}-${month}-${day}`;
                
                // Cập nhật giá trị trong input field
                const endDateFlatpickr = endDateInput._flatpickr;
                if (endDateFlatpickr) {
                    endDateFlatpickr.setDate(formattedDate);
                } else {
                    endDateInput.value = formattedDate;
                }
            } else {
                // Nếu không có kỳ hạn hoặc ngày bắt đầu, xóa giá trị ngày kết thúc
                const endDateFlatpickr = endDateInput._flatpickr;
                if (endDateFlatpickr) {
                    endDateFlatpickr.clear();
                } else {
                    endDateInput.value = '';
                }
            }
        };
        
        // Thêm sự kiện listener
        payPeriodSelect.addEventListener('change', updateEndDate);
        startDateInput.addEventListener('change', updateEndDate);
        
        // Kích hoạt ban đầu nếu đã có giá trị
        if (payPeriodSelect.value && startDateInput.value) {
            updateEndDate();
        }
    }
});

function loadTenants() {
    fetch('/tenants-list')
        .then(response => response.json())
        .then(data => {
            const tenantSelect = document.getElementById('tenant_id');
            if (tenantSelect) {
                tenantSelect.innerHTML = '<option value="">-- Chọn người thuê --</option>';
                
                data.forEach(tenant => {
                    const option = document.createElement('option');
                    option.value = tenant.id;
                    option.textContent = `${tenant.name} - ${tenant.tel}` + (tenant.email ? ` - ${tenant.email}` : '');
                    
                    // Check for old('tenant_id') or selected_tenant_id
                    const oldValue = tenantSelect.getAttribute('data-old-value');
                    if (tenant.id == oldValue) {
                        option.selected = true;
                    }
                    
                    tenantSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading tenants:', error));
}
</script>
@endpush