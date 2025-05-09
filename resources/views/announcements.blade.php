@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Announcements</h1>
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <!-- Search Bar -->
            <form action="{{ route('announcements') }}" method="GET" class="flex w-full sm:w-96 relative">
    <input 
        type="text" 
        name="search"
        value="{{ request('search') }}"
        placeholder="Search announcements..." 
        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
    >
    <button 
        type="submit"
        class="px-4 py-2 bg-red-600 text-white rounded-r-lg hover:bg-red-700 transition-colors"
    >
        <i class="fas fa-search"></i>
    </button>
    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
</form>

            <!-- Filter Buttons -->
            <div class="flex gap-2 w-full sm:w-auto">
            <a href="{{ route('announcements') }}"
                class="filter-btn px-4 py-2 rounded-lg {{ !request('filter') ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-red-700 transition-colors">
                All
            </a>
            <a href="{{ route('announcements', ['filter' => 'recent']) }}"
                class="filter-btn px-4 py-2 rounded-lg {{ request('filter') === 'recent' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-red-700 transition-colors">
                Recent
            </a>

            </div>
        </div>
    </div>

    <!-- Announcements Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="announcementsGrid">
        @foreach($announcements as $announcement)
        <div class="announcement-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
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
            <div class="px-6 py-4">
                <p class="text-gray-700 overflow-hidden" style="-webkit-line-clamp: 3; display: -webkit-box; -webkit-box-orient: vertical;">
                    {{ $announcement->message }}
                </p>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end items-center">
                <button onclick="viewAnnouncement({{ $announcement->id }})" class="text-red-600 hover:text-red-700 text-sm font-medium transition-colors">
                    Read More <i class="fas fa-arrow-right ml-1"></i>
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
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeViewModal()"></div>
        <div class="relative bg-white rounded-xl max-w-2xl w-full shadow-xl">
            <div class="absolute top-4 right-4">
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <h2 id="modalTitle" class="text-2xl font-bold text-gray-900 mb-2"></h2>
                <div class="flex items-center gap-4 mb-6">
                    <span id="modalDate" class="text-sm text-gray-500"></span>
                </div>
                <div id="modalContent" class="prose max-w-none"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('input[name="search"]');
    const cards = document.querySelectorAll('.announcement-card');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const emptyState = document.getElementById('emptyState');
    const grid = document.getElementById('announcementsGrid');
    let debounceTimer;

    // Function to filter announcements based on search term and filter button
    function filterAnnouncements() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const activeFilter = document.querySelector('.filter-btn.bg-red-600').textContent.trim().toLowerCase();
        let visibleCount = 0;

        cards.forEach(card => {
            const titleEl = card.querySelector('h3');
            const contentEl = card.querySelector('.text-gray-700');
            const dateEl = card.querySelector('[data-date]');
            const date = new Date(dateEl.dataset.date);

            const titleText = titleEl.textContent.toLowerCase();
            const contentText = contentEl.textContent.toLowerCase();

            let match = titleText.includes(searchTerm) || contentText.includes(searchTerm);

            // Apply filter for recent announcements if the filter is active
            if (match && activeFilter === 'recent') {
                const recentLimit = new Date();
                recentLimit.setDate(recentLimit.getDate() - 30); // Set the limit to the last 30 days
                match = date >= recentLimit;
            }

            if (match) {
                card.style.display = ''; // Show the card if it matches the search/filter
                visibleCount++;
            } else {
                card.style.display = 'none'; // Hide the card if it doesn't match the search/filter
            }
        });

        // Show or hide the empty state based on visible count
        emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        grid.style.display = visibleCount === 0 ? 'none' : 'grid';
    }

    // Bind input event to the search input field
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer); // Clear previous timer
            debounceTimer = setTimeout(filterAnnouncements, 200); // Trigger search after 200ms
        });
    }

    // Initial filter on page load
    filterAnnouncements();
});

function viewAnnouncement(id) {
    fetch(`/api/announcements/${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalTitle').textContent = data.announcement.title;
                document.getElementById('modalDate').textContent = new Date(data.announcement.created_at).toLocaleDateString();
                document.getElementById('modalContent').innerHTML = data.announcement.message;
                document.getElementById('viewModal').classList.remove('hidden');
            }
        })
        .catch(err => console.error('Error:', err));
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}
</script>
@endpush
