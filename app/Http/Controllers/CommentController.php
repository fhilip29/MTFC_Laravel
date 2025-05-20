<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Services\ContentScreeningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected $contentScreening;

    public function __construct(ContentScreeningService $contentScreening)
    {
        $this->contentScreening = $contentScreening;
    }

    // Store a new comment
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        // Screen content for inappropriate material
        $contentIssues = $this->contentScreening->screenContent($request->content);
        if (!empty($contentIssues)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $contentIssues
                ], 422);
            }
            return back()->withErrors(['content' => $contentIssues]);
        }

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
        $comment = Comment::findOrFail($comment);
        
        // Ensure the user is the owner of the comment
        if (auth()->id() !== $comment->user_id) {
            return redirect()->route('community')->with('error', 'You can only delete your own comments.');
        }

        $comment->delete();

        return redirect()->route('community')->with('success', 'Comment deleted successfully.');
    }
}
