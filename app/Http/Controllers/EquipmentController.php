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
                // Create directory if it doesn't exist
                $directory = public_path('images/equipment');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Move the file to public/images/equipment
                $file->move($directory, $filename);
                $data['image_path'] = 'images/equipment/' . $filename;
            }

            Equipment::create($data);

            return redirect()->route('admin.gym.gym')
                ->with('success', 'Equipment added successfully.');
        } catch (\Exception $e) {
            // If there was an error and we uploaded a new image, delete it
            if (isset($data['image_path'])) {
                $imagePath = public_path($data['image_path']);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
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
                // Create directory if it doesn't exist
                $directory = public_path('images/equipment');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Keep track of the old image path in case we need to restore it on error
                $oldImagePath = $equipment->image_path;
                
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Move the file to public/images/equipment
                $file->move($directory, $filename);
                $data['image_path'] = 'images/equipment/' . $filename;
                
                // Delete old image if it exists and is not a default image
                if ($oldImagePath && !str_contains($oldImagePath, 'placeholder')) {
                    $oldImageFullPath = public_path($oldImagePath);
                    if (file_exists($oldImageFullPath)) {
                        unlink($oldImageFullPath);
                    }
                }
            }

            $equipment->update($data);

            return redirect()->route('admin.gym.gym')
                ->with('success', 'Equipment updated successfully.');
        } catch (\Exception $e) {
            // If there was an error and we uploaded a new image, delete it
            if (isset($data['image_path'])) {
                $imagePath = public_path($data['image_path']);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating equipment: ' . $e->getMessage());
        }
    }

    public function destroy(Equipment $equipment)
    {
        // Delete the image if it exists and is not a default image
        if ($equipment->image_path && !str_contains($equipment->image_path, 'placeholder')) {
            $imagePath = public_path($equipment->image_path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
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