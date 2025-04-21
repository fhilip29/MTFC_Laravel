<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipmentMaintenance extends Model
{
    use HasFactory;

    // Explicitly set the table name to match the migration
    protected $table = 'equipment_maintenances';

    protected $fillable = [
        'equipment_id', 
        'performed_by', 
        'notes', 
        'maintenance_date'
    ];

    protected $dates = ['maintenance_date'];
    
    // Add casts to ensure dates are properly handled
    protected $casts = [
        'maintenance_date' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
