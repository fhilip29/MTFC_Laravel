<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Vendor;
use App\Models\EquipmentMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EquipmentController extends Controller
{
    public function index()
    {
        return view('admin.gym.admin_gym', [
            'equipments' => Equipment::with('vendor')->latest()->get(),
            'vendors' => Vendor::all(),
            'maintenanceLogs' => EquipmentMaintenance::with('equipment')->latest()->take(10)->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'date_purchased' => 'required|date',
            'quality' => 'required|string|in:new,good,fair,rusty',
            'vendor_id' => 'required|exists:vendors,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'image.max' => 'The image must not be larger than 2MB.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
        ]);

        // Format date using Carbon
        $data['date_purchased'] = Carbon::parse($data['date_purchased'])->format('Y-m-d');

        try {
            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('equipment_images', 'public');
            }

            Equipment::create($data);

            return redirect()->route('admin.gym.gym')
                ->with('success', 'Equipment added successfully.');
        } catch (\Exception $e) {
            // If there was an error uploading the image, delete it if it was uploaded
            if (isset($data['image_path']) && Storage::disk('public')->exists($data['image_path'])) {
                Storage::disk('public')->delete($data['image_path']);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error adding equipment: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Equipment $equipment)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'date_purchased' => 'required|date',
            'quality' => 'required|string|in:new,good,fair,rusty',
            'vendor_id' => 'required|exists:vendors,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'image.max' => 'The image must not be larger than 2MB.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
        ]);

        // Format date using Carbon
        $data['date_purchased'] = Carbon::parse($data['date_purchased'])->format('Y-m-d');

        try {
            $oldImagePath = null;
            
            if ($request->hasFile('image')) {
                // Keep track of the old image path in case we need to restore it on error
                $oldImagePath = $equipment->image_path;
                
                // Upload the new image
                $data['image_path'] = $request->file('image')->store('equipment_images', 'public');
                
                // Delete old image if successful and if it exists
                if ($oldImagePath) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }

            $equipment->update($data);

            return redirect()->route('admin.gym.gym')
                ->with('success', 'Equipment updated successfully.');
        } catch (\Exception $e) {
            // If there was an error and we uploaded a new image, delete it
            if (isset($data['image_path']) && Storage::disk('public')->exists($data['image_path'])) {
                Storage::disk('public')->delete($data['image_path']);
            }
            
            // If we deleted the old image but encountered an error, we can't restore it
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating equipment: ' . $e->getMessage());
        }
    }

    public function destroy(Equipment $equipment)
    {
        if ($equipment->image_path) {
            Storage::disk('public')->delete($equipment->image_path);
        }
        
        // Delete associated maintenance logs
        $equipment->maintenanceLogs()->delete();
        
        $equipment->delete();
        return redirect()->route('admin.gym.gym')->with('success', 'Equipment deleted successfully.');
    }
    
    public function show(Equipment $equipment)
    {
        $equipment->load('vendor', 'maintenanceLogs');
        return view('admin.gym.equipment_details', [
            'equipment' => $equipment,
            'vendors' => Vendor::all()
        ]);
    }
}