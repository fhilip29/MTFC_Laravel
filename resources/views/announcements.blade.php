@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Announcements</h1>
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <!-- Search Bar -->
            <div class="relative w-full sm:w-96">
                <input 
                    type="text" 
                    id="searchAnnouncement" 
                    placeholder="Search announcements..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                >
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex gap-2 w-full sm:w-auto">
                <button class="filter-btn active px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors" data-filter="all">
                    All
                </button>
                <button class="filter-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors" data-filter="recent">
                    Recent
                </button>
            </div>
        </div>
    </div>

    <!-- Announcements Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="announcementsGrid">
        @foreach($announcements as $announcement)
        <div class="announcement-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <!-- Card Header -->
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-xl font-semibold text-gray-900 hover:text-red-600 transition-colors">
                        {{ $announcement->title }}
                    </h3>
                    @if($announcement->created_at->isToday())
                        <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-semibold rounded-full">New</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500" data-date="{{ $announcement->created_at->toDateString() }}">
    {{ $announcement->created_at->format('F j, Y') }}
</p>
            </div>
            
            <!-- Card Body -->
            <div class="px-6 py-4">
            <p class="text-gray-700 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">

                    {{ $announcement->message }}
                </p>
            </div>
            
            <!-- Card Footer -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end items-center">
                <button 
                    onclick="viewAnnouncement({{ $announcement->id }})"
                    class="text-red-600 hover:text-red-700 text-sm font-medium transition-colors">
                    Read More
                    <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden text-center py-12">
        <div class="inline-block p-6 rounded-full bg-gray-100 mb-4">
            <i class="fas fa-bullhorn text-4xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Announcements Found</h3>
        <p class="text-gray-600">There are no announcements matching your search criteria.</p>
    </div>
</div>

<!-- Announcement View Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeViewModal()"></div>
        
        <div class="relative bg-white rounded-xl max-w-2xl w-full shadow-xl transform transition-all">
            <div class="absolute top-4 right-4">
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6">
                <h2 id="modalTitle" class="text-2xl font-bold text-gray-900 mb-2"></h2>
                <div class="flex items-center gap-4 mb-6">
                    <span id="modalDate" class="text-sm text-gray-500"></span>
                </div>
                <div id="modalContent" class="prose max-w-none">
                    <!-- Content will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchAnnouncement');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.announcement-card');
    const emptyState = document.getElementById('emptyState');
    const grid = document.getElementById('announcementsGrid');

    // Search functionality
    searchInput.addEventListener('input', filterAnnouncements);

    // Filter buttons
    filterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Update active state
            filterBtns.forEach(b => b.classList.remove('active', 'bg-red-600', 'text-white'));
            filterBtns.forEach(b => b.classList.add('bg-gray-200', 'text-gray-700'));
            btn.classList.remove('bg-gray-200', 'text-gray-700');
            btn.classList.add('active', 'bg-red-600', 'text-white');
            
            filterAnnouncements();
        });
    });

    function filterAnnouncements() {
        const searchTerm = searchInput.value.toLowerCase();
        const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
        let visibleCount = 0;

        cards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const content = card.querySelector('.text-gray-700').textContent.toLowerCase();
            const date = new Date(card.querySelector('.text-gray-500').textContent);
            
            let showCard = (title.includes(searchTerm) || content.includes(searchTerm));
            
            // Apply date filters
            if (showCard && activeFilter === 'recent') {
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                showCard = date >= thirtyDaysAgo;
            }
            
            card.style.display = showCard ? 'block' : 'none';
            if (showCard) visibleCount++;
        });

        // Toggle empty state
        emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        grid.style.display = visibleCount === 0 ? 'none' : 'grid';
    }
});

function viewAnnouncement(id) {
    // Fetch announcement details
    fetch(`/api/announcements/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modal = document.getElementById('viewModal');
                const title = document.getElementById('modalTitle');
                const date = document.getElementById('modalDate');
                const content = document.getElementById('modalContent');

                title.textContent = data.announcement.title;
                date.textContent = new Date(data.announcement.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                content.innerHTML = data.announcement.message;

                modal.classList.remove('hidden');
            }
        })
        .catch(error => console.error('Error fetching announcement:', error));
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}


</script>
@endpush
