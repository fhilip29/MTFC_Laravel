<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Display all posts in the community feed
    public function index(Request $request)
    {
        // Check if we're filtering by user
        $userId = $request->input('user');
        
        // Get sort parameter
        $sort = $request->input('sort', 'latest');
        
        // Start with base query
        $postsQuery = Post::with(['user', 'images', 'likes', 'comments.user', 'tags']);
        
        // Filter by user if requested
        if ($userId) {
            $postsQuery->where('user_id', $userId);
        }
        
        // Apply sorting
        if ($sort === 'popular') {
            $postsQuery->withCount('likes')
                      ->orderBy('likes_count', 'desc')
                      ->orderBy('created_at', 'desc');
        } else {
            $postsQuery->latest();
        }
        
        // Get posts
        $posts = $postsQuery->get();
        
        // Get all available tags for the sidebar and post creation
        $tags = PostTag::all();
        
        // Get the current authenticated user for highlighting in "My Posts" tab
        $currentUserId = $userId;
        
        // Pass posts to the community view
        return view('community', compact('posts', 'tags', 'currentUserId'));
    }

    // Search for posts based on content or user name
    public function search(Request $request)
    {
        // Get the search query and filters from the input
        $query = $request->input('query');
        $tag = $request->input('tag');
        $userId = $request->input('user');

        // Start with base query
        $postsQuery = Post::with(['user', 'images', 'likes', 'comments.user', 'tags']);
        
        // If tag is provided, filter by tag
        if ($tag) {
            $postsQuery->whereHas('tags', function($q) use ($tag) {
                $q->where('slug', $tag);
            });
        }
        
        // If user ID is provided, filter by user
        if ($userId) {
            $postsQuery->where('user_id', $userId);
        }
        
        // If search query is provided, filter by content or user name
        if ($query) {
            $postsQuery->where(function($q) use ($query) {
                $q->where('content', 'like', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('full_name', 'like', "%{$query}%")
                               ->orWhere('email', 'like', "%{$query}%");
                  });
            });
        }
        
        // Get posts sorted by newest first
        $posts = $postsQuery->latest()->get();
        
        // Get all tags for sidebar and post creation
        $tags = PostTag::all();
        
        // Get the current authenticated user for highlighting in "My Posts" tab
        $currentUserId = $userId;

        // Return the community view with the filtered posts
        return view('community', compact('posts', 'tags', 'currentUserId'));
    }

    // Store a new post (with optional images)
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'content' => 'required|string|max:500',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:post_tags,id'
        ]);

        // Create the post
        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);
        
        // Attach tags if provided
        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }

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

    public function destroy(Post $post)
    {
        // Ensure the user is the owner of the post
        if (auth()->id() !== $post->user_id) {
            return redirect()->route('community')->with('error', 'You can only delete your own posts.');
        }

        // Delete associated images if they exist
        foreach ($post->images as $image) {
            if (file_exists(public_path('storage/' . $image->path))) {
                unlink(public_path('storage/' . $image->path));
            }
            $image->delete();
        }

        // Delete the post
        $post->delete();

        return redirect()->route('community')->with('success', 'Post deleted successfully.');
    }

    // Like a post
    public function like(Post $post)
    {
        $user = auth()->user();

        // Check if the user has already liked the post
        $existingLike = $post->likes()->where('user_id', $user->id)->first();
        
        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $action = 'unliked';
        } else {
            // Like
            $post->likes()->create(['user_id' => $user->id]);
            $action = 'liked';
        }

        // Get fresh count
        $likesCount = $post->likes()->count();

        // If request is AJAX, return JSON response
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'action' => $action,
                'likes_count' => $likesCount
            ]);
        }

        return redirect()->back()->with('success', 'Post ' . $action . ' successfully.');
    }

    // Show a single post with likes (optional: comments if implemented)
    public function show($id)
    {
        $post = Post::with(['images', 'likedByUsers'])->findOrFail($id);
        return view('community', compact('post'));
    }

    // Filter posts by tag
    public function byTag($tag)
    {
        // Load posts with the specified tag
        $posts = Post::with(['user', 'images', 'likes', 'comments.user', 'tags'])
            ->whereHas('tags', function($query) use ($tag) {
                $query->where('slug', $tag);
            })
            ->latest()
            ->get();
        
        // Get all available tags for the sidebar and post creation
        $tags = PostTag::all();
        
        // Pass posts to the community view along with the active tag
        return view('community', compact('posts', 'tags', 'tag'));
    }
}