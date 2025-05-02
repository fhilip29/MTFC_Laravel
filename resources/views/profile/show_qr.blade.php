@extends('layouts.app')

@section('title', 'My QR Code')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-[#121212]">
    <div class="bg-[#1e1e1e] p-8 rounded-xl shadow-lg text-center border border-gray-700 max-w-md w-full">
        <div class="mb-6">
            <a href="{{ route('profile') }}" class="text-sm text-blue-400 hover:text-blue-300">&larr; Back to Profile</a>
        </div>
        <h1 class="text-2xl font-bold text-white mb-4">Your Check-In QR Code</h1>
        <div class="bg-white p-6 rounded-lg inline-block mb-6">
            <div class="w-64 h-64 sm:w-72 sm:h-72">
                {!! QrCode::size(280)->generate($user->qr_code) !!}
            </div>
        </div>
        <p class="text-gray-400 text-sm px-4">Show this QR code at the gym entrance to check in or out.</p>
    </div>
</div>
@endsection 