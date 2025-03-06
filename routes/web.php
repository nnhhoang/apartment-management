<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ApartmentRoomController;
use App\Http\Controllers\ContractAIController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomFeeCollectionController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantContractController;
use Illuminate\Support\Facades\Route;

// Trang chính
Route::get('/', fn() => redirect()->route('login'));

// Auth routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/home', HomeController::class)->name('dashboard');

    // Apartments (Tòa nhà)
    Route::controller(ApartmentController::class)->group(function () {
        Route::get('/apartments', 'index')->name('apartments.index');
        Route::get('/apartments/create', 'create');
        Route::post('/apartments', 'store');
        Route::get('/apartments/{apartment}', 'show')->name('apartments.show');
        Route::get('/apartments/{apartment}/edit', 'edit');
        Route::put('/apartments/{apartment}', 'update');
        Route::delete('/apartments/{apartment}', 'destroy');
    });

    // Apartment Rooms (Phòng trọ)
    Route::controller(ApartmentRoomController::class)->group(function () {
        Route::get('/apartment_rooms', 'index')->name('apartment_rooms.index');
        Route::get('/apartment_rooms/create', 'create')->name('apartment_rooms.create');
        Route::post('/apartment_rooms', 'store');
        Route::get('/apartment_rooms/{apartmentRoom}', 'show');
        Route::get('/apartment_rooms/{apartmentRoom}/edit', 'edit');
        Route::put('/apartment_rooms/{apartmentRoom}', 'update');
        Route::delete('/apartment_rooms/{apartmentRoom}', 'destroy');
    });

    // Tenants (Người thuê)
    Route::controller(TenantController::class)->group(function () {
        Route::get('/tenants', 'index');
        Route::get('/tenants/create', 'create');
        Route::post('/tenants', 'store');
        Route::get('/tenants/{tenant}', 'show')->name('tenants.show');
        Route::get('/tenants/{tenant}/edit', 'edit');
        Route::put('/tenants/{tenant}', 'update');
        Route::delete('/tenants/{tenant}', 'destroy');
        Route::get('/tenants-list', 'getTenants');
    });

    // Tenant Contracts (Hợp đồng thuê)
    Route::controller(TenantContractController::class)->group(function () {
        Route::get('/tenant_contracts', 'index');
        Route::get('/tenant_contracts/create', 'create');
        Route::post('/tenant_contracts', 'store');
        Route::get('/tenant_contracts/{tenantContract}', 'show');
        Route::get('/tenant_contracts/{tenantContract}/edit', 'edit');
        Route::put('/tenant_contracts/{tenantContract}', 'update');
        Route::delete('/tenant_contracts/{tenantContract}', 'destroy');
        Route::post('/tenant_contracts/{tenantContract}/end', 'endContract');
    });

    // Room Fees (Tiền trọ hàng tháng)
    Route::controller(RoomFeeCollectionController::class)->group(function () {
        Route::get('/room_fees', 'index');
        Route::get('/room_fees/create', 'create');
        Route::post('/room_fees', 'store');
        Route::get('/room_fees/{roomFeeCollection}', 'show');
        Route::get('/room_fees/{roomFeeCollection}/edit', 'edit');
        Route::put('/room_fees/{roomFeeCollection}', 'update');
        Route::delete('/room_fees/{roomFeeCollection}', 'destroy');
        Route::post('/room_fees/{roomFeeCollection}/payment', 'addPayment');
    });

    // Statistics (Thống kê)
    Route::controller(StatisticsController::class)->group(function () {
        Route::get('/statistics', 'index');
        Route::get('/statistics/unpaid', 'unpaidRooms');
        Route::get('/statistics/apartments', 'apartmentStatistics');
        Route::get('/statistics/rooms', 'roomStatistics');
    });
    
    // AI Assistant cho hợp đồng
    Route::controller(ContractAIController::class)->group(function () {
        // AI Assistant cho chủ trọ
        Route::get('/tenant_contracts/{tenantContract}/ai-assistant', 'show');
        Route::post('/tenant_contracts/{tenantContract}/ai-assistant/ask', 'askQuestion');
        
        // AI Assistant cho người thuê
        Route::get('/my-contracts/ai-assistant', 'tenantAssistant');
    });
});

// Định nghĩa Auth routes của Laravel
require __DIR__ . '/auth.php';
