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
    
    protected $appends = ['image_url'];

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
        if (empty($this->image_path)) {
            return asset('assets/placeholder.png');
        }
        
        // Check if image path is already a full URL
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }
        
        // Check if path starts with images/ (for new direct public path approach)
        if (strpos($this->image_path, 'images/') === 0) {
            return asset($this->image_path);
        }
        
        // Check if path starts with storage/ or public/
        if (strpos($this->image_path, 'storage/') === 0 || strpos($this->image_path, 'public/') === 0) {
            return asset($this->image_path);
        }
        
        // Default case - prepend storage path
        return asset('storage/' . $this->image_path);
    }
}
