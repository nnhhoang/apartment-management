<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ApartmentRoom;
use App\Models\RoomFeeCollection;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    /**
     * Trang thống kê chính.
     */
    public function index(): View
    {
        return view('statistics.index');
    }
    
    /**
     * Thống kê phòng chưa thanh toán đủ.
     */
    public function unpaidRooms(): View
    {
        $previousMonth = Carbon::now()->subMonth();
        
        // Lấy các phòng chưa thanh toán đủ của tháng trước
        $unpaidCollections = RoomFeeCollection::with(['room.apartment', 'tenant', 'contract'])
            ->whereHas('room.apartment', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereMonth('charge_date', $previousMonth->month)
            ->whereYear('charge_date', $previousMonth->year)
            ->whereRaw('total_paid < total_price')
            ->orderBy('room_fee_collections.charge_date', 'desc')
            ->paginate(10);
        
        return view('statistics.unpaid_rooms', compact('unpaidCollections', 'previousMonth'));
    }
    
    /**
     * Thống kê theo tòa nhà.
     */
    public function apartmentStatistics(): View
    {
        // Lấy danh sách tòa nhà
        $apartments = Apartment::where('user_id', Auth::id())
            ->withCount('rooms')
            ->get();
        
        // Lấy tổng số phòng, số phòng có người thuê, số phòng trống
        $occupancyStats = ApartmentRoom::select('apartment_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN EXISTS (SELECT 1 FROM tenant_contracts WHERE apartment_rooms.id = tenant_contracts.apartment_room_id AND tenant_contracts.end_date IS NULL) THEN 1 ELSE 0 END) as occupied')
            ->whereHas('apartment', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->groupBy('apartment_id')
            ->get()
            ->keyBy('apartment_id');
        
        // Lấy tổng doanh thu của tháng hiện tại theo tòa nhà
        $currentMonth = Carbon::now();
        $revenueStats = RoomFeeCollection::select('apartments.id as apartment_id')
            ->selectRaw('SUM(room_fee_collections.total_paid) as revenue')
            ->join('apartment_rooms', 'room_fee_collections.apartment_room_id', '=', 'apartment_rooms.id')
            ->join('apartments', 'apartment_rooms.apartment_id', '=', 'apartments.id')
            ->where('apartments.user_id', Auth::id())
            ->whereMonth('room_fee_collections.charge_date', $currentMonth->month)
            ->whereYear('room_fee_collections.charge_date', $currentMonth->year)
            ->groupBy('apartments.id')
            ->get()
            ->keyBy('apartment_id');
        
        return view('statistics.apartments', compact('apartments', 'occupancyStats', 'revenueStats', 'currentMonth'));
    }
    
    /**
     * Thống kê theo phòng.
     */
    public function roomStatistics(Request $request): View
    {
        // Lấy các phòng có hợp đồng đang active
        $query = ApartmentRoom::whereHas('apartment', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->with(['apartment', 'activeContract.tenant']);
        
        // Lọc theo tòa nhà
        if ($request->has('apartment_id') && $request->apartment_id) {
            $query->where('apartment_id', $request->apartment_id);
        }
        
        $rooms = $query->get();
        
        // Lấy danh sách tòa nhà để dropdown filter
        $apartments = Apartment::where('user_id', Auth::id())->orderBy('name')->get();
        
        // Lấy thống kê doanh thu theo tháng (12 tháng gần nhất)
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        $monthlyStats = RoomFeeCollection::select('apartment_room_id')
            ->selectRaw('DATE_FORMAT(charge_date, "%Y-%m") as month')
            ->selectRaw('SUM(total_price) as total_price')
            ->selectRaw('SUM(total_paid) as total_paid')
            ->selectRaw('SUM(total_price - total_paid) as total_debt')
            ->whereHas('room.apartment', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereBetween('charge_date', [$startDate, $endDate])
            ->groupBy('apartment_room_id', 'month')
            ->get()
            ->groupBy('apartment_room_id');
        
        return view('statistics.rooms', compact('rooms', 'apartments', 'monthlyStats', 'startDate', 'endDate'));
    }
    
    /**
     * API lấy dữ liệu biểu đồ theo tháng.
     */
    public function getChartData(Request $request): JsonResponse
    {
        $request->validate([
            'year' => 'required|integer',
        ]);
        
        $year = $request->year;

        $monthlyData = RoomFeeCollection::select(DB::raw('MONTH(charge_date) as month'))
            ->selectRaw('SUM(total_price) as total_price')
            ->selectRaw('SUM(total_paid) as total_paid')
            ->selectRaw('SUM(total_price - total_paid) as total_debt')
            ->whereHas('room.apartment', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereYear('charge_date', $year)
            ->groupBy(DB::raw('MONTH(charge_date)'))
            ->orderBy('month')
            ->get();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $monthlyData->firstWhere('month', $i);
            $chartData[] = [
                'month' => $i,
                'total_price' => $monthData ? $monthData->total_price : 0,
                'total_paid' => $monthData ? $monthData->total_paid : 0,
                'total_debt' => $monthData ? $monthData->total_debt : 0,
            ];
        }
        
        return response()->json($chartData);
    }
}