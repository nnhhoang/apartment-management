@extends('layouts.app')

@section('title', 'Danh sách phòng trọ')

@section('header', 'Danh sách phòng trọ')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 fw-bold text-primary">Danh sách phòng trọ</h6>
        <a href="{{ url('/apartment_rooms/create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Thêm phòng mới
        </a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ url('/apartment_rooms') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="apartment_id" class="form-label">Tòa nhà</label>
                    <select class="form-select" id="apartment_id" name="apartment_id">
                        <option value="">Tất cả tòa nhà</option>
                        @foreach($apartments as $apartment)
                            <option value="{{ $apartment->id }}" {{ request('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                {{ $apartment->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="room_number" class="form-label">Số phòng</label>
                    <input type="text" class="form-control" id="room_number" name="room_number" value="{{ request('room_number') }}" placeholder="Nhập số phòng...">
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Tìm kiếm
                    </button>
                    <a href="{{ url('/apartment_rooms') }}" class="btn btn-secondary">
                        <i class="fas fa-sync-alt me-1"></i>Làm mới
                    </a>
                </div>
            </div>
        </form>

        <!-- Rooms Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Số phòng</th>
                        <th scope="col">Tòa nhà</th>
                        <th scope="col">Giá thuê</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Người thuê</th>
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                    <tr>
                        <td>{{ $rooms->firstItem() + $loop->index }}</td>
                        <td>{{ $room->room_number }}</td>
                        <td>{{ $room->apartment->name }}</td>
                        <td>{{ number_format($room->default_price, 0, ',', '.') }} VNĐ</td>
                        <td>
                            @if($room->hasActiveContract())
                                <span class="badge bg-success">Đã thuê</span>
                            @else
                                <span class="badge bg-warning">Trống</span>
                            @endif
                        </td>
                        <td>
                            @if($room->hasActiveContract())
                                @php $activeContract = $room->getActiveContract(); @endphp
                                {{ $activeContract->tenant->name }}
                            @else
                                <span class="text-muted">Chưa có</span>
                            @endif
                        </td>
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
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $room->id }}" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <a href="{{ url('/room_fees/create?room_id=' . $room->id . '&contract_id=' . $room->activeContract->id) }}" class="btn btn-sm btn-primary" title="Tạo khoản thu">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </a>
                                @endif
                            </div>

                            <!-- Delete Modal -->
                            @if(!$room->activeContract)
                            <div class="modal fade" id="deleteModal{{ $room->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $room->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $room->id }}">Xác nhận xóa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có chắc chắn muốn xóa phòng <strong>{{ $room->room_number }}</strong> của tòa nhà <strong>{{ $room->apartment->name }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <form action="{{ url('/apartment_rooms/' . $room->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Xóa</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $rooms->links() }}
        </div>
    </div>
</div>
@endsection