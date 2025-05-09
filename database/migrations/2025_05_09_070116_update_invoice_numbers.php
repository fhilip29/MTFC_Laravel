<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all invoices that don't start with INV-
        $invoices = DB::table('invoices')
            ->whereRaw('invoice_number NOT LIKE "INV-%"')
            ->get();

        foreach ($invoices as $invoice) {
            // Generate a new invoice number in the standard format
            $newInvoiceNumber = 'INV-' . strtoupper(Str::random(8));
            
            // Update the invoice
            DB::table('invoices')
                ->where('id', $invoice->id)
                ->update(['invoice_number' => $newInvoiceNumber]);
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Note: This is not easily reversible since we're changing
     * the format of existing invoice numbers.
     */
    public function down(): void
    {
        // Cannot reliably revert this change
    }
};
