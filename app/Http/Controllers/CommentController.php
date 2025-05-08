<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Store a new comment
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $post = Post::findOrFail($postId);

        // Create the comment
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Load the user relationship
        $comment->load('user');

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            // Prepare HTML for the new comment to insert via JavaScript
            $commentHtml = view('partials.comment', ['comment' => $comment])->render();
            
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'html' => $commentHtml
            ]);
        }

        return redirect()->route('community')->with('success', 'Comment added successfully!');
    }

    public function destroy($comment)
    {
        // Find the comment
        $comment = Comment::findOrFail($comment);

        // Check if the authenticated user is the one who created the comment
        if ($comment->user_id !== auth()->id()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this comment.'
                ], 403);
            }
            return redirect()->route('community')->with('error', 'You are not authorized to delete this comment.');
        }

        // Delete the comment
        $comment->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully!'
            ]);
        }

        return redirect()->route('community')->with('success', 'Comment deleted successfully!');
    }
}
