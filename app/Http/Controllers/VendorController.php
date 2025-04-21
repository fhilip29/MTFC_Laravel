<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.gym.vendors', [
            'vendors' => Vendor::withCount('equipments')->latest()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
        ]);

        Vendor::create($data);

        return redirect()->back()->with('success', 'Vendor added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        $vendor->load('equipments');
        return view('admin.gym.vendor_details', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
        ]);

        $vendor->update($data);

        return redirect()->back()->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        // Check if vendor has equipment
        if ($vendor->equipments()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete vendor with associated equipment.');
        }
        
        $vendor->delete();
        return redirect()->back()->with('success', 'Vendor deleted successfully.');
    }
}
