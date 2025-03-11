@extends('layouts.app')

@section('title', 'Danh sách hợp đồng thuê')

@section('header', 'Danh sách hợp đồng thuê')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 fw-bold text-primary">Danh sách hợp đồng thuê</h6>
        <a href="{{ url('/tenant_contracts/create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tạo hợp đồng mới
        </a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ url('/tenant_contracts') }}" method="GET" class="mb-4">
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
                    <label for="status" class="form-label">Trạng thái hợp đồng</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hiệu lực</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Đã kết thúc</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="room_id" class="form-label">Phòng</label>
                    <select class="form-select" id="room_id" name="room_id">
                        <option value="">Tất cả phòng</option>
                        <!-- Room options will be populated via JavaScript -->
                    </select>
                </div>
                
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Tìm kiếm
                    </button>
                    <a href="{{ url('/tenant_contracts') }}" class="btn btn-secondary">
                        <i class="fas fa-sync-alt me-1"></i>Làm mới
                    </a>
                </div>
            </div>
        </form>

        <!-- Contracts Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Người thuê</th>
                        <th scope="col">Phòng</th>
                        <th scope="col">Tòa nhà</th>
                        <th scope="col">Giá thuê</th>
                        <th scope="col">Ngày bắt đầu</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                    <tr>
                        <td>{{ $contracts->firstItem() + $loop->index }}</td>
                        <td>{{ $contract->tenant->name }}</td>
                        <td>{{ $contract->room->room_number }}</td>
                        <td>{{ $contract->room->apartment->name }}</td>
                        <td>{{ number_format($contract->price, 0, ',', '.') }} VNĐ</td>
                        <td>{{ $contract->start_date->format('d/m/Y') }}</td>
                        <td>
                            @if($contract->end_date)
                                @if($contract->end_date->isPast())
                                    <span class="badge bg-danger">Đã kết thúc</span>
                                @else
                                    <span class="badge bg-warning">Có thời hạn</span>
                                @endif
                            @else
                                <span class="badge bg-success">Đang hiệu lực</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ url('/tenant_contracts/' . $contract->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/tenant_contracts/' . $contract->id . '/edit') }}" class="btn btn-sm btn-warning" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($contract->feeCollections->isEmpty())
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $contract->id }}" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>

                            <!-- Delete Modal -->
                            @if($contract->feeCollections->isEmpty())
                            <div class="modal fade" id="deleteModal{{ $contract->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $contract->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $contract->id }}">Xác nhận xóa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có chắc chắn muốn xóa hợp đồng của <strong>{{ $contract->tenant->name }}</strong> cho phòng <strong>{{ $contract->room->room_number }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <form action="{{ url('/tenant_contracts/' . $contract->id) }}" method="POST" class="d-inline">
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
                        <td colspan="8" class="text-center">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $contracts->links() }}
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