<!-- resources/views/posts/create.blade.php -->
@extends('layouts.app')

@section('title', 'Create a New Post')

@section('content')
<div class="container mt-6">
    <h1 class="text-xl font-semibold mb-4">Create a New Post</h1>
    
    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-4">
            <label for="content" class="block text-sm">Content</label>
            <textarea name="content" id="content" class="form-control w-full" rows="4" placeholder="What's on your mind?" required></textarea>
        </div>

        <div class="form-group mb-4">
            <label for="images" class="block text-sm">Attach Image(s)</label>
            <input type="file" name="images[]" class="form-control" multiple>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">Post</button>
        </div>
    </form>
</div>
@endsection
