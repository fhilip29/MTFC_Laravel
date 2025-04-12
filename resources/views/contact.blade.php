@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<section class="bg-gray-100 min-h-screen py-12 px-4 md:px-36">
    <div class="flex justify-center mb-10">
    
    </div>

    <div class="bg-white rounded-xl shadow-xl p-8 max-w-3xl mx-auto space-y-6" x-data="contactForm()">
        <h2 class="text-3xl font-bold text-center text-gray-800">Contact Us</h2>

        <!-- Input Fields -->
        <form @submit.prevent="handleSubmit" class="space-y-5">
            <div>
                <label class="block mb-1 font-semibold">Full Name</label>
                <input type="text" x-model="form.fullName" placeholder="Your name"
                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-700"
                />
                <template x-if="error && form.fullName.trim() === ''">
                    <p class="text-red-500 text-sm mt-1">Name is required</p>
                </template>
            </div>

            <div>
                <label class="block mb-1 font-semibold">Email</label>
                <input type="email" x-model="form.email" placeholder="you@example.com"
                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-700"
                />
                <template x-if="error && form.email.trim() === ''">
                    <p class="text-red-500 text-sm mt-1">Email is required</p>
                </template>
            </div>

            <div>
                <label class="block mb-1 font-semibold">Subject</label>
                <input type="text" x-model="form.subject" placeholder="Subject"
                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-700"
                />
                <template x-if="error && form.subject.trim() === ''">
                    <p class="text-red-500 text-sm mt-1">Subject is required</p>
                </template>
            </div>

            <div>
                <label class="block mb-1 font-semibold">Phone Number</label>
                <input type="text" x-model="form.phoneNumber" placeholder="+63"
                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-700"
                />
                <template x-if="error && form.phoneNumber.trim() === ''">
                    <p class="text-red-500 text-sm mt-1">Phone number is required</p>
                </template>
            </div>

            <div>
                <label class="block mb-1 font-semibold">Message</label>
                <textarea x-model="form.message" rows="6" placeholder="Type your message..."
                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-700"></textarea>
                <template x-if="error && form.message.trim() === ''">
                    <p class="text-red-500 text-sm mt-1">Message is required</p>
                </template>
            </div>

            <div class="text-center">
                <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-2 rounded-full transition-all duration-300">
                    Submit
                </button>
            </div>
        </form>

        <!-- Snackbar -->
        <div x-show="showSnackbar" x-transition.duration.500ms
            class="fixed bottom-5 right-5 bg-white border border-gray-300 px-6 py-4 rounded-lg shadow-xl text-black">
            Message sent successfully!
        </div>

        <!-- Modal -->
        <div x-show="showModal"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl p-6 w-[90%] md:w-[450px] shadow-xl" @click.away="showModal = false">
                <h3 class="text-xl font-bold mb-4">Thank You!</h3>
                <p>Your message has been successfully sent. We'll get back to you soon.</p>
                <div class="text-right mt-6">
                    <button class="bg-gray-800 text-white px-4 py-2 rounded-full hover:bg-gray-900"
                        @click="showModal = false">Close</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function contactForm() {
        return {
            form: {
                fullName: '',
                email: '',
                subject: '',
                phoneNumber: '',
                message: ''
            },
            error: false,
            showModal: false,
            showSnackbar: false,
            handleSubmit() {
                const { fullName, email, subject, phoneNumber, message } = this.form;

                if (!fullName || !email || !subject || !phoneNumber || !message) {
                    this.error = true;
                    return;
                }

                // Simulate server response
                setTimeout(() => {
                    this.error = false;
                    this.showModal = true;
                    this.showSnackbar = true;

                    // Auto-close snackbar after 5 seconds
                    setTimeout(() => {
                        this.showSnackbar = false;
                    }, 5000);
                }, 500);
            }
        }
    }
</script>
@endsection
