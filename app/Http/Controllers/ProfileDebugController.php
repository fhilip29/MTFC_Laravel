<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Invoice;
use App\Models\User;

class ProfileDebugController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Debug profile image display
     *
     * @return \Illuminate\Http\Response
     */
    public function debugProfileImage()
    {
        $user = Auth::user();
        $data = [
            'user_id' => $user->id,
            'profile_image_path' => $user->profile_image,
            'profile_image_exists' => $user->profile_image ? file_exists(public_path($user->profile_image)) : false,
            'public_path' => public_path($user->profile_image ?? ''),
            'asset_path' => asset($user->profile_image ?? ''),
            'storage_path' => $user->profile_image ? Storage::url($user->profile_image) : null,
            'public_directories' => [
                'images_dir_exists' => file_exists(public_path('images')),
                'users_dir_exists' => file_exists(public_path('images/users')),
                'trainer_dir_exists' => file_exists(public_path('images/trainer')),
                'admin_dir_exists' => file_exists(public_path('images/admin')),
            ]
        ];
        
        return response()->json($data);
    }
    
    /**
     * Debug invoice receipt display
     *
     * @return \Illuminate\Http\Response
     */
    public function debugInvoiceReceipt()
    {
        $user = Auth::user();
        $invoices = Invoice::where('user_id', $user->id)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $data = [
            'user_id' => $user->id,
            'invoices_count' => $invoices->count(),
            'invoices' => $invoices->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'type' => $invoice->type,
                    'total_amount' => $invoice->total_amount,
                    'invoice_date' => $invoice->invoice_date,
                    'items_count' => $invoice->items->count(),
                    'receipt_url' => route('user.invoices.receipt', $invoice->id)
                ];
            })
        ];
        
        return response()->json($data);
    }
} 