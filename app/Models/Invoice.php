<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'type',
        'total_amount',
        'invoice_date',
        'payment_status',
        'payment_method',
        'payment_reference',
        'subscription_id',
        'paid_at',
        'amount',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the subscription associated with the invoice.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
