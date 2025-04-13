@extends('layouts.app')

@section('content')
<!-- Add Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="min-h-screen bg-gray-100 py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Account Settings</h1>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form class="space-y-6">
                <!-- Profile Picture -->
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200">
                        <img id="preview-image" src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="Profile Picture" class="w-full h-full object-cover">
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="bg-blue-500 text-white px-4 py-2 rounded-full cursor-pointer hover:bg-blue-600 transition-colors">
                            <span>Choose File</span>
                            <input type="file" class="hidden" accept="image/*" onchange="previewImage(event)">
                        </label>
                        <span class="text-sm text-gray-500">Image (4MB)</span>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                        <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fitness Goal</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            <option value="">Select Goal</option>
                            <option value="weight_loss">Weight Loss</option>
                            <option value="muscle_gain">Build Muscle</option>
                            <option value="endurance">Improve Endurance</option>
                            <option value="flexibility">Increase Flexibility</option>
                        </select>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-full hover:bg-red-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection