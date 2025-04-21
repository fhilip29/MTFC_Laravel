<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentMaintenance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EquipmentMaintenanceController extends Controller
{
    /**
     * Display a listing of all maintenance logs
     */
    public function index()
    {
        $maintenanceLogs = EquipmentMaintenance::with('equipment')->latest()->get();
        
        return view('admin.gym.maintenance_logs', [
            'maintenanceLogs' => $maintenanceLogs
        ]);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'performed_by' => 'required|string',
            'notes' => 'nullable|string',
            'maintenance_date' => 'required|date',
        ]);

        // Format date using Carbon
        $data['maintenance_date'] = Carbon::parse($data['maintenance_date'])->format('Y-m-d');

        EquipmentMaintenance::create($data);

        return redirect()->route('admin.gym.equipment.show', $request->equipment_id)
            ->with('success', 'Maintenance record added successfully.');
    }
    
    public function destroy(EquipmentMaintenance $maintenance)
    {
        $equipment_id = $maintenance->equipment_id;
        $maintenance->delete();
        
        // Check if the request is coming from the maintenance logs page
        if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'maintenance/logs')) {
            return redirect()->route('admin.gym.maintenance')
                ->with('success', 'Maintenance record deleted successfully.');
        }
        
        return redirect()->route('admin.gym.equipment.show', $equipment_id)
            ->with('success', 'Maintenance record deleted successfully.');
    }
}
