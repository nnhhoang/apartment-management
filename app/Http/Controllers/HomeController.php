<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ApartmentRoom;
use App\Models\RoomFeeCollection;
use App\Models\TenantContract;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function __invoke(Request $request): View
    {
        $user = Auth::user();

        $totalApartments = Apartment::where('user_id', $user->id)->count();
        $totalRooms = ApartmentRoom::whereHas('apartment', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        
        $occupiedRooms = ApartmentRoom::whereHas('apartment', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereHas('contracts', function($query) {
            $query->whereNull('end_date');
        })
        ->count();
        
        $vacantRooms = $totalRooms - $occupiedRooms;

        $currentMonth = Carbon::now();
        $currentMonthIncome = RoomFeeCollection::whereHas('room.apartment', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereMonth('charge_date', $currentMonth->month)
        ->whereYear('charge_date', $currentMonth->year)
        ->sum('total_paid');

        $expectedIncome = TenantContract::whereHas('room.apartment', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereNull('end_date')
        ->sum('price');

        $totalDebt = RoomFeeCollection::whereHas('room.apartment', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereRaw('total_paid < total_price')
        ->sum(DB::raw('total_price - total_paid'));

        $unpaidRooms = RoomFeeCollection::with(['room.apartment', 'tenant'])
            ->whereHas('room.apartment', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereRaw('total_paid < total_price')
            ->orderBy('charge_date', 'desc')
            ->limit(5)
            ->get();

        $apartments = Apartment::where('user_id', $user->id)
            ->withCount('rooms')
            ->get();
        
        return view('home', compact(
            'totalApartments',
            'totalRooms',
            'occupiedRooms',
            'vacantRooms',
            'currentMonthIncome',
            'expectedIncome',
            'totalDebt',
            'unpaidRooms',
            'apartments'
        ));
    }
}