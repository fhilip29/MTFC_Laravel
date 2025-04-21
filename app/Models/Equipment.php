<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory;

    // Explicitly set the table name to match the migration
    protected $table = 'equipments';

    protected $fillable = [
        'name', 'description', 'qty', 'price', 'date_purchased',
        'quality', 'vendor_id', 'image_path',
    ];

    protected $dates = ['date_purchased'];

    // Add casts to ensure dates are properly handled
    protected $casts = [
        'date_purchased' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : asset('images/placeholder.png');
    }
}
