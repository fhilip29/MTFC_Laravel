<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminMemberController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\EquipmentMaintenanceController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\FileUploadController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileDebugController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminSessionController;

// ========================
// PUBLIC/HOME ROUTES
// ========================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ===================
// AUTH ROUTES
// ===================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup.form');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Fallback for logout if POST request fails
Route::get('/logout', [AuthController::class, 'logout']);

// Password Reset Routes
Route::view('/forgot-password', 'auth.forgot_password')->name('forgot_password');
Route::post('/password/email', [PasswordResetController::class, 'sendResetCode'])->name('password.email');
Route::post('/password/verify-code', [PasswordResetController::class, 'verifyCode'])->name('password.verify');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');

// Google Login Routes
Route::get('/auth/google', [\App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\GoogleController::class, 'handleGoogleCallback']);

// ===================
// HEADER BTNS ROUTES
// ===================
Route::view('/about', 'about')->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/trainers', [TrainerController::class, 'indexUser'])->name('trainers');

// ===================
// PRICING ROUTES
// ===================
Route::get('/pricing/gym', [PricingController::class, 'gym'])->name('pricing.gym');
Route::get('/pricing/boxing', [PricingController::class, 'boxing'])->name('pricing.boxing');
Route::get('/pricing/muay', [PricingController::class, 'muay'])->name('pricing.muay');
Route::get('/pricing/jiu-jitsu', [PricingController::class, 'jiu'])->name('pricing.jiu');

// ===================
// TERMS / POLICIES
// ===================
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacypolicy', 'privacy')->name('privacy');

// ===================
// COMMUNITY ROUTES
// ===================
Route::get('/community', [PostController::class, 'index'])->name('community'); // View all posts
Route::get('/community/search', [PostController::class, 'search'])->name('community.search'); // Search posts
Route::get('/community/tag/{tag}', [PostController::class, 'byTag'])->name('community.tag'); // Filter by tag

// Post-related routes
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create'); // Form for creating post
Route::post('/posts', [PostController::class, 'store'])->name('posts.store'); // Store new post
Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');// Like a post
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show'); // View a single post with comments

// Comment-related routes
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store'); // Add a comment to a post
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy'); // Delete a comment

// Delete post route
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy'); // Delete a post

// Public user announcements
Route::get('/announcements', [AnnouncementController::class, 'userIndex'])->name('announcements');
Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');

// QR Scanner Test Route
Route::get('/test-scanner', function () {
    return view('test-scanner');
});

// ===================
// PAYMENT ROUTES
// ===================
Route::get('/payment-method', [PaymentController::class, 'showPaymentMethods'])->name('payment-method');
Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::post('/payment/cash/qr', [PaymentController::class, 'generateQRCode'])->name('payment.cash.qr');
Route::get('/payment/cash/qr/{reference}', [PaymentController::class, 'showCashQr'])->name('payment.cash.qr.show');
Route::post('/payment/cash/verify', [PaymentController::class, 'verifyCashPayment'])->name('payment.cash.verify');
Route::post('/payment/process-cash', [PaymentController::class, 'processCashPayment'])->name('payment.process.cash');

// ===================
// CART ROUTES
// ===================
Route::get('/cart', [CartController::class, 'showCart'])->name('cart');
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');

// File Upload Routes for FilePond
Route::post('/upload', [FileUploadController::class, 'process'])->name('upload.process');
Route::delete('/upload', [FileUploadController::class, 'revert'])->name('upload.revert');
Route::get('/upload/{uniqueId}', [FileUploadController::class, 'load'])->name('upload.load');

// SMTP Test Page (only available in local/development environment)
if (app()->environment(['local', 'development'])) {
    Route::view('/test-smtp', 'test-smtp')->name('test.smtp');
    Route::post('/test-smtp-send', function(Illuminate\Http\Request $request) {
        try {
            $request->validate([
                'email' => 'required|email',
                'subject' => 'required|string',
                'message' => 'required|string'
            ]);
            
            $mailConfig = [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
                'username' => config('mail.mailers.smtp.username') ? '(set)' : '(not set)',
                'password' => config('mail.mailers.smtp.password') ? '(set)' : '(not set)',
            ];
            
            // Log the attempt
            \Log::info('SMTP Test Email Request', [
                'to' => $request->email,
                'subject' => $request->subject,
                'config' => $mailConfig
            ]);
            
            $startTime = microtime(true);
            
            // Try to send the email
            \Illuminate\Support\Facades\Mail::raw($request->message, function($message) use ($request) {
                $message->to($request->email)
                        ->subject($request->subject);
            });
            
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2); // in milliseconds
            
            \Log::info('SMTP Test Email Success', [
                'to' => $request->email,
                'duration_ms' => $duration
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->email,
                'config' => $mailConfig,
                'duration_ms' => $duration
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            \Log::warning('SMTP Test Validation Error', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log the detailed error
            \Log::error('SMTP Test Email Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check for common SMTP errors and provide helpful messages
            $errorMessage = $e->getMessage();
            $errorDetails = null;
            
            if (strpos($errorMessage, 'Connection could not be established') !== false) {
                $errorDetails = 'Could not connect to the mail server. Please check host and port settings.';
            } elseif (strpos($errorMessage, 'Expected response code') !== false) {
                $errorDetails = 'Authentication failed. Please check your username and password.';
            } elseif (strpos($errorMessage, 'Incorrect username') !== false || strpos($errorMessage, 'Incorrect password') !== false) {
                $errorDetails = 'SMTP authentication failed. Check your credentials.';
            } elseif (strpos($errorMessage, 'ssl') !== false || strpos($errorMessage, 'tls') !== false) {
                $errorDetails = 'SSL/TLS encryption issue. Try changing MAIL_ENCRYPTION in your .env file.';
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $errorMessage,
                'error' => $errorMessage,
                'error_details' => $errorDetails,
                'config' => $mailConfig,
                'php_version' => PHP_VERSION,
                'server_info' => [
                    'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
                    'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'unknown',
                ]
            ], 500);
        }
    });
}

// ===================
// API ROUTES (public)
// ===================
Route::get('/api/announcements/{announcement}', [AnnouncementController::class, 'apiShow'])->name('api.announcements.show');

// ===================
// AUTHENTICATED ROUTES (all users)
// ===================
Route::middleware(['auth'])->group(function() {
    // API Routes
    Route::get('/api/user/attendance', [ProfileController::class, 'getUserAttendance'])->name('api.user.attendance');
    Route::get('/api/invoice/{id}/items', [InvoiceController::class, 'getInvoiceItems'])->name('api.invoice.items');
    Route::get('/api/user/attendance-dates', [ProfileController::class, 'getAttendanceDates'])->name('api.user.attendance.dates');
    
    // Account Settings Routes (accessible to all authenticated users)
    Route::get('/account-settings', [AccountController::class, 'index'])->name('account.settings');
    Route::post('/account-settings/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::post('/account-settings/password', [AccountController::class, 'updatePassword'])->name('password.update');
    
    // QR Code Routes (needed by both members and trainers)
    Route::get('/profile/qr', [ProfileController::class, 'showQrCode'])->name('profile.qr');
    Route::get('/my-qr', [ProfileController::class, 'showQrCode'])->name('user.qr');
    Route::get('/qr-image', [ProfileController::class, 'generateQrImage'])->name('user.qr.image');
    
    // Message Routes (needed by both members and trainers)
    Route::get('/messages', [MessageController::class, 'index'])->name('user.messages');
    Route::get('/messages/compose', [MessageController::class, 'compose'])->name('user.messages.compose');
    Route::get('/messages/sent', [MessageController::class, 'sent'])->name('user.messages.sent');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('user.messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('user.messages.store');
    Route::post('/messages/{id}/reply', [MessageController::class, 'reply'])->name('user.messages.reply');
    Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead'])->name('user.messages.read');
    
    // Cart Routes
    Route::get('/cart/get', [CartController::class, 'getCart'])->name('cart.get');
    Route::post('/cart/sync', [CartController::class, 'syncCart'])->name('cart.sync');

     // Order routes
     Route::get('/orders', [OrderController::class, 'index'])->name('orders');
     Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
     Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
     Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// ===================
// AUTHENTICATED USER ROUTES
// ===================
Route::middleware(['auth', RoleMiddleware::class . ':member'])->group(function () {
    // User profile and dashboard
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/settings', [AccountController::class, 'index'])->name('profile.settings');
    Route::view('/notifications', 'notifications')->name('notifications');
    Route::get('/user/attendance', [ProfileController::class, 'showAttendanceDetails'])->name('user.attendance');
    
    // Debug routes
    Route::get('/debug/profile-image', [ProfileDebugController::class, 'debugProfileImage']);
    Route::get('/debug/invoice-receipt', [ProfileDebugController::class, 'debugInvoiceReceipt']);
    Route::get('/debug/qr-code', [ProfileController::class, 'debugQrCode']);
    
    
    // Invoice routes
    Route::get('/invoices/{id}', [InvoiceController::class, 'userShow'])->name('user.invoices.show');
    Route::get('/my-invoice/{id}/receipt', [InvoiceController::class, 'userShowReceipt'])->name('user.invoices.receipt');
    Route::get('/my-invoices', [InvoiceController::class, 'userInvoices'])->name('user.invoices');
    Route::get('/my-payments', [InvoiceController::class, 'userInvoices'])->name('user.payments');
    Route::get('/my-payment/{id}/receipt', [InvoiceController::class, 'userShowReceipt'])->name('user.payments.receipt');
    Route::get('/my-payment/{id}/details', [InvoiceController::class, 'userInvoiceDetails'])->name('user.payment.details');
    
    // Subscription routes
    Route::post('/subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
    Route::get('/subscription/history', [SubscriptionController::class, 'history'])->name('subscription.history');
    Route::post('/subscription/{id}/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
});

// ===================
// TRAINER ROUTES
// ===================
Route::middleware(['auth', RoleMiddleware::class . ':trainer'])->group(function () {
    Route::get('/trainer/dashboard', function () {
        return view('trainer.dashboard');
    })->name('trainer.dashboard');
    Route::get('/trainer/profile', [TrainerController::class, 'showProfile'])->name('trainer.profile');
});

// ===================
// ADMIN ROUTES
// ===================
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Admin Profile
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('admin.profile');
    Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    
    // Members Management
    Route::get('/members/admin_members', [AdminMemberController::class, 'index'])->name('admin.members.admin_members');
    Route::get('/members/{id}', [AdminMemberController::class, 'show'])->name('admin.members.show');
    Route::get('/members/{user}/subscriptions', [AdminMemberController::class, 'manageMemberSubscriptions'])->name('admin.members.subscriptions');
    Route::post('/members/{user}/subscriptions', [AdminMemberController::class, 'storeSubscription'])->name('admin.members.subscriptions.store');
    Route::put('/members/{user}/subscriptions/{subscription}', [AdminMemberController::class, 'updateSubscription'])->name('admin.members.subscriptions.update');
    Route::post('/members/{user}/subscriptions/{subscription}/cancel', [AdminMemberController::class, 'cancelSubscription'])->name('admin.members.subscriptions.cancel');
    Route::post('/members/{user}/subscriptions/{subscription}/add-sessions', [AdminMemberController::class, 'addSessions'])->name('admin.members.subscriptions.add-sessions');
    Route::post('/members/{user}/archive', [AdminMemberController::class, 'archiveMember'])->name('admin.members.archive');
    
    // Session Management
    Route::get('/session/admin_session', [SessionController::class, 'index'])->name('admin.session.admin_session');
    Route::post('/sessions/store', [SessionController::class, 'store'])->name('admin.session.store');
    Route::post('/sessions/check-status', [SessionController::class, 'checkStatus'])->name('admin.session.check-status');
    Route::post('/sessions/check-guest-status', [AdminSessionController::class, 'checkGuestStatus'])->name('admin.session.check-guest-status');
    Route::get('/sessions/guest-list', [AdminSessionController::class, 'getCheckedInGuests'])->name('admin.session.guests');
    
    // Invoice Management
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('admin.invoice.invoice');
    Route::get('/invoice/export', [InvoiceController::class, 'export'])->name('admin.invoice.export');
    Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->name('admin.invoice.show');
    Route::get('/invoice/{id}/print', [InvoiceController::class, 'printReceipt'])->name('admin.invoice.print');
    
    // Trainer Management
    Route::get('/trainer/admin_trainer', [TrainerController::class, 'indexAdmin'])->name('admin.trainer.admin_trainer');
    Route::post('/trainers', [TrainerController::class, 'store'])->name('admin.trainers.store');
    Route::get('/trainers/{id}/edit', [TrainerController::class, 'edit'])->name('admin.trainers.edit');
    Route::put('/trainers/{id}', [TrainerController::class, 'update'])->name('admin.trainers.update');
    Route::post('/trainers/{id}/archive', [TrainerController::class, 'archive'])->name('admin.trainers.archive');
    
    // Announcement Management
    Route::get('/promotion/admin_promo', [AnnouncementController::class, 'adminIndex'])->name('admin.promotion.admin_promo');
    Route::get('/announcements', [AnnouncementController::class, 'adminIndex'])->name('admin.announcements');
    Route::get('/announcements/{id}', [AnnouncementController::class, 'show'])->name('admin.announcements.show');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('admin.announcements.store');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('admin.announcements.update');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');
    Route::patch('/announcements/{id}/toggle-active', [AnnouncementController::class, 'toggleActive'])->name('admin.announcements.toggleActive');
    Route::view('/announcement', 'admin.announcement.admin_announcement')->name('admin.announcement');
    
    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.orders');
    Route::get('/orders/admin_orders', [AdminOrderController::class, 'index'])->name('admin.orders.admin_orders');
    Route::get('/orders/{id}/details', [AdminOrderController::class, 'showDetails'])->name('admin.orders.details');
    Route::post('/orders/{id}/update-status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    
    // Product Management
    Route::get('/products', [ProductController::class, 'index'])->name('admin.product.products');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.product.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.product.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.product.destroy');
    
    // Equipment Management
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('admin.gym.gym');
    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])->name('admin.gym.equipment.show');
    Route::post('/equipment', [EquipmentController::class, 'store'])->name('admin.gym.equipment.store');
    Route::put('/equipment/{equipment}', [EquipmentController::class, 'update'])->name('admin.gym.equipment.update');
    Route::delete('/equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('admin.gym.equipment.destroy');
    
    // Equipment Maintenance
    Route::get('/equipment/maintenance/logs', [EquipmentMaintenanceController::class, 'index'])->name('admin.gym.maintenance');
    Route::post('/equipment/maintenance', [EquipmentMaintenanceController::class, 'store'])->name('admin.gym.maintenance.store');
    Route::delete('/equipment/maintenance/{maintenance}', [EquipmentMaintenanceController::class, 'destroy'])->name('admin.gym.maintenance.destroy');
    
    // Vendor Management
    Route::get('/vendors', [VendorController::class, 'index'])->name('admin.gym.vendors');
    Route::post('/vendors', [VendorController::class, 'store'])->name('admin.gym.vendors.store');
    Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('admin.gym.vendors.show');
    Route::put('/vendors/{vendor}', [VendorController::class, 'update'])->name('admin.gym.vendors.update');
    Route::delete('/vendors/{vendor}', [VendorController::class, 'destroy'])->name('admin.gym.vendors.destroy');
    
    // Admin Messages
    Route::get('/messages', [AdminController::class, 'getMessages'])->name('admin.messages');
    Route::get('/messages/{id}', [AdminController::class, 'showMessage'])->name('admin.messages.show');
    Route::post('/messages/{id}/reply', [AdminController::class, 'replyToMessage'])->name('admin.messages.reply');
    
    // Payment Verification
    Route::post('/verify-payment', [AdminController::class, 'verifyPayment'])->name('admin.verify.payment');
});


