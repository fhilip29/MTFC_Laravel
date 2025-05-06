@extends('layouts.admin')
@section('title', 'Manage Members')

@section('content')
<!-- Add SweetAlert2 at the top of the content section -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div x-data="{ 
    showModal: false, 
    currentMember: null, 
    showSubscriptionModal: false, 
    currentMemberId: null, 
    subscriptions: [],
    showQrScannerModal: false,
    setEditFormValues(subscription) {
        // Set form data for edit - only type and plan now
        document.getElementById('edit-type').value = subscription.type;
        document.getElementById('edit-plan').value = subscription.plan;
        document.getElementById('edit-subscription-id').value = subscription.id;
        
        // Show/hide cancel button based on subscription status
        const cancelBtn = document.getElementById('cancel-subscription-btn');
        if (subscription.is_active) {
            cancelBtn.classList.remove('hidden');
        } else {
            cancelBtn.classList.add('hidden');
        }
        
        // Show edit form
        document.getElementById('edit-subscription-modal').classList.remove('hidden');
    },
    confirmArchive(memberId, isArchived) {
        const action = isArchived ? 'restore' : 'archive';
        const title = isArchived ? 'Restore Member' : 'Archive Member';
        const text = isArchived 
            ? 'This will make the member visible in the main list again.'
            : 'This will hide the member from the main list.';
        const confirmButtonText = isArchived ? 'Yes, restore it!' : 'Yes, archive it!';
        const confirmButtonColor = isArchived ? '#10B981' : '#EF4444';
        const successMessage = isArchived ? 'Member has been restored!' : 'Member has been archived!';
        
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmButtonColor,
            cancelButtonColor: '#6B7280',
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Cancel',
            background: '#1F2937',
            color: '#FFFFFF',
            customClass: {
                popup: 'rounded-lg border border-[#374151]',
                title: 'text-white text-xl',
                htmlContainer: 'text-[#9CA3AF]',
                confirmButton: 'rounded-md px-4 py-2',
                cancelButton: 'rounded-md px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/admin/members/' + memberId + '/archive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the row from the table
                        const row = document.querySelector('#member-row-' + memberId);
                        if (row) row.remove();
                        
                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: successMessage,
                            icon: 'success',
                            confirmButtonColor: '#3B82F6',
                            background: '#1F2937',
                            color: '#FFFFFF',
                            customClass: {
                                popup: 'rounded-lg border border-[#374151]',
                                title: 'text-white text-xl',
                                htmlContainer: 'text-[#9CA3AF]',
                                confirmButton: 'rounded-md px-4 py-2'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#3B82F6',
                            background: '#1F2937',
                            color: '#FFFFFF',
                            customClass: {
                                popup: 'rounded-lg border border-[#374151]',
                                title: 'text-white text-xl',
                                htmlContainer: 'text-[#9CA3AF]',
                                confirmButton: 'rounded-md px-4 py-2'
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#3B82F6',
                        background: '#1F2937',
                        color: '#FFFFFF',
                        customClass: {
                            popup: 'rounded-lg border border-[#374151]',
                            title: 'text-white text-xl',
                            htmlContainer: 'text-[#9CA3AF]',
                            confirmButton: 'rounded-md px-4 py-2'
                        }
                    });
                });
            }
        });
    }
}" class="container mx-auto px-4 py-6 sm:py-8">
    <div class="bg-[#1F2937] p-4 sm:p-6 rounded-2xl shadow-md border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-2 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-users text-[#9CA3AF]"></i> Manage Members
            </h1>
            
            @if(isset($showArchived) && $showArchived)
            <div class="bg-yellow-500 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-archive mr-2"></i> 
                Viewing archived members
                <a href="{{ route('admin.members.admin_members') }}" class="ml-3 bg-[#374151] hover:bg-[#4B5563] text-white px-3 py-1 rounded-md text-sm">
                    Show Active
                </a>
            </div>
            @endif
        </div>

        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="relative w-full sm:w-2/3 md:w-1/2 lg:w-1/3">
                <input 
                    type="text" 
                    id="search-member"
                    placeholder="Search members..." 
                    class="w-full pl-10 pr-4 py-3 bg-[#374151] border border-[#4B5563] text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF]"
                >
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]"></i>
            </div>
            <select 
                id="status-filter"
                onchange="filterMembers(this.value)"
                class="w-full sm:w-48 px-4 py-3 bg-[#374151] border border-[#4B5563] text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]">
                <option value="all" {{ !isset($showArchived) ? 'selected' : '' }}>All Members</option>
                <option value="active" {{ !isset($showArchived) || !$showArchived ? 'selected' : '' }}>Active</option>
                <option value="inactive">Inactive</option>
                <option value="archived" {{ isset($showArchived) && $showArchived ? 'selected' : '' }}>Archived</option>
            </select>
        </div>

        <div class="-mx-4 sm:mx-0 overflow-x-auto rounded-lg shadow-md bg-[#1F2937]">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-[#374151] text-sm">
                        <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left">Member</th>
                                <th class="hidden sm:table-cell px-4 py-3 text-left">Email</th>
                                <th class="hidden md:table-cell px-4 py-3 text-left">Join Date</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#374151] text-[#9CA3AF]">
                        @foreach($members->where('role', 'member') as $member)
                            <tr id="member-row-{{ $member->id }}" class="hover:bg-[#374151] transition-colors">
                                <td class="px-4 py-3 text-white">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $member->profile_image ? asset('storage/' . $member->profile_image) : asset('assets/default-profile.jpg') }}" 
                                             alt="{{ $member->full_name }}" class="w-10 h-10 rounded-full object-cover">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $member->full_name }}</span>
                                            <span class="text-xs text-[#9CA3AF] sm:hidden">{{ $member->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-4 py-3">{{ $member->email }}</td>
                                <td class="hidden md:table-cell px-4 py-3">{{ $member->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $hasActiveSubscription = $member->activeSubscriptions()->count() > 0;
                                        $status = $hasActiveSubscription ? 'Active' : 'Inactive';
                                        $statusClass = $hasActiveSubscription ? 'bg-green-500' : 'bg-gray-500';
                                    @endphp
                                    <span id="member-status-{{ $member->id }}" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center gap-3">
                                        <button 
                                            type="button"
                                            @click="showSubscriptionModal = true; currentMemberId = {{ $member->id }}; 
                                                fetch('/admin/members/'+{{ $member->id }}+'/subscriptions')
                                                .then(response => response.json())
                                                .then(data => { subscriptions = data; })"
                                            class="text-blue-400 hover:text-blue-300 transition-colors" 
                                            title="Manage Subscription">
                                            <i class="fas fa-crown"></i>
                                        </button>
                                        <button 
                                            type="button"
                                            @click="showModal = true; 
                                                fetch('/admin/members/' + {{ $member->id }} + '/subscriptions')
                                                .then(response => response.json())
                                                .then(data => {
                                                    const activeSubscriptions = data.filter(sub => sub.is_active);
                                                    currentMember = Object.assign({}, {{ json_encode($member) }}, {
                                                        subscriptions: data,
                                                        active_subscriptions: activeSubscriptions
                                                    });
                                                })"
                                            class="text-blue-400 hover:text-blue-300 transition-colors" 
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button 
                                            type="button"
                                            @click="confirmArchive({{ $member->id }}, {{ isset($showArchived) && $showArchived ? 'true' : 'false' }})"
                                            @if(isset($showArchived) && $showArchived)
                                            class="text-green-400 hover:text-green-300 transition-colors" 
                                            title="Restore Member">
                                            <i class="fas fa-undo-alt"></i>
                                            @else
                                            class="text-red-400 hover:text-red-300 transition-colors" 
                                            title="Archive Member">
                                            <i class="fas fa-archive"></i>
                                            @endif
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Member Details Modal -->
    <div 
        x-show="showModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showModal = false" class="fixed inset-0 bg-black opacity-50"></div>
            
            <div 
                class="relative bg-[#1F2937] rounded-lg max-w-xl w-full mx-auto shadow-xl z-50 border border-[#374151]">
                
                <div class="p-6">
                    <div class="flex justify-between items-center border-b border-[#374151] pb-4">
                        <h3 class="text-xl font-bold text-white">Member Details</h3>
                        <button @click="showModal = false" class="text-[#9CA3AF] hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="py-4" x-show="currentMember">
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="flex flex-col items-center">
                                <img :src="currentMember?.profile_image ? '/storage/' + currentMember.profile_image : '/assets/default-profile.jpg'" 
                                     :alt="currentMember?.full_name" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-[#374151] mb-2">
                                <h4 x-text="currentMember?.full_name" class="text-lg font-bold text-white"></h4>
                                <div>
                                    <template x-if="currentMember?.active_subscriptions && currentMember.active_subscriptions.length > 0">
                                        <div class="flex flex-wrap justify-center gap-1 mt-1">
                                            <template x-for="sub in currentMember.active_subscriptions" :key="sub.id">
                                                <span 
                                                    :class="{
                                                        'bg-red-500': sub.type === 'boxing',
                                                        'bg-purple-500': sub.type === 'muay',
                                                        'bg-blue-500': sub.type === 'jiu-jitsu',
                                                        'bg-green-500': sub.type === 'gym'
                                                    }"
                                                    class="px-2 py-1 rounded-full text-xs font-medium text-white"
                                                    x-text="sub.type.charAt(0).toUpperCase() + sub.type.slice(1)"
                                                ></span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!currentMember?.active_subscriptions || currentMember.active_subscriptions.length === 0">
                                        <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full mt-1">Inactive Member</span>
                                    </template>
                                </div>
                            </div>
                            
                            <div class="flex-1 space-y-3">
                                <div class="border-b border-[#374151] pb-3">
                                    <h5 class="text-[#9CA3AF] uppercase text-xs font-semibold mb-2">Contact Information</h5>
                                    <div class="grid grid-cols-1 gap-2">
                                        <div class="flex items-center">
                                            <span class="w-24 text-[#9CA3AF]">Email:</span>
                                            <span x-text="currentMember?.email" class="text-white"></span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-24 text-[#9CA3AF]">Phone:</span>
                                            <span x-text="currentMember?.mobile_number || 'Not provided'" class="text-white"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-b border-[#374151] pb-3">
                                    <h5 class="text-[#9CA3AF] uppercase text-xs font-semibold mb-2">Personal Information</h5>
                                    <div class="grid grid-cols-1 gap-2">
                                        <div class="flex items-center">
                                            <span class="w-24 text-[#9CA3AF]">Gender:</span>
                                            <span x-text="currentMember?.gender ? (currentMember.gender.charAt(0).toUpperCase() + currentMember.gender.slice(1)) : 'Not specified'" class="text-white"></span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-24 text-[#9CA3AF]">Goal:</span>
                                            <span x-text="currentMember?.fitness_goal ? (currentMember.fitness_goal.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')) : 'Not specified'" class="text-white"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h5 class="text-[#9CA3AF] uppercase text-xs font-semibold mb-2">Membership Information</h5>
                                    <div class="grid grid-cols-1 gap-2">
                                        <div class="flex items-center">
                                            <span class="w-24 text-[#9CA3AF]">Joined:</span>
                                            <span x-text="new Date(currentMember?.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })" class="text-white"></span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-24 text-[#9CA3AF]">Status:</span>
                                            <template x-if="currentMember?.active_subscriptions && currentMember.active_subscriptions.length > 0">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white bg-green-500">Active</span>
                                            </template>
                                            <template x-if="!currentMember?.active_subscriptions || currentMember.active_subscriptions.length === 0">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white bg-gray-500">Inactive</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-right">
                        <button @click="showModal = false" class="px-4 py-2 bg-[#374151] text-white rounded hover:bg-[#4B5563] transition-colors">
                            Close
                        </button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Subscription Management Modal -->
    <div 
        x-show="showSubscriptionModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showSubscriptionModal = false" class="fixed inset-0 bg-black opacity-50"></div>
            
            <div class="relative bg-[#1F2937] rounded-lg max-w-3xl w-full mx-auto shadow-xl z-50 border border-[#374151]">
                <div class="p-6">
                    <div class="flex justify-between items-center border-b border-[#374151] pb-4">
                        <h3 class="text-xl font-bold text-white">Manage Subscriptions</h3>
                        <button @click="showSubscriptionModal = false" class="text-[#9CA3AF] hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="py-4">
                        <!-- Subscription list with scrollable container -->
                        <div class="mb-6" x-show="subscriptions.length > 0">
                            <div class="overflow-auto max-h-60 rounded-lg shadow-md">
                                <table class="min-w-full divide-y divide-[#374151] text-sm">
                                    <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Type</th>
                                            <th class="px-4 py-3 text-left">Plan</th>
                                            <th class="px-4 py-3 text-left">Price</th>
                                            <th class="px-4 py-3 text-left">Start Date</th>
                                            <th class="px-4 py-3 text-left">End Date</th>
                                            <th class="px-4 py-3 text-left">Status</th>
                                            <th class="px-4 py-3 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-[#1F2937] divide-y divide-[#374151] text-[#9CA3AF]">
                                        <template x-for="subscription in subscriptions" :key="subscription.id">
                                            <tr class="hover:bg-[#374151] transition-colors">
                                                <td class="px-4 py-3 text-white" x-text="subscription.type.charAt(0).toUpperCase() + subscription.type.slice(1)"></td>
                                                <td class="px-4 py-3" x-text="subscription.plan.charAt(0).toUpperCase() + subscription.plan.slice(1)"></td>
                                                <td class="px-4 py-3" x-text="'$' + subscription.price"></td>
                                                <td class="px-4 py-3" x-text="subscription.start_date ? new Date(subscription.start_date).toLocaleDateString() : 'N/A'"></td>
                                                <td class="px-4 py-3" x-text="subscription.end_date ? new Date(subscription.end_date).toLocaleDateString() : 'N/A'"></td>
                                                <td class="px-4 py-3">
                                                    <span 
                                                        :class="subscription.is_active ? 'bg-green-500' : 'bg-red-500'" 
                                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                                                        x-text="subscription.is_active ? 'Active' : 'Inactive'">
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="flex justify-center items-center gap-2">
                                                        <button 
                                                            @click="
                                                                Swal.fire({
                                                                    title: 'Cancel Subscription',
                                                                    text: 'Are you sure you want to cancel this subscription?',
                                                                    icon: 'warning',
                                                                    showCancelButton: true,
                                                                    confirmButtonColor: '#EF4444',
                                                                    cancelButtonColor: '#6B7280',
                                                                    confirmButtonText: 'Yes, cancel it!',
                                                                    cancelButtonText: 'No, keep it',
                                                                    background: '#1F2937',
                                                                    color: '#FFFFFF',
                                                                    customClass: {
                                                                        popup: 'rounded-lg border border-[#374151]',
                                                                        title: 'text-white text-xl',
                                                                        htmlContainer: 'text-[#9CA3AF]',
                                                                        confirmButton: 'rounded-md px-4 py-2',
                                                                        cancelButton: 'rounded-md px-4 py-2'
                                                                    }
                                                                }).then((result) => {
                                                                    if (result.isConfirmed) {
                                                                        fetch('/admin/members/'+currentMemberId+'/subscriptions/'+subscription.id+'/cancel', {
                                                                            method: 'POST',
                                                                            headers: {
                                                                                'Content-Type': 'application/json',
                                                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                                                            }
                                                                        })
                                                                        .then(response => response.json())
                                                                        .then(data => {
                                                                            if (data.success) {
                                                                                // Refresh the subscriptions list
                                                                                fetch('/admin/members/'+currentMemberId+'/subscriptions')
                                                                                    .then(response => response.json())
                                                                                    .then(data => { 
                                                                                        subscriptions = data;
                                                                                        
                                                                                        // Update member status in main table
                                                                                        updateMemberStatusBadge(currentMemberId, false);
                                                                                        
                                                                                        // Show success message
                                                                                        Swal.fire({
                                                                                            title: 'Cancelled!',
                                                                                            text: 'Subscription has been cancelled successfully.',
                                                                                            icon: 'success',
                                                                                            confirmButtonColor: '#3B82F6',
                                                                                            background: '#1F2937',
                                                                                            color: '#FFFFFF',
                                                                                            customClass: {
                                                                                                popup: 'rounded-lg border border-[#374151]',
                                                                                                title: 'text-white text-xl',
                                                                                                htmlContainer: 'text-[#9CA3AF]',
                                                                                                confirmButton: 'rounded-md px-4 py-2'
                                                                                            }
                                                                                        });
                                                                                    });
                                                                            } else {
                                                                                Swal.fire({
                                                                                    title: 'Error!',
                                                                                    text: data.message || 'Failed to cancel subscription',
                                                                                    icon: 'error',
                                                                                    confirmButtonColor: '#3B82F6',
                                                                                    background: '#1F2937',
                                                                                    color: '#FFFFFF',
                                                                                    customClass: {
                                                                                        popup: 'rounded-lg border border-[#374151]',
                                                                                        title: 'text-white text-xl',
                                                                                        htmlContainer: 'text-[#9CA3AF]',
                                                                                        confirmButton: 'rounded-md px-4 py-2'
                                                                                    }
                                                                                });
                                                                            }
                                                                        })
                                                                        .catch(error => {
                                                                            console.error('Error:', error);
                                                                            Swal.fire({
                                                                                title: 'Error!',
                                                                                text: 'An error occurred. Please try again.',
                                                                                icon: 'error',
                                                                                confirmButtonColor: '#3B82F6',
                                                                                background: '#1F2937',
                                                                                color: '#FFFFFF',
                                                                                customClass: {
                                                                                    popup: 'rounded-lg border border-[#374151]',
                                                                                    title: 'text-white text-xl',
                                                                                    htmlContainer: 'text-[#9CA3AF]',
                                                                                    confirmButton: 'rounded-md px-4 py-2'
                                                                                }
                                                                            });
                                                                        });
                                                                    }
                                                                })
                                                            "
                                                            class="text-red-400 hover:text-red-300 transition-colors"
                                                            title="Cancel"
                                                            x-show="subscription.is_active">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- No subscriptions message -->
                        <div 
                            x-show="subscriptions.length === 0"
                            class="py-6 text-center">
                            <i class="fas fa-scroll text-[#4B5563] text-4xl mb-3"></i>
                            <p class="text-[#9CA3AF]">This member doesn't have any subscriptions yet.</p>
                        </div>
                        
                        <!-- Add subscription form -->
                        <div class="mt-6 border-t border-[#374151] pt-6">
                            <h4 class="text-white font-semibold mb-4">Add New Subscription</h4>
                            <form 
                                class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                @submit.prevent="
                                    // Show loading state
                                    Swal.fire({
                                        title: 'Adding subscription...',
                                        text: 'Please wait',
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });
                                    
                                    fetch('/admin/members/' + currentMemberId + '/subscriptions', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                        },
                                        body: JSON.stringify({
                                            type: $event.target.elements.type.value,
                                            plan: $event.target.elements.plan.value
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            // Refresh the subscriptions list
                                            fetch('/admin/members/'+currentMemberId+'/subscriptions')
                                                .then(response => response.json())
                                                .then(data => { 
                                                    subscriptions = data; 
                                                    // Reset form
                                                    $event.target.reset();
                                                    
                                                    // Update member status in main table
                                                    updateMemberStatusBadge(currentMemberId, true);
                                                    
                                                    // Show success message
                                                    Swal.fire({
                                                        title: 'Success!',
                                                        text: 'Subscription added successfully',
                                                        icon: 'success',
                                                        confirmButtonColor: '#3B82F6',
                                                        background: '#1F2937',
                                                        color: '#FFFFFF',
                                                        customClass: {
                                                            popup: 'rounded-lg border border-[#374151]',
                                                            title: 'text-white text-xl',
                                                            htmlContainer: 'text-[#9CA3AF]',
                                                            confirmButton: 'rounded-md px-4 py-2'
                                                        }
                                                    });
                                                });
                                        } else {
                                            Swal.fire({
                                                title: 'Error!',
                                                text: data.message || 'Failed to add subscription',
                                                icon: 'error',
                                                confirmButtonColor: '#3B82F6',
                                                background: '#1F2937',
                                                color: '#FFFFFF',
                                                customClass: {
                                                    popup: 'rounded-lg border border-[#374151]',
                                                    title: 'text-white text-xl',
                                                    htmlContainer: 'text-[#9CA3AF]',
                                                    confirmButton: 'rounded-md px-4 py-2'
                                                }
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        Swal.fire({
                                            title: 'Error!',
                                            text: 'An error occurred. Please try again.',
                                            icon: 'error',
                                            confirmButtonColor: '#3B82F6',
                                            background: '#1F2937',
                                            color: '#FFFFFF',
                                            customClass: {
                                                popup: 'rounded-lg border border-[#374151]',
                                                title: 'text-white text-xl',
                                                htmlContainer: 'text-[#9CA3AF]',
                                                confirmButton: 'rounded-md px-4 py-2'
                                            }
                                        });
                                    })
                                "
                            >
                                <div>
                                    <label class="block text-[#9CA3AF] text-sm font-medium mb-2">Type</label>
                                    <select name="type" required class="w-full px-3 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Type</option>
                                        <option value="gym">Gym</option>
                                        <option value="boxing">Boxing</option>
                                        <option value="muay">Muay Thai</option>
                                        <option value="jiu-jitsu">Jiu-Jitsu</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[#9CA3AF] text-sm font-medium mb-2">Plan</label>
                                    <select name="plan" required class="w-full px-3 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Plan</option>
                                        <option value="daily">Daily</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="per-session">Per Session</option>
                                    </select>
                                </div>
                            
                                <div class="md:col-span-2 mt-6 flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-1"></i> Add Subscription
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Subscription Modal -->
    <div id="edit-subscription-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div onclick="document.getElementById('edit-subscription-modal').classList.add('hidden')" class="fixed inset-0 bg-black opacity-50"></div>
            
            <div class="relative bg-[#1F2937] rounded-lg max-w-md w-full mx-auto shadow-xl z-50 border border-[#374151]">
                <div class="p-6">
                    <div class="flex justify-between items-center border-b border-[#374151] pb-4">
                        <h3 class="text-xl font-bold text-white">Edit Subscription</h3>
                        <button onclick="document.getElementById('edit-subscription-modal').classList.add('hidden')" class="text-[#9CA3AF] hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="py-4">
                        <form 
                            id="edit-subscription-form"
                            class="space-y-4"
                            onsubmit="
                                event.preventDefault();
                                const form = event.target;
                                const subscriptionId = form.elements['subscription_id'].value;
                                
                                fetch(`/admin/members/${currentMemberId}/subscriptions/${subscriptionId}`, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                    },
                                    body: JSON.stringify({
                                        type: form.elements['type'].value,
                                        plan: form.elements['plan'].value
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Refresh the subscriptions list
                                        fetch('/admin/members/'+currentMemberId+'/subscriptions')
                                            .then(response => response.json())
                                            .then(data => { 
                                                subscriptions = data;
                                                // Hide modal
                                                document.getElementById('edit-subscription-modal').classList.add('hidden');
                                            });
                                    } else {
                                        alert('Error updating subscription: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred. Please try again.');
                                });
                            "
                        >
                            <input type="hidden" id="edit-subscription-id" name="subscription_id">
                            
                            <div>
                                <label class="block text-[#9CA3AF] text-sm font-medium mb-2">Type</label>
                                <select id="edit-type" name="type" required class="w-full px-3 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Type</option>
                                    <option value="gym">Gym</option>
                                    <option value="boxing">Boxing</option>
                                    <option value="muay">Muay Thai</option>
                                    <option value="jiu-jitsu">Jiu-Jitsu</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-[#9CA3AF] text-sm font-medium mb-2">Plan</label>
                                <select id="edit-plan" name="plan" required class="w-full px-3 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Plan</option>
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="per-session">Per Session</option>
                                </select>
                            </div>
                            
                            <div class="flex flex-wrap justify-end gap-3 mt-6">
                                <button 
                                    type="button" 
                                    onclick="document.getElementById('edit-subscription-modal').classList.add('hidden')"
                                    class="px-4 py-2 bg-[#374151] text-white rounded hover:bg-[#4B5563] transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="button"
                                    id="cancel-subscription-btn"
                                    class="hidden px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
                                    onclick="
                                        Swal.fire({
                                            title: 'Cancel Subscription',
                                            text: 'Are you sure you want to cancel this subscription?',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#EF4444',
                                            cancelButtonColor: '#6B7280',
                                            confirmButtonText: 'Yes, cancel it!',
                                            cancelButtonText: 'No, keep it',
                                            background: '#1F2937',
                                            color: '#FFFFFF',
                                            customClass: {
                                                popup: 'rounded-lg border border-[#374151]',
                                                title: 'text-white text-xl',
                                                htmlContainer: 'text-[#9CA3AF]',
                                                confirmButton: 'rounded-md px-4 py-2',
                                                cancelButton: 'rounded-md px-4 py-2'
                                            }
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                            const subscriptionId = document.getElementById('edit-subscription-id').value;
                                            
                                            fetch(`/admin/members/${currentMemberId}/subscriptions/${subscriptionId}/cancel`, {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                                }
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    // Refresh the subscriptions list
                                                    fetch('/admin/members/'+currentMemberId+'/subscriptions')
                                                        .then(response => response.json())
                                                        .then(data => { 
                                                            subscriptions = data;
                                                            // Hide modal
                                                            document.getElementById('edit-subscription-modal').classList.add('hidden');
                                                                
                                                                // Show success message
                                                                Swal.fire({
                                                                    title: 'Success!',
                                                                    text: 'Subscription has been cancelled!',
                                                                    icon: 'success',
                                                                    confirmButtonColor: '#3B82F6',
                                                                    background: '#1F2937',
                                                                    color: '#FFFFFF',
                                                                    customClass: {
                                                                        popup: 'rounded-lg border border-[#374151]',
                                                                        title: 'text-white text-xl',
                                                                        htmlContainer: 'text-[#9CA3AF]',
                                                                        confirmButton: 'rounded-md px-4 py-2'
                                                                    }
                                                                });
                                                        });
                                                } else {
                                                        Swal.fire({
                                                            title: 'Error!',
                                                            text: 'Error cancelling subscription: ' + data.message,
                                                            icon: 'error',
                                                            confirmButtonColor: '#3B82F6',
                                                            background: '#1F2937',
                                                            color: '#FFFFFF',
                                                            customClass: {
                                                                popup: 'rounded-lg border border-[#374151]',
                                                                title: 'text-white text-xl',
                                                                htmlContainer: 'text-[#9CA3AF]',
                                                                confirmButton: 'rounded-md px-4 py-2'
                                                            }
                                                        });
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                    Swal.fire({
                                                        title: 'Error!',
                                                        text: 'An error occurred. Please try again.',
                                                        icon: 'error',
                                                        confirmButtonColor: '#3B82F6',
                                                        background: '#1F2937',
                                                        color: '#FFFFFF',
                                                        customClass: {
                                                            popup: 'rounded-lg border border-[#374151]',
                                                            title: 'text-white text-xl',
                                                            htmlContainer: 'text-[#9CA3AF]',
                                                            confirmButton: 'rounded-md px-4 py-2'
                                                        }
                                                    });
                                            });
                                        }
                                        });
                                    "
                                >
                                    <i class="fas fa-ban mr-1"></i> Cancel Subscription
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                                >
                                    Update Subscription
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Custom scrollbar for subscription list */
    .overflow-auto::-webkit-scrollbar {
        width: 6px;
    }
    
    .overflow-auto::-webkit-scrollbar-track {
        background: #374151;
        border-radius: 8px;
    }
    
    .overflow-auto::-webkit-scrollbar-thumb {
        background: #4B5563;
        border-radius: 8px;
    }
    
    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #6B7280;
    }
    
    /* Firefox scrollbar */
    .overflow-auto {
        scrollbar-width: thin;
        scrollbar-color: #4B5563 #374151;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search-member');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const name = row.querySelector('td:first-child').textContent.toLowerCase();
                    const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || email.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });

    function filterMembers(status) {
        if (status === 'archived') {
            window.location.href = "{{ route('admin.members.admin_members') }}?show_archived=1";
        } else if (status === 'all') {
            window.location.href = "{{ route('admin.members.admin_members') }}";
        } else {
            // Future implementation for filtering by other statuses
            // For now just reload the page without archived members
            window.location.href = "{{ route('admin.members.admin_members') }}";
        }
    }

    /**
     * Update the member status badge in the main table
     */
    function updateMemberStatusBadge(memberId, isActive) {
        const statusBadge = document.getElementById(`member-status-${memberId}`);
        if (statusBadge) {
            if (isActive) {
                statusBadge.className = 'inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white bg-green-500';
                statusBadge.textContent = 'Active';
            } else {
                statusBadge.className = 'inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white bg-gray-500';
                statusBadge.textContent = 'Inactive';
            }
        }
    }
</script>
@endsection