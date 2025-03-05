@extends('layouts.app')

@section('title', 'Thêm phòng mới')

@section('header', 'Thêm phòng mới')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Thông tin phòng</h6>
    </div>
    <div class="card-body">
        <form action="{{ url('/apartment_rooms') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="apartment_id" class="form-label">Tòa nhà <span class="text-danger">*</span></label>
                    <select class="form-select @error('apartment_id') is-invalid @enderror" id="apartment_id" name="apartment_id" required>
                        <option value="">-- Chọn tòa nhà --</option>
                        @foreach($apartments as $apartment)
                            <option value="{{ $apartment->id }}" {{ (old('apartment_id', $selectedApartmentId) == $apartment->id) ? 'selected' : '' }}>
                                {{ $apartment->name }} - {{ $apartment->address }}
                            </option>
                        @endforeach
                    </select>
                    @error('apartment_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="room_number" class="form-label">Số phòng <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('room_number') is-invalid @enderror" id="room_number" name="room_number" value="{{ old('room_number') }}" required>
                    @error('room_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="default_price" class="form-label">Giá thuê mặc định (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('default_price') is-invalid @enderror" id="default_price" name="default_price" value="{{ old('default_price') }}" min="0" required>
                    @error('default_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="max_tenant" class="form-label">Số người ở tối đa</label>
                    <input type="number" class="form-control @error('max_tenant') is-invalid @enderror" id="max_tenant" name="max_tenant" value="{{ old('max_tenant') }}" min="0">
                    @error('max_tenant')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Ảnh phòng</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                <div class="form-text">Định dạng: jpg, jpeg, png. Dung lượng tối đa: 2MB</div>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Lưu
                </button>
                <a href="{{ url('/apartment_rooms') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </form>
    </div>
</div>
@endsection