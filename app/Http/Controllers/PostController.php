<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Display all posts in the community feed
    public function index()
{
    // Load posts along with their relationships (user, images, likes, and comments)
    $posts = Post::with(['user', 'images', 'likes', 'comments.user'])->latest()->get();
    
    // Pass posts to the community view
    return view('community', compact('posts'));
}
    // Search for posts based on content
    public function search(Request $request)
    {
        $query = $request->input('query');
        $posts = Post::where('content', 'like', "%{$query}%")->with(['images', 'likedByUsers'])->get();
        return redirect()->route('community')->with('success', 'Comment added!');

    }

    // Store a new post (with optional images)
    public function store(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'content' => 'required|string|max:500',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Create the post
    $post = Post::create([
        'user_id' => Auth::id(),
        'content' => $request->content,
    ]);

    // Handle image uploads for the post
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('posts', 'public');
            PostImage::create([
                'post_id' => $post->id,
                'path' => $path,
            ]);
        }
    }

    // Redirect to community page with a success message
    return redirect()->route('community')->with('success', 'Post created successfully!');
}


    // Like a post
    public function like(Post $post)
{
    // Prevent duplicate likes
    if ($post->likedByUsers()->where('user_id', auth()->id())->exists()) {
        return back()->with('message', 'You already liked this post.');
    }

    $post->likedByUsers()->attach(auth()->id());

    return back();
}


    // Show a single post with likes (optional: comments if implemented)
    public function show($id)
    {
        $post = Post::with(['images', 'likedByUsers'])->findOrFail($id);
        return view('community', compact('post'));
    }
}
