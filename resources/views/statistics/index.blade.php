@extends('layouts.app')

@section('title', 'Thống kê')

@section('header', 'Thống kê tổng quan')

@section('content')
<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow border-left-primary">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Biểu đồ doanh thu và dư nợ theo tháng</h6>
                <div>
                    <select id="yearSelect" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                        @for($year = date('Y'); $year >= date('Y') - 3; $year--)
                            <option value="{{ $year }}">Năm {{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Thống kê phòng chưa thanh toán đủ</h6>
            </div>
            <div class="card-body">
                <div id="unpaidRooms">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ url('/statistics/unpaid') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>Xem tất cả
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Thống kê theo tòa nhà</h6>
            </div>
            <div class="card-body">
                <div id="apartmentStats">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ url('/statistics/apartments') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>Xem tất cả
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Thống kê theo phòng</h6>
                <a href="{{ url('/statistics/rooms') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye me-1"></i>Xem tất cả
                </a>
            </div>
            <div class="card-body">
                <div id="roomStats">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch unpaid rooms
        fetchUnpaidRooms();
        
        // Fetch apartment stats
        fetchApartmentStats();
        
        // Fetch room stats
        fetchRoomStats();
        
        // Initialize revenue chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        let revenueChart;
        
        // Fetch chart data when year changes
        const yearSelect = document.getElementById('yearSelect');
        yearSelect.addEventListener('change', function() {
            fetchChartData(this.value);
        });
        
        // Initial chart data fetch
        fetchChartData(yearSelect.value);
        
        function fetchChartData(year) {
            fetch(`/statistics/chart-data?year=${year}`)
                .then(response => response.json())
                .then(data => {
                    if (revenueChart) {
                        revenueChart.destroy();
                    }
                    
                    const monthNames = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
                    
                    revenueChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.map(item => monthNames[item.month - 1]),
                            datasets: [
                                {
                                    label: 'Tổng tiền',
                                    data: data.map(item => item.total_price),
                                    backgroundColor: 'rgba(78, 115, 223, 0.5)',
                                    borderColor: 'rgba(78, 115, 223, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Đã thanh toán',
                                    data: data.map(item => item.total_paid),
                                    backgroundColor: 'rgba(28, 200, 138, 0.5)',
                                    borderColor: 'rgba(28, 200, 138, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Dư nợ',
                                    data: data.map(item => item.total_debt),
                                    backgroundColor: 'rgba(231, 74, 59, 0.5)',
                                    borderColor: 'rgba(231, 74, 59, 1)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return formatMoney(value);
                                        }
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + formatMoney(context.raw);
                                        }
                                    }
                                },
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }
        
        function fetchUnpaidRooms() {
            fetch('/statistics/unpaid')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const table = doc.querySelector('table');
                    
                    if (table) {
                        // Limit to 5 rows
                        const rows = table.querySelectorAll('tbody tr');
                        if (rows.length > 5) {
                            for (let i = 5; i < rows.length; i++) {
                                rows[i].remove();
                            }
                        }
                        
                        document.getElementById('unpaidRooms').innerHTML = table.outerHTML;
                    } else {
                        document.getElementById('unpaidRooms').innerHTML = '<div class="alert alert-info">Không có phòng nào chưa thanh toán đủ</div>';
                    }
                })
                .catch(error => console.error('Error fetching unpaid rooms:', error));
        }
        
        function fetchApartmentStats() {
            fetch('/statistics/apartments')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const table = doc.querySelector('table');
                    
                    if (table) {
                        // Limit to 5 rows
                        const rows = table.querySelectorAll('tbody tr');
                        if (rows.length > 5) {
                            for (let i = 5; i < rows.length; i++) {
                                rows[i].remove();
                            }
                        }
                        
                        document.getElementById('apartmentStats').innerHTML = table.outerHTML;
                    } else {
                        document.getElementById('apartmentStats').innerHTML = '<div class="alert alert-info">Không có dữ liệu thống kê theo tòa nhà</div>';
                    }
                })
                .catch(error => console.error('Error fetching apartment stats:', error));
        }
        
        function fetchRoomStats() {
            fetch('/statistics/rooms')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const table = doc.querySelector('table');
                    
                    if (table) {
                        // Limit to 5 rows
                        const rows = table.querySelectorAll('tbody tr');
                        if (rows.length > 5) {
                            for (let i = 5; i < rows.length; i++) {
                                rows[i].remove();
                            }
                        }
                        
                        document.getElementById('roomStats').innerHTML = table.outerHTML;
                    } else {
                        document.getElementById('roomStats').innerHTML = '<div class="alert alert-info">Không có dữ liệu thống kê theo phòng</div>';
                    }
                })
                .catch(error => console.error('Error fetching room stats:', error));
        }
        
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';
        }
    });
</script>
@endsection