@extends('layouts.app')

@section('title', 'Danh sách tòa nhà')

@section('header', 'Danh sách tòa nhà')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 fw-bold text-primary">Danh sách tòa nhà</h6>
        <a href="{{ url('/apartments/create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Thêm tòa nhà mới
        </a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ url('/apartments') }}" method="GET" class="mb-4">
            <div class="row g-2">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Tìm kiếm theo tên, địa chỉ..." name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-1"></i>Tìm kiếm
                        </button>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ url('/apartments') }}" class="btn btn-secondary">
                        <i class="fas fa-sync-alt me-1"></i>Làm mới
                    </a>
                </div>
            </div>
        </form>

        <!-- Apartments Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tên tòa nhà</th>
                        <th scope="col">Địa chỉ</th>
                        <th scope="col">Số phòng</th>
                        <th scope="col">Ngày tạo</th>
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apartments as $apartment)
                    <tr>
                        <td>{{ $apartments->firstItem() + $loop->index }}</td>
                        <td>{{ $apartment->name }}</td>
                        <td>{{ $apartment->address }}</td>
                        <td>{{ $apartment->rooms_count ?? $apartment->rooms->count() }}</td>
                        <td>{{ $apartment->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ url('/apartments/' . $apartment->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/apartments/' . $apartment->id . '/edit') }}" class="btn btn-sm btn-warning" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $apartment->id }}" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $apartment->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $apartment->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $apartment->id }}">Xác nhận xóa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có chắc chắn muốn xóa tòa nhà <strong>{{ $apartment->name }}</strong>?
                                            <p class="text-danger mt-2">Lưu ý: Tất cả phòng, hợp đồng và khoản thu liên quan đến tòa nhà này cũng sẽ bị xóa.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <form action="{{ url('/apartments/' . $apartment->id) }}" method="POST" class="d-inline">
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
                        <td colspan="6" class="text-center">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $apartments->links() }}
        </div>
    </div>
</div>
@endsection