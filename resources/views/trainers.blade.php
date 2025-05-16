@extends('layouts.app')

@section('title', 'Our Trainers')

@section('content')
<style>
    /* Page transition fade-in animation */
    @keyframes pageTransition {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .page-transition {
        opacity: 0;
        animation: pageTransition 0.8s ease forwards;
    }
</style>

<div class="page-transition">
    <div class="trainers-hero" style="background-image: url('{{ asset('assets/gym-bg.jpg') }}')">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Choose your Personal Trainer</h1>
            <h2>in</h2>
            <h2 class="highlight">MANILA TOTAL FITNESS</h2>
        </div>
    </div>

    <div class="trainer-filters">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="gym">Gym</button>
        <button class="filter-btn" data-filter="boxing">Boxing</button>
        <button class="filter-btn" data-filter="muay-thai">Muay Thai</button>
        <button class="filter-btn" data-filter="jiu-jitsu">Jiu Jitsu</button>
    </div>

    <!-- Wrapped in a consistent container div -->
    <div class="trainers-section-container bg-[#121212] py-12">
        <div class="trainers-container">
            @forelse($trainers as $trainer)
            <div class="trainer-card" data-category="{{ strtolower(str_replace(',', ' ', $trainer->instructor_for)) }}">
                <div class="trainer-image">
                    <img src="{{ $trainer->profile_url && strpos($trainer->profile_url, 'data:image') === 0 
                            ? $trainer->profile_url 
                            : (asset($trainer->profile_url) ?: asset('assets/default_profile.png')) }}" 
                        alt="{{ $trainer->user->full_name }}">
                </div>
                <div class="trainer-info">
                    <h3>{{ $trainer->user->full_name }}</h3>
                    <p class="specialization">{{ $trainer->specialization }}</p>
                    <p class="description">{{ $trainer->short_intro }}</p>
                    <div class="schedule">
                        <h4>Instructor Schedule:</h4>
                        <div class="schedule-times">
                            @forelse($trainer->formatted_schedule as $day => $hours)
                                <p>{{ $day }}: {{ $hours }}</p>
                            @empty
                                <p>No schedule available</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <!-- Sample Trainer Cards as fallbacks -->
            <div class="trainer-card" data-category="boxing gym">
                <div class="trainer-image">
                    <img src="{{ asset('assets/about_1.jpg') }}" alt="Mark Reges Cruz">
                </div>
                <div class="trainer-info">
                    <h3>Mark Reges Cruz</h3>
                    <p class="specialization">Boxing & Gym Instructor</p>
                    <p class="description">Mark Reges Cruz is a skilled boxing and gym instructor with a strong martial arts background.</p>
                    <div class="schedule">
                        <h4>Instructor Schedule:</h4>
                        <div class="schedule-times">
                            <p>Mon: 9am - 5pm</p>
                            <p>Tue: 9am - 5pm</p>
                            <p>Wed: 9am - 5pm</p>
                            <p>Thu: 9am - 5pm</p>
                            <p>Fri: 9am - 5pm</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="trainer-card" data-category="taekwondo gym">
                <div class="trainer-image">
                    <img src="{{ asset('assets/about_2.jpg') }}" alt="Isabella Mae Navarro">
                </div>
                <div class="trainer-info">
                    <h3>Isabella Mae Navarro</h3>
                    <p class="specialization">Woman Gym Instructor</p>
                    <p class="description">Isabella Mae Navarro is a skilled taekwondo and gym instructor.</p>
                    <div class="schedule">
                        <h4>Instructor Schedule:</h4>
                        <div class="schedule-times">
                            <p>Mon: 9am - 5pm</p>
                            <p>Tue: 9am - 5pm</p>
                            <p>Wed: 9am - 5pm</p>
                            <p>Sat: 9am - 5pm</p>
                            <p>Sun: 9am - 5pm</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add dark spacer div for consistent spacing -->
<div class="bg-[#121212] py-16"></div>

