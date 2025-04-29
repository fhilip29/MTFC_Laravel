<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Types\Model\Comments;

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
    Comment::create([
        'post_id' => $post->id,
        'user_id' => Auth::id(),
        'content' => $request->content,
    ]);

    return redirect()->route('community')->with('success', 'Comment added successfully!');
}

public function destroy($comment)
{
    // Find the comment
    $comment = Comment::findOrFail($comment);

    // Check if the authenticated user is the one who created the comment
    if ($comment->user_id !== auth()->id()) {
        return redirect()->route('community')->with('error', 'You are not authorized to delete this comment.');
    }

    // Delete the comment
    $comment->delete();

    return redirect()->route('community')->with('success', 'Comment deleted successfully!');
}

}
