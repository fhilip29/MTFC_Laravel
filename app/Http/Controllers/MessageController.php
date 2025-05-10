<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MessageController extends Controller
{
    /**
     * Display a listing of the user's messages
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all messages for this user (sent and received)
        $receivedMessages = Message::where('recipient_id', $user->id)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $sentMessages = Message::where('sender_id', $user->id)
            ->with('recipient')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('messages.index', compact('receivedMessages', 'sentMessages'));
    }
    
    /**
     * Display a listing of the user's sent messages
     *
     * @return \Illuminate\View\View
     */
    public function sent()
    {
        $user = Auth::user();
        
        // Get sent messages for this user
        $sentMessages = Message::where('sender_id', $user->id)
            ->with('recipient')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('messages.sent', compact('sentMessages'));
    }
    
    /**
     * Display the specified message
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        $message = Message::with(['sender', 'recipient', 'replies.sender'])
            ->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('recipient_id', $user->id);
            })
            ->findOrFail($id);
        
        // If this is a received message and it's unread, mark it as read
        if ($message->recipient_id == $user->id && !$message->is_read) {
            $message->is_read = true;
            $message->read_at = Carbon::now();
            $message->save();
        }
        
        return view('messages.show', compact('message'));
    }
    
    /**
     * Store a newly created message
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        $message = new Message();
        $message->sender_id = Auth::id();
        $message->recipient_id = $request->recipient_id;
        $message->subject = $request->subject;
        $message->content = $request->content;
        $message->save();
        
        return redirect()->route('user.messages')->with('success', 'Message sent successfully!');
    }
    
    /**
     * Reply to a message
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        
        $originalMessage = Message::findOrFail($id);
        
        // Create reply message
        $reply = new Message();
        $reply->sender_id = Auth::id();
        // Send reply to the original sender
        $reply->recipient_id = $originalMessage->sender_id;
        $reply->subject = 'RE: ' . $originalMessage->subject;
        $reply->content = $request->content;
        $reply->parent_id = $originalMessage->id;
        $reply->save();
        
        return redirect()->route('user.messages.show', $id)->with('success', 'Reply sent successfully!');
    }
    
    /**
     * Mark a message as read via AJAX
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $message = Message::where('recipient_id', $user->id)
                    ->where('id', $id)
                    ->first();
                    
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found or you do not have permission to view it'
            ], 404);
        }
        
        if (!$message->is_read) {
            $message->is_read = true;
            $message->read_at = Carbon::now();
            $message->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Message marked as read'
        ]);
    }
    
    /**
     * Get the count of unread messages for the authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->receivedMessages()->where('is_read', false)->count();
        
        return response()->json([
            'count' => $count,
            'success' => true
        ]);
    }
    
    /**
     * Show the compose message form
     */
    public function compose(Request $request)
    {
        // Get users who can be recipients (everyone except current user)
        $users = User::where('id', '!=', Auth::id())->get();
        
        // Get members, trainers and admins for categorized display
        $members = $users->where('role', 'member');
        $trainers = $users->where('role', 'trainer');
        $admins = $users->where('role', 'admin');
        
        // Pre-select recipient if provided in URL
        $preSelectedRecipient = null;
        
        if ($request->has('recipient_id')) {
            $preSelectedRecipient = User::find($request->recipient_id);
        } elseif ($request->has('admin') && $request->admin == true) {
            // Get the first admin user if admin parameter is true
            $preSelectedRecipient = User::where('role', 'admin')->first();
        }
        
        return view('messages.compose', compact('members', 'trainers', 'admins', 'preSelectedRecipient'));
    }
} 