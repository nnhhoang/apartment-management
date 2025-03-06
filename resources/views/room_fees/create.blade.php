@extends('layouts.app')

@section('title', 'Tạo khoản thu mới')

@section('header', 'Tạo khoản thu mới')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Thông tin khoản thu</h6>
    </div>
    <div class="card-body">
        <form action="{{ url('/room_fees') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="room_select" class="form-label">Phòng trọ <span class="text-danger">*</span></label>
                    <select class="form-select @error('room_id') is-invalid @enderror" id="room_select" required>
                        <option value="">-- Chọn phòng trọ --</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" 
                                data-contract-id="{{ $room->activeContract->id ?? '' }}"
                                data-electricity-before="{{ $room->activeContract ? $room->activeContract->electricity_number_start : 0 }}"
                                data-water-before="{{ $room->activeContract ? $room->activeContract->water_number_start : 0 }}"
                                {{ (old('apartment_room_id', $selectedRoomId) == $room->id) ? 'selected' : '' }}>
                                {{ $room->apartment->name }} - Phòng {{ $room->room_number }} ({{ $room->activeContract->tenant->name ?? 'Không có người thuê' }})
                            </option>
                        @endforeach
                    </select>
                    @error('apartment_room_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="tenant_contract_id" class="form-label">Hợp đồng <span class="text-danger">*</span></label>
                    <input type="hidden" id="tenant_contract_id" name="tenant_contract_id" value="{{ old('tenant_contract_id', $selectedContractId) }}" required>
                    <input type="text" class="form-control" id="contract_info" readonly>
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
                            <input type="number" class="form-control @error('electricity_number_before') is-invalid @enderror" id="electricity_number_before" name="electricity_number_before" value="{{ old('electricity_number_before') }}" min="0" required>
                            @error('electricity_number_before')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="electricity_number_after" class="form-label">Số điện cuối kỳ <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('electricity_number_after') is-invalid @enderror" id="electricity_number_after" name="electricity_number_after" value="{{ old('electricity_number_after') }}" min="0" required>
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
                            <input type="number" class="form-control @error('water_number_before') is-invalid @enderror" id="water_number_before" name="water_number_before" value="{{ old('water_number_before') }}" min="0" required>
                            @error('water_number_before')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="water_number_after" class="form-label">Số nước cuối kỳ <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('water_number_after') is-invalid @enderror" id="water_number_after" name="water_number_after" value="{{ old('water_number_after') }}" min="0" required>
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
                            <input type="text" class="form-control datepicker @error('charge_date') is-invalid @enderror" id="charge_date" name="charge_date" value="{{ old('charge_date', date('Y-m-d')) }}" required>
                            @error('charge_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="total_paid" class="form-label">Số tiền đã thanh toán (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('total_paid') is-invalid @enderror" id="total_paid" name="total_paid" value="{{ old('total_paid', 0) }}" min="0" required>
                            @error('total_paid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Tạo khoản thu
                </button>
                <a href="{{ url('/room_fees') }}" class="btn btn-secondary">
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
        
        // Handle room selection
        const roomSelect = document.getElementById('room_select');
        const contractIdInput = document.getElementById('tenant_contract_id');
        const contractInfoInput = document.getElementById('contract_info');
        const electricityBeforeInput = document.getElementById('electricity_number_before');
        const waterBeforeInput = document.getElementById('water_number_before');
        
        roomSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                const contractId = selectedOption.getAttribute('data-contract-id');
                const electricityBefore = selectedOption.getAttribute('data-electricity-before');
                const waterBefore = selectedOption.getAttribute('data-water-before');
                
                contractIdInput.value = contractId;
                contractInfoInput.value = selectedOption.textContent;
                
                // If no contract, can't proceed with fee collection
                if (!contractId) {
                    alert('Phòng này chưa có hợp đồng. Vui lòng tạo hợp đồng trước khi tạo khoản thu.');
                    contractInfoInput.value = 'Chưa có hợp đồng';
                    return;
                }
                
                // Set initial values for electricity and water
                if (electricityBefore) {
                    electricityBeforeInput.value = electricityBefore;
                }
                
                if (waterBefore) {
                    waterBeforeInput.value = waterBefore;
                }
            } else {
                contractIdInput.value = '';
                contractInfoInput.value = '';
            }
        });
        
        // Set initial values if room is already selected
        if (roomSelect.value) {
            roomSelect.dispatchEvent(new Event('change'));
        }
        
        // Set contract ID if it's provided but room is not selected
        if (contractIdInput.value && !roomSelect.value) {
            for (let i = 0; i < roomSelect.options.length; i++) {
                if (roomSelect.options[i].getAttribute('data-contract-id') === contractIdInput.value) {
                    roomSelect.selectedIndex = i;
                    roomSelect.dispatchEvent(new Event('change'));
                    break;
                }
            }
        }
    });
</script>
@endsection