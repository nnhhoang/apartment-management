<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ApartmentRoomController;
use App\Http\Controllers\ContractAIController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
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
    Route::redirect('/dashboard', '/home');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Apartments 
    Route::controller(ApartmentController::class)->group(function () {
        Route::get('/apartments', 'index')->name('apartments.index');
        Route::get('/apartments/create', 'create');
        Route::post('/apartments', 'store');
        Route::get('/apartments/{apartment}', 'show')->name('apartments.show');
        Route::get('/apartments/{apartment}/edit', 'edit');
        Route::put('/apartments/{apartment}', 'update');
        Route::delete('/apartments/{apartment}', 'destroy');
    });

    // Apartment Rooms 
    Route::controller(ApartmentRoomController::class)->group(function () {
        Route::get('/apartment_rooms', 'index')->name('apartment_rooms.index');
        Route::get('/apartment_rooms/create', 'create')->name('apartment_rooms.create');
        Route::post('/apartment_rooms', 'store');
        Route::get('/apartment_rooms/{apartmentRoom}', 'show')->name('apartment_rooms.show');
        Route::get('/apartment_rooms/{apartmentRoom}/edit', 'edit');
        Route::put('/apartment_rooms/{apartmentRoom}', 'update');
        Route::delete('/apartment_rooms/{apartmentRoom}', 'destroy');
    });

    // Tenants 
    Route::controller(TenantController::class)->group(function () {
        Route::get('/tenants', 'index')->name('tenants.index');
        Route::get('/tenants/create', 'create')->name('tenants.create');
        Route::post('/tenants', 'store')->name('tenants.store');
        Route::get('/tenants/{tenant}', 'show')->name('tenants.show');
        Route::get('/tenants/{tenant}/edit', 'edit')->name('tenants.edit');
        Route::put('/tenants/{tenant}', 'update')->name('tenants.update');
        Route::delete('/tenants/{tenant}', 'destroy')->name('tenants.destroy');
        
        // API endpoint for tenants list
        Route::get('/tenants-list', 'getTenants')->name('tenants.list');
    });

    // Tenant Contracts
    Route::controller(TenantContractController::class)->group(function () {
        Route::get('/tenant_contracts', 'index')->name('tenant_contracts.index');
        Route::get('/tenant_contracts/create', 'create')->name('tenant_contracts.create');
        Route::post('/tenant_contracts', 'store')->name('tenant_contracts.store');
        Route::get('/tenant_contracts/{tenantContract}', 'show')->name('tenant_contracts.show');
        Route::get('/tenant_contracts/{tenantContract}/edit', 'edit')->name('tenant_contracts.edit');
        Route::put('/tenant_contracts/{tenantContract}', 'update')->name('tenant_contracts.update');
        Route::delete('/tenant_contracts/{tenantContract}', 'destroy')->name('tenant_contracts.destroy');
        Route::post('/tenant_contracts/{tenantContract}/end', 'endContract')->name('tenant_contracts.end');
    });

    // Room Fees 
    Route::controller(RoomFeeCollectionController::class)->group(function () {
        Route::get('/room_fees', 'index')->name('room_fees.index');
        Route::get('/room_fees/create', 'create')->name('room_fees.create');
        Route::post('/room_fees', 'store')->name('room_fees.store');
        Route::get('/room_fees/{roomFeeCollection}', 'show')->name('room_fees.show');
        Route::get('/room_fees/{roomFeeCollection}/edit', 'edit')->name('room_fees.edit');
        Route::put('/room_fees/{roomFeeCollection}', 'update')->name('room_fees.update');
        Route::delete('/room_fees/{roomFeeCollection}', 'destroy')->name('room_fees.destroy');
        Route::post('/room_fees/{roomFeeCollection}/payment', 'addPayment')->name('room_fees.payment');
    });

    // Statistics 
    Route::controller(StatisticsController::class)->group(function () {
        Route::get('/statistics', 'index')->name('statistics.index');
        Route::get('/statistics/unpaid', 'unpaidRooms')->name('statistics.unpaid');
        Route::get('/statistics/apartments', 'apartmentStatistics')->name('statistics.apartments');
        Route::get('/statistics/rooms', 'roomStatistics')->name('statistics.rooms');
        Route::get('/statistics/chart-data', 'getChartData')->name('statistics.chart-data');
    });

    Route::controller(ContractAIController::class)->group(function () {
        Route::get('/tenant_contracts/{tenantContract}/ai-assistant', 'show')->name('tenant_contracts.ai');
        Route::post('/tenant_contracts/{tenantContract}/ai-assistant/ask', 'askQuestion')->name('tenant_contracts.ai.ask');
        Route::get('/my-contracts/ai-assistant', 'tenantAssistant')->name('tenant.ai');
    });
    
    // API Routes for internal use
    Route::get('/api/apartments/{apartment}/rooms', function($apartment) {
        return \App\Models\ApartmentRoom::where('apartment_id', $apartment)
            ->orderBy('room_number')
            ->get(['id', 'room_number']);
    });
});

require __DIR__ . '/auth.php';