<style>
    body {
        background-color: #121212 !important;
        color: #ffffff !important;
    }
    
    .trainers-hero {
        height: 60vh;
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        margin-bottom: 2rem;
        background-color: #1a1a1a;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-content h1 {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }

    .hero-content h2 {
        font-size: 2rem;
    }

    .hero-content .highlight {
        color: #f05454;
    }

    .trainer-filters {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        padding: 0 1rem;
    }

    .filter-btn {
        padding: 0.5rem 1.5rem;
        border: 2px solid #f05454;
        background: transparent;
        color: #f05454;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: #f05454;
        color: white;
    }

    .trainers-section-container {
        min-height: 900px; /* Increase minimum height */
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .trainers-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2rem;
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        min-height: 800px; /* Increase minimum height */
    }

    .trainers-container::before {
        display: none;
    }
    
    .trainer-card {
        background: #1a1a1a;
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s ease, opacity 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 320px;
        height: 750px; /* Increased height for all cards */
        flex-shrink: 0;
        margin-bottom: 1rem;
        display: flex;
        flex-direction: column;
    }

    .trainer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .trainer-image {
        width: 100%;
        height: 320px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .trainer-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .trainer-card:hover .trainer-image img {
        transform: scale(1.05);
    }

    .trainer-info {
        padding: 1.8rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .trainer-info h3 {
        color: #f05454;
        font-size: 1.6rem;
        margin-bottom: 0.7rem;
        font-weight: 600;
    }

    .specialization {
        color: #a0a0a0;
        font-size: 1.1rem;
        margin-bottom: 1.2rem;
        font-weight: 500;
    }

    .description {
        color: #e0e0e0;
        font-size: 1rem;
        line-height: 1.7;
        margin-bottom: 1.2rem;
        flex-grow: 1;
    }

    .schedule {
        background: #292929;
        padding: 1.2rem;
        border-radius: 12px;
        margin-top: auto;
        border: 1px solid rgba(255, 255, 255, 0.1);
        height: 180px; /* Fixed height for schedule section */
        overflow-y: auto; /* Allow scrolling if content exceeds height */
        display: flex;
        flex-direction: column;
        position: relative; /* For scroll indicator positioning */
    }

    .schedule h4 {
        color: #f05454;
        margin-bottom: 0.7rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    /* Add scroll icon to schedule header */
    .schedule h4::after {
        content: '\f0d7'; /* down arrow */
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        font-size: 0.8rem;
        color: #f05454;
        opacity: 0.8;
        margin-left: 0.5rem;
        animation: bounce 1.5s infinite;
    }

    .schedule-times {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
        overflow-y: auto; /* Make only the times section scrollable */
        position: relative;
        /* Custom scrollbar for better visibility */
        scrollbar-width: thin;
        scrollbar-color: #f05454 #292929;
    }
    
    /* Custom scrollbar styling for WebKit browsers */
    .schedule-times::-webkit-scrollbar {
        width: 5px;
    }
    
    .schedule-times::-webkit-scrollbar-track {
        background: #292929;
        border-radius: 10px;
    }
    
    .schedule-times::-webkit-scrollbar-thumb {
        background-color: #f05454;
        border-radius: 10px;
    }

    .schedule p {
        color: #e0e0e0;
        font-size: 0.95rem;
        margin: 0;
    }

    /* Add scroll indicator text - only shows when content overflows */
    .schedule.has-overflow::after {
        content: 'Scroll for more';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, #292929 0%, rgba(41, 41, 41, 0.8) 50%, transparent 100%);
        color: #e0e0e0;
        font-size: 0.75rem;
        text-align: center;
        padding: 0.5rem 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .schedule.has-overflow:hover::after {
        opacity: 1;
    }
    
    /* Hide indicator when scrolled to bottom */
    .schedule.scrolled-bottom::after {
        opacity: 0 !important;
    }
    
    /* Subtle bounce animation for scroll indicator */
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(3px); }
    }

    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2rem;
        }

        .hero-content h2 {
            font-size: 1.5rem;
        }

        .trainers-container {
            padding: 1rem;
        }
        
        .trainer-card {
            width: 100%;
            max-width: 350px;
            height: auto; /* Allow height to adjust on mobile */
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const trainerCards = document.querySelectorAll('.trainer-card');
        
        // Add click event listeners to each filter button
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Get filter value
                const filterValue = this.getAttribute('data-filter');
                
                // Show/hide trainer cards based on filter with smooth transitions
                trainerCards.forEach(card => {
                    if (filterValue === 'all') {
                        card.style.opacity = '0';
                        setTimeout(() => {
                            card.style.display = 'block';
                            setTimeout(() => {
                                card.style.opacity = '1';
                            }, 50);
                        }, 300);
                    } else {
                        if (card.getAttribute('data-category').includes(filterValue)) {
                            card.style.opacity = '0';
                            setTimeout(() => {
                                card.style.display = 'block';
                                setTimeout(() => {
                                    card.style.opacity = '1';
                                }, 50);
                            }, 300);
                        } else {
                            card.style.opacity = '0';
                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 300);
                        }
                    }
                });
            });
        });
        
        // Check for schedule overflow and add appropriate class
        function checkScheduleOverflow() {
            const schedules = document.querySelectorAll('.schedule');
            schedules.forEach(schedule => {
                const scheduleContent = schedule.querySelector('.schedule-times');
                if (scheduleContent.scrollHeight > scheduleContent.clientHeight) {
                    // Content is overflowing, add class
                    schedule.classList.add('has-overflow');
                } else {
                    schedule.classList.remove('has-overflow');
                }
            });
        }
        
        // Run on page load
        checkScheduleOverflow();
        
        // Run after window resize
        window.addEventListener('resize', checkScheduleOverflow);
        
        // Add scroll indicator for schedules with scrollable content
        const scheduleTimes = document.querySelectorAll('.schedule-times');
        scheduleTimes.forEach(scheduleTime => {
            scheduleTime.addEventListener('scroll', function() {
                const schedule = this.closest('.schedule');
                
                // If scrolled to bottom, fade out the indicator
                if (this.scrollHeight - this.scrollTop <= this.clientHeight + 10) {
                    schedule.classList.add('scrolled-bottom');
                } else {
                    schedule.classList.remove('scrolled-bottom');
                }
            });
        });
    });
</script>
@endsection