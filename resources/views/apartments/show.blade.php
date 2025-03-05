@extends('layouts.app')

@section('title', 'Chi tiết tòa nhà')

@section('header', 'Chi tiết tòa nhà')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Thông tin tòa nhà</h6>
                <div>
                    <a href="{{ url('/apartments/' . $apartment->id . '/edit') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Sửa
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i>Xóa
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($apartment->image)
                        <img src="{{ asset('storage/' . $apartment->image) }}" alt="{{ $apartment->name }}" class="img-fluid rounded" style="max-height: 200px;">
                    @else
                        <div class="bg-light rounded p-4 text-center text-muted">
                            <i class="fas fa-building fa-4x mb-2"></i>
                            <p>Không có ảnh</p>
                        </div>
                    @endif
                </div>
                
                <table class="table">
                    <tr>
                        <th style="width: 30%;">Tên tòa nhà:</th>
                        <td>{{ $apartment->name }}</td>
                    </tr>
                    <tr>
                        <th>Địa chỉ:</th>
                        <td>{{ $apartment->address }}</td>
                    </tr>
                    <tr>
                        <th>Tỉnh/Thành phố:</th>
                        <td>{{ $apartment->province_id ?? 'Chưa cập nhật' }}</td>
                    </tr>
                    <tr>
                        <th>Quận/Huyện:</th>
                        <td>{{ $apartment->district_id ?? 'Chưa cập nhật' }}</td>
                    </tr>
                    <tr>
                        <th>Phường/Xã:</th>
                        <td>{{ $apartment->ward_id ?? 'Chưa cập nhật' }}</td>
                    </tr>
                    <tr>
                        <th>Số phòng:</th>
                        <td>{{ $apartment->rooms->count() }}</td>
                    </tr>
                    <tr>
                        <th>Ngày tạo:</th>
                        <td>{{ $apartment->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Danh sách phòng</h6>
                <a href="{{ url('/apartment_rooms/create?apartment_id=' . $apartment->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Thêm phòng mới
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Số phòng</th>
                                <th>Giá thuê</th>
                                <th>Trạng thái</th>
                                <th>Số người ở</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apartment->rooms as $room)
                                <tr>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ number_format($room->default_price, 0, ',', '.') }} VNĐ</td>
                                    <td>
                                        @if($room->activeContract)
                                            <span class="badge bg-success">Đã thuê</span>
                                        @else
                                            <span class="badge bg-warning">Trống</span>
                                        @endif
                                    </td>
                                    <td>{{ $room->activeContract ? $room->activeContract->number_of_tenant_current : '0' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ url('/apartment_rooms/' . $room->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ url('/apartment_rooms/' . $room->id . '/edit') }}" class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!$room->activeContract)
                                                <a href="{{ url('/tenant_contracts/create?room_id=' . $room->id) }}" class="btn btn-sm btn-success" title="Tạo hợp đồng">
                                                    <i class="fas fa-file-contract"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Chưa có phòng nào</td>
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
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa tòa nhà <strong>{{ $apartment->name }}</strong>?
                <p class="text-danger mt-2">Lưu ý: Tất cả phòng, hợp đồng và khoản thu liên quan đến tòa nhà này cũng sẽ bị xóa.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ url('/apartments/' . $apartment->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection