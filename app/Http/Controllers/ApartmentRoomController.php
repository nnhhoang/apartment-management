<?php

namespace App\Http\Controllers;

use App\Events\ApartmentRoomCreated;
use App\Http\Requests\ApartmentRoomRequest;
use App\Models\Apartment;
use App\Models\ApartmentRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ApartmentRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {   
        $query = ApartmentRoom::whereHas('apartment', function($q) {
            $q->where('user_id', Auth::id());
        });

        if ($request->has('apartment_id') && $request->apartment_id) {
            $query->where('apartment_id', $request->apartment_id);
        }

        if ($request->has('room_number') && $request->room_number) {
            $query->where('room_number', 'like', "%{$request->room_number}%");
        }

        $rooms = $query->with(['apartment', 'contracts.tenant'])->paginate(10);

        $apartments = Apartment::where('user_id', Auth::id())->orderBy('name')->get();
        
        return view('apartment_rooms.index', compact('rooms', 'apartments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $apartments = Apartment::where('user_id', Auth::id())->orderBy('name')->get();

        $selectedApartmentId = $request->apartment_id;
        
        return view('apartment_rooms.create', compact('apartments', 'selectedApartmentId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApartmentRoomRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $apartment = Apartment::findOrFail($validatedData['apartment_id']);
        $this->authorize('view', $apartment);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('rooms', 'public');
            $validatedData['image'] = $path;
        }

        $room = ApartmentRoom::create($validatedData);

        event(new ApartmentRoomCreated($room));
        
        return redirect()->route('apartment_rooms.show', $room)
            ->with('success', 'Phòng trọ đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ApartmentRoom $apartmentRoom): View
    {
        $this->authorize('view', $apartmentRoom);
        
        $apartmentRoom->load(['apartment', 'contracts.tenant', 'feeCollections']);
        
        return view('apartment_rooms.show', compact('apartmentRoom'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApartmentRoom $apartmentRoom): View
    {
        $this->authorize('update', $apartmentRoom);
        
        $apartments = Apartment::where('user_id', Auth::id())->orderBy('name')->get();
        
        return view('apartment_rooms.edit', compact('apartmentRoom', 'apartments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ApartmentRoomRequest $request, ApartmentRoom $apartmentRoom): RedirectResponse
    {
        $this->authorize('update', $apartmentRoom);
        
        $validatedData = $request->validated();

        if ($validatedData['apartment_id'] != $apartmentRoom->apartment_id) {
            $apartment = Apartment::findOrFail($validatedData['apartment_id']);
            $this->authorize('view', $apartment);
        }

        if ($request->hasFile('image')) {

            if ($apartmentRoom->image) {
                Storage::disk('public')->delete($apartmentRoom->image);
            }
            
            $path = $request->file('image')->store('rooms', 'public');
            $validatedData['image'] = $path;
        }
        
        $apartmentRoom->update($validatedData);
        
        return redirect()->route('apartment_rooms.show', $apartmentRoom)
            ->with('success', 'Thông tin phòng trọ đã được cập nhật.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApartmentRoom $apartmentRoom): RedirectResponse
    {
        $this->authorize('delete', $apartmentRoom);

        if ($apartmentRoom->contracts()->exists()) {
            return back()->with('error', 'Không thể xóa phòng đã có hợp đồng.');
        }

        if ($apartmentRoom->image) {
            Storage::disk('public')->delete($apartmentRoom->image);
        }
        
        $apartmentRoom->delete();
        
        return redirect()->route('apartment_rooms.index')
            ->with('success', 'Phòng trọ đã được xóa thành công.');
    }
}