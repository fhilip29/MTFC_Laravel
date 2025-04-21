<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    // Explicitly set the table name to match the migration
    protected $table = 'vendors';
    
    protected $fillable = ['name', 'contact_info'];

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}