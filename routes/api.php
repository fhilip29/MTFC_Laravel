use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User attendance data
Route::get('/user/attendance', [\App\Http\Controllers\API\AttendanceController::class, 'getUserAttendance']);

// Unread messages count for authenticated user
Route::middleware('auth')->get('/user/unread-messages-count', [\App\Http\Controllers\MessageController::class, 'getUnreadCount']);

// Mark message as read
Route::middleware('auth')->post('/user/messages/{id}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead']); 