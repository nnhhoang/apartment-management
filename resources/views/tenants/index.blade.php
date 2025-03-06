@extends('layouts.app')

@section('title', 'Danh sách người thuê')

@section('header', 'Danh sách người thuê')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 fw-bold text-primary">Danh sách người thuê</h6>
        <a href="{{ url('/tenants/create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Thêm người thuê mới
        </a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ url('/tenants') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Tìm kiếm theo tên, số điện thoại, email..." name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-1"></i>Tìm kiếm
                        </button>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ url('/tenants') }}" class="btn btn-secondary">
                        <i class="fas fa-sync-alt me-1"></i>Làm mới
                    </a>
                </div>
            </div>
        </form>

        <!-- Tenants Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tên người thuê</th>
                        <th scope="col">Số điện thoại</th>
                        <th scope="col">Email</th>
                        <th scope="col">CMND/CCCD</th>
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $tenant)
                    <tr>
                        <td>{{ $tenants->firstItem() + $loop->index }}</td>
                        <td>{{ $tenant->name }}</td>
                        <td>{{ $tenant->tel }}</td>
                        <td>{{ $tenant->email ?? 'Không có' }}</td>
                        <td>{{ $tenant->identity_card_number ?? 'Không có' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ url('/tenants/' . $tenant->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/tenants/' . $tenant->id . '/edit') }}" class="btn btn-sm btn-warning" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $tenant->id }}" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $tenant->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $tenant->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $tenant->id }}">Xác nhận xóa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có chắc chắn muốn xóa người thuê <strong>{{ $tenant->name }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <form action="{{ url('/tenants/' . $tenant->id) }}" method="POST" class="d-inline">
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
            {{ $tenants->links() }}
        </div>
    </div>
</div>
@endsection