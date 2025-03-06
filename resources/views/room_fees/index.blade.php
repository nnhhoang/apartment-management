@extends('layouts.app')

@section('title', 'Danh sách khoản thu tiền phòng')

@section('header', 'Danh sách khoản thu tiền phòng')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 fw-bold text-primary">Danh sách khoản thu tiền phòng</h6>
        <a href="{{ url('/room_fees/create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tạo khoản thu mới
        </a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ url('/room_fees') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
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
                
                <div class="col-md-3">
                    <label for="room_id" class="form-label">Phòng</label>
                    <select class="form-select" id="room_id" name="room_id">
                        <option value="">Tất cả phòng</option>
                        <!-- Room options will be populated via JavaScript -->
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="month" class="form-label">Tháng</label>
                    <input type="month" class="form-control" id="month" name="month" value="{{ request('month') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="payment_status" class="form-label">Trạng thái thanh toán</label>
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="">Tất cả</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán đủ</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán đủ</option>
                    </select>
                </div>
                
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Tìm kiếm
                    </button>
                    <a href="{{ url('/room_fees') }}" class="btn btn-secondary">
                        <i class="fas fa-sync-alt me-1"></i>Làm mới
                    </a>
                </div>
            </div>
        </form>

        <!-- Room Fees Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Phòng</th>
                        <th scope="col">Tòa nhà</th>
                        <th scope="col">Người thuê</th>
                        <th scope="col">Tháng thu</th>
                        <th scope="col">Tổng tiền</th>
                        <th scope="col">Đã thanh toán</th>
                        <th scope="col">Còn nợ</th>
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feeCollections as $fee)
                    <tr>
                        <td>{{ $feeCollections->firstItem() + $loop->index }}</td>
                        <td>{{ $fee->room->room_number }}</td>
                        <td>{{ $fee->room->apartment->name }}</td>
                        <td>{{ $fee->tenant->name }}</td>
                        <td>{{ $fee->charge_date->format('m/Y') }}</td>
                        <td>{{ number_format($fee->total_price, 0, ',', '.') }} VNĐ</td>
                        <td>{{ number_format($fee->total_paid, 0, ',', '.') }} VNĐ</td>
                        <td>
                            @if($fee->total_paid < $fee->total_price)
                                <span class="text-danger">{{ number_format($fee->total_price - $fee->total_paid, 0, ',', '.') }} VNĐ</span>
                            @else
                                <span class="text-success">0 VNĐ</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ url('/room_fees/' . $fee->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/room_fees/' . $fee->id . '/edit') }}" class="btn btn-sm btn-warning" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $fee->id }}" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $fee->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $fee->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $fee->id }}">Xác nhận xóa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có chắc chắn muốn xóa khoản thu của phòng <strong>{{ $fee->room->room_number }}</strong> tháng <strong>{{ $fee->charge_date->format('m/Y') }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <form action="{{ url('/room_fees/' . $fee->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Xóa</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $feeCollections->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle apartment selection to update rooms
        const apartmentSelect = document.getElementById('apartment_id');
        const roomSelect = document.getElementById('room_id');
        
        if (apartmentSelect && roomSelect) {
            apartmentSelect.addEventListener('change', function() {
                const apartmentId = this.value;
                
                // Clear current room options
                roomSelect.innerHTML = '<option value="">Tất cả phòng</option>';
                
                if (apartmentId) {
                    // Fetch rooms for selected apartment
                    fetch(`/api/apartments/${apartmentId}/rooms`)
                        .then(response => response.json())
                        .then(rooms => {
                            rooms.forEach(room => {
                                const option = document.createElement('option');
                                option.value = room.id;
                                option.textContent = room.room_number;
                                
                                if (room.id == "{{ request('room_id') }}") {
                                    option.selected = true;
                                }
                                
                                roomSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error fetching rooms:', error));
                }
            });
            
            // Trigger change event to load rooms if apartment is already selected
            if (apartmentSelect.value) {
                apartmentSelect.dispatchEvent(new Event('change'));
            }
        }
    });
</script>
@endsection