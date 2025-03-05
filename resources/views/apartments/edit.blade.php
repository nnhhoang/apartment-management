@extends('layouts.app')

@section('title', 'Chỉnh sửa tòa nhà')

@section('header', 'Chỉnh sửa tòa nhà')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Thông tin tòa nhà</h6>
    </div>
    <div class="card-body">
        <form action="{{ url('/apartments/' . $apartment->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Tên tòa nhà <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $apartment->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $apartment->address) }}" required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="province_id" class="form-label">Tỉnh/Thành phố</label>
                    <input type="text" class="form-control @error('province_id') is-invalid @enderror" id="province_id" name="province_id" value="{{ old('province_id', $apartment->province_id) }}">
                    @error('province_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="district_id" class="form-label">Quận/Huyện</label>
                    <input type="text" class="form-control @error('district_id') is-invalid @enderror" id="district_id" name="district_id" value="{{ old('district_id', $apartment->district_id) }}">
                    @error('district_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="ward_id" class="form-label">Phường/Xã</label>
                    <input type="text" class="form-control @error('ward_id') is-invalid @enderror" id="ward_id" name="ward_id" value="{{ old('ward_id', $apartment->ward_id) }}">
                    @error('ward_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Ảnh tòa nhà</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                <div class="form-text">Định dạng: jpg, jpeg, png. Dung lượng tối đa: 2MB</div>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                @if($apartment->image)
                    <div class="mt-2">
                        <label class="form-label">Ảnh hiện tại:</label>
                        <div>
                            <img src="{{ asset('storage/' . $apartment->image) }}" alt="{{ $apartment->name }}" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Lưu thay đổi
                </button>
                <a href="{{ url('/apartments/' . $apartment->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </form>
    </div>
</div>
@endsection