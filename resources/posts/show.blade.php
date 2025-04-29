@extends('layouts.app')

@section('title', 'Post Details')

@section('content')
    <div class="p-6 text-white">
        <h1 class="text-2xl font-bold mb-4">Post by {{ $post->user->full_name }}</h1>
        <p>{{ $post->content }}</p>

        <div class="mt-4">
            <h2 class="text-xl mb-2">Comments ({{ $post->comments->count() }})</h2>
            @foreach ($post->comments as $comment)
                <div class="bg-gray-800 p-3 rounded mb-2">
                    <strong>{{ $comment->user->full_name }}</strong> - {{ $comment->created_at->diffForHumans() }}
                    <p>{{ $comment->content }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
