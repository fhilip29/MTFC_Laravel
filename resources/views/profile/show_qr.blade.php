@extends('layouts.app')

@section('title', 'My QR Code')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-xl shadow-lg text-center border border-gray-200 max-w-md w-full">
        <div class="mb-6">
            <a href="{{ $role === 'trainer' ? route('trainer.profile') : route('profile') }}" class="text-sm text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-1"></i> Back to Profile
            </a>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Your Check-In QR Code</h1>
        <div class="bg-gray-50 p-6 rounded-lg inline-block mb-6 border border-gray-200">
            <div class="w-64 h-64 sm:w-72 sm:h-72">
                {!! QrCode::size(280)->generate($user->qr_code) !!}
            </div>
        </div>
        <p class="text-gray-600 text-sm px-4">
            {{ $role === 'trainer' ? 'Show this QR code at the gym entrance to record your attendance as a trainer.' : 'Show this QR code at the gym entrance to check in or out.' }}
        </p>
        <div class="mt-4 flex items-center justify-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                <i class="fas fa-user-{{ $role === 'trainer' ? 'tie' : 'check' }} mr-2"></i>
                {{ ucfirst($role) }}
            </span>
        </div>
    </div>
</div>
@endsection 