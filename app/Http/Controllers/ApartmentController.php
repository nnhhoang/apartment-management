<?php

namespace App\Http\Controllers;

use App\Events\ApartmentCreated;
use App\Http\Requests\ApartmentRequest;
use App\Models\Apartment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Apartment::where('user_id', Auth::id());

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $apartments = $query->orderBy('name')->paginate(10);
        
        return view('apartments.index', compact('apartments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('apartments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApartmentRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::id();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('apartments', 'public');
            $validatedData['image'] = $path;
        }
        
        $apartment = Apartment::create($validatedData);

        event(new ApartmentCreated($apartment));
        
        return redirect()->route('apartments.show', $apartment)
            ->with('success', 'Tòa nhà đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment): View
    {
        $this->authorize('view', $apartment);
        
        $apartment->load(['rooms.contracts.tenant']);
        
        return view('apartments.show', compact('apartment'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Apartment $apartment): View
    {
        $this->authorize('update', $apartment);
        
        return view('apartments.edit', compact('apartment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ApartmentRequest $request, Apartment $apartment): RedirectResponse
    {
        $this->authorize('update', $apartment);
        
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            if ($apartment->image) {
                Storage::disk('public')->delete($apartment->image);
            }
            
            $path = $request->file('image')->store('apartments', 'public');
            $validatedData['image'] = $path;
        }
        
        $apartment->update($validatedData);
        
        return redirect()->route('apartments.show', $apartment)
            ->with('success', 'Thông tin tòa nhà đã được cập nhật.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apartment $apartment): RedirectResponse
    {
        $this->authorize('delete', $apartment);

        if ($apartment->image) {
            Storage::disk('public')->delete($apartment->image);
        }
        
        $apartment->delete();
        
        return redirect()->route('apartments.index')
            ->with('success', 'Tòa nhà đã được xóa thành công.');
    }
}