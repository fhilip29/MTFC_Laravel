@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<section class="bg-gradient-to-b from-gray-800 to-gray-900 min-h-screen py-12 pb-24 px-4 md:px-36">
    <div class="max-w-5xl mx-auto">
        <!-- Header with animated elements -->
        <div class="text-center mb-12 animate-fade-in-down">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Get in Touch</h1>
            <p class="text-gray-300 text-lg max-w-2xl mx-auto">Have questions about our gym or services? Our team is ready to help you with any inquiries you might have.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Contact Information -->
            <div class="md:col-span-1">
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6 border border-gray-700 text-white h-full">
                    <h2 class="text-xl font-bold mb-6 text-white border-b border-gray-700 pb-3">Contact Information</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-gray-700 p-3 rounded-full mr-4">
                                <i class="fas fa-map-marker-alt text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-blue-400">Address</h3>
                                <p class="text-gray-300 mt-1">123 Fitness Avenue, Manila, Philippines</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-gray-700 p-3 rounded-full mr-4">
                                <i class="fas fa-phone text-green-400"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-green-400">Phone</h3>
                                <p class="text-gray-300 mt-1">+63 (2) 8123 4567</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-gray-700 p-3 rounded-full mr-4">
                                <i class="fas fa-envelope text-red-400"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-red-400">Email</h3>
                                <p class="text-gray-300 mt-1">info@mtfc.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-gray-700 p-3 rounded-full mr-4">
                                <i class="fas fa-clock text-yellow-400"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-yellow-400">Working Hours</h3>
                                <p class="text-gray-300 mt-1">Monday - Friday: 6am - 10pm</p>
                                <p class="text-gray-300">Saturday - Sunday: 8am - 8pm</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="mt-8 pt-6 border-t border-gray-700">
                        <h3 class="font-semibold mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="bg-gray-700 hover:bg-blue-600 p-3 rounded-full text-white transition-all duration-300">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="bg-gray-700 hover:bg-pink-600 p-3 rounded-full text-white transition-all duration-300">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="bg-gray-700 hover:bg-red-600 p-3 rounded-full text-white transition-all duration-300">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" class="bg-gray-700 hover:bg-blue-400 p-3 rounded-full text-white transition-all duration-300">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="md:col-span-2" x-data="contactForm()">
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6 md:p-8 border border-gray-700">
                    <h2 class="text-2xl font-bold text-white border-b border-gray-700 pb-3 mb-6">Send Us a Message</h2>
                    
                    <!-- Form -->
                    <form @submit.prevent="handleSubmit" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block mb-1 font-semibold text-white">Full Name</label>
                                <input 
                                    type="text" 
                                    x-model="form.fullName" 
                                    placeholder="Your name"
                                    class="w-full border border-gray-600 bg-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    readonly
                                >
                                <template x-if="errors.fullName">
                                    <p class="text-red-500 text-sm mt-1" x-text="errors.fullName"></p>
                                </template>
                            </div>

                            <div>
                                <label class="block mb-1 font-semibold text-white">Email</label>
                                <input 
                                    type="email" 
                                    x-model="form.email" 
                                    placeholder="Your email"
                                    class="w-full border border-gray-600 bg-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    readonly
                                >
                                <template x-if="errors.email">
                                    <p class="text-red-500 text-sm mt-1" x-text="errors.email"></p>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block mb-1 font-semibold text-white">Phone Number</label>
                                <input 
                                    type="text" 
                                    x-model="form.phoneNumber" 
                                    placeholder="+63 9XX XXX XXXX"
                                    class="w-full border border-gray-600 bg-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    readonly
                                >
                                <template x-if="errors.phoneNumber">
                                    <p class="text-red-500 text-sm mt-1" x-text="errors.phoneNumber"></p>
                                </template>
                            </div>

                            <div>
                                <label class="block mb-1 font-semibold text-white">Subject</label>
                                <input 
                                    type="text" 
                                    x-model="form.subject" 
                                    placeholder="How can we help you?"
                                    class="w-full border border-gray-600 bg-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                >
                                <template x-if="errors.subject">
                                    <p class="text-red-500 text-sm mt-1" x-text="errors.subject"></p>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 font-semibold text-white">Message</label>
                            <textarea 
                                x-model="form.message" 
                                rows="6" 
                                placeholder="Tell us what you need..."
                                class="w-full border border-gray-600 bg-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            ></textarea>
                            <template x-if="errors.message">
                                <p class="text-red-500 text-sm mt-1" x-text="errors.message"></p>
                            </template>
                        </div>

                        <div class="flex justify-end pt-3">
                            <button 
                                type="submit" 
                                :disabled="isSubmitting"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition-all duration-300 transform hover:scale-105 flex items-center justify-center"
                            >
                                <template x-if="isSubmitting">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <span x-text="isSubmitting ? 'Sending...' : 'Send Message'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="mt-12 bg-gray-800 rounded-xl shadow-2xl p-4 border border-gray-700">
            <div class="aspect-w-16 aspect-h-9 w-full">
                <iframe 
                    class="w-full h-96 rounded-lg"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d123552.41546483343!2d120.96172879999999!3d14.5965788!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397ca03571ec38b%3A0x69d1d5751069c11f!2sManila%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1651152606128!5m2!1sen!2sph" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
        

    </div>
</section>

<script>
    function contactForm() {
        return {
            form: {
                fullName: '{{ $user ? $user->full_name : '' }}',
                email: '{{ $user ? $user->email : '' }}',
                subject: '',
                phoneNumber: '{{ $user && $user->mobile_number ? $user->mobile_number : '' }}',
                message: ''
            },
            errors: {},
            isSubmitting: false,
            // showModal: false, // Removed as we are using SweetAlert now
            
            handleSubmit() {
                this.errors = {};
                this.isSubmitting = true;
                
                // Form validation
                if (!this.form.fullName.trim()) this.errors.fullName = 'Full name is required';
                if (!this.form.email.trim()) this.errors.email = 'Email is required';
                if (!this.form.subject.trim()) this.errors.subject = 'Subject is required';
                if (!this.form.phoneNumber.trim()) this.errors.phoneNumber = 'Phone number is required';
                if (!this.form.message.trim()) this.errors.message = 'Message is required';
                
                // If there are validation errors, stop submission
                if (Object.keys(this.errors).length > 0) {
                    this.isSubmitting = false;
                    return;
                }
                
                // Submit the form using fetch API
                fetch('{{ route("contact.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.form)
                })
                .then(response => response.json())
                .then(data => {
                    this.isSubmitting = false;
                    
                    if (data.success) {
                        // Show success SweetAlert
                        Swal.fire({
                            title: 'Thank You!',
                            text: 'Your message has been successfully sent. Our team will get back to you as soon as possible.',
                            icon: 'success',
                            background: '#ffffff', // White background matching theme
                            color: '#000000', // Black text matching theme
                            confirmButtonColor: '#EF4444', // Red button matching theme (red-500)
                            confirmButtonText: 'Close'
                        });
                        
                        // Reset form
                        this.form = {
                            fullName: '{{ $user ? $user->full_name : '' }}',
                            email: '{{ $user ? $user->email : '' }}',
                            subject: '',
                            phoneNumber: '{{ $user && $user->mobile_number ? $user->mobile_number : '' }}',
                            message: ''
                        };
                    } else {
                        // Show validation errors
                        if (data.errors) {
                            this.errors = data.errors;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.isSubmitting = false;
                    this.errors.general = 'An error occurred. Please try again.';
                });
            }
        }
    }
</script>
@endsection
