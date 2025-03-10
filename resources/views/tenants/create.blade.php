@extends('layouts.app')

@section('title', 'Thêm người thuê mới')

@section('header', 'Thêm người thuê mới')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Thông tin người thuê</h6>
    </div>
    <div class="card-body">
        <form action="{{ url('/tenants') }}" method="POST">
            @csrf
            
            @if(isset($returnToContract) && $returnToContract)
                <input type="hidden" name="return_to" value="contract">
                @if(isset($roomId) && $roomId)
                    <input type="hidden" name="room_id" value="{{ $roomId }}">
                @endif
            @endif
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="tel" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('tel') is-invalid @enderror" id="tel" name="tel" value="{{ old('tel') }}" required>
                    @error('tel')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="identity_card_number" class="form-label">Số CMND/CCCD</label>
                    <input type="text" class="form-control @error('identity_card_number') is-invalid @enderror" id="identity_card_number" name="identity_card_number" value="{{ old('identity_card_number') }}">
                    @error('identity_card_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Lưu
                </button>
                
                @if(isset($returnToContract) && $returnToContract)
                    @if(isset($roomId) && $roomId)
                        <a href="{{ url('/tenant_contracts/create?room_id=' . $roomId) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại tạo hợp đồng
                        </a>
                    @else
                        <a href="{{ url('/tenant_contracts/create') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại tạo hợp đồng
                        </a>
                    @endif
                @else
                    <a href="{{ url('/tenants') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection