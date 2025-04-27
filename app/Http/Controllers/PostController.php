<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Display all posts in the community feed
    public function index()
    {
        $posts = Post::with(['images', 'likes'])->latest()->get(); // Load posts with associated images and likes
        return view('community', compact('posts'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $posts = Post::where('content', 'like', "%{$query}%")->get();
        return view('community', compact('posts'));
    }

    // Store a new post (including content and images)
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:500',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // image validation
        ]);

        // Create the post
        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public'); // Store images in the "posts" folder
                PostImage::create([
                    'post_id' => $post->id,
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('community')->with('success', 'Post created successfully!');
    }

    // Like a post
    public function like(Post $post)
{
    // Check if the user has already liked this post
    if ($post->likedByUsers->contains(auth()->user())) {
        // If the user has already liked the post, you can either return a message or just redirect
        return back()->with('message', 'You already liked this post.');
    }

    // Attach the logged-in user to the post likes
    $post->likedByUsers()->attach(auth()->id());

    return back();
}

    

    // Show a single post (view comments, likes, etc.)
    public function show($id)
{
    $post = Post::with('likes')->find($id);
    return view('posts.show', compact('post'));
}

}

