<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Store a new comment
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = Auth::id();
        $comment->post_id = $post->id;
        $comment->save();

        return redirect()->route('posts.show', $post->id);
    }

    // Delete a comment
    public function destroy(Comment $comment)
    {
        // Ensure that the authenticated user is the owner of the comment
        if (Auth::id() === $comment->user_id) {
            $comment->delete();
        }

        return redirect()->route('posts.show', $comment->post_id);
    }
}
