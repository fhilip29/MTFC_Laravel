@extends('layouts.app')

@section('title', 'Our Trainers')

@section('content')
<div class="trainers-hero" style="background-image: url('{{ asset('assets/gym-bg.jpg') }}')">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Choose your Personal Trainer</h1>
        <h2>in</h2>
        <h2 class="highlight">MANILA TOTAL FITNESS</h2>
    </div>
</div>

<div class="trainer-filters">
    <button class="filter-btn" data-filter="gym">Gym</button>
    <button class="filter-btn" data-filter="boxing">Boxing</button>
    <button class="filter-btn" data-filter="muay-thai">Muay Thai</button>
    <button class="filter-btn" data-filter="jiu-jitsu">Jiu Jitsu</button>
</div>

<div class="trainers-container">
    @forelse($trainers as $trainer)
    <div class="trainer-card" data-category="{{ strtolower(str_replace(',', ' ', $trainer->instructor_for)) }}">
        <div class="trainer-image">
            <img src="{{ $trainer->profile_url && strpos($trainer->profile_url, 'data:image') === 0 
                    ? $trainer->profile_url 
                    : (asset($trainer->profile_url) ?: asset('assets/default-profile.jpg')) }}" 
                alt="{{ $trainer->user->full_name }}">
        </div>
        <div class="trainer-info">
            <h3>{{ $trainer->user->full_name }}</h3>
            <p class="specialization">{{ $trainer->specialization }}</p>
            <p class="description">{{ $trainer->short_intro }}</p>
            <div class="schedule">
                <h4>Instructor Schedule:</h4>
                @forelse($trainer->formatted_schedule as $day => $hours)
                    <p>{{ $day }}: {{ $hours }}</p>
                @empty
                    <p>No schedule available</p>
                @endforelse
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
                <p>Weekdays: 8:00AM - 1:00PM</p>
                <p>Weekends: 9:00AM - 12:00PM</p>
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
                <p>Weekdays: 2:00PM - 7:00PM</p>
                <p>Weekends: 1:00PM - 4:00PM</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

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

    .trainers-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
    }

    .trainers-container::before {
        display: none;
    }
    .trainer-card {
        background: #1a1a1a;
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .trainer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .trainer-image img {
        width: 100%;
        height: 350px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .trainer-card:hover .trainer-image img {
        transform: scale(1.05);
    }

    .trainer-info {
        padding: 1.8rem;
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
    }

    .schedule {
        background: #292929;
        padding: 1.2rem;
        border-radius: 12px;
        margin-bottom: 1.2rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .schedule h4 {
        color: #f05454;
        margin-bottom: 0.7rem;
        font-weight: 600;
    }

    .schedule p {
        color: #e0e0e0;
        font-size: 0.95rem;
        margin: 0.3rem 0;
    }



    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2rem;
        }

        .hero-content h2 {
            font-size: 1.5rem;
        }

        .trainers-container {
            grid-template-columns: 1fr;
            padding: 1rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const trainerCards = document.querySelectorAll('.trainer-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                button.classList.add('active');

                const filter = button.getAttribute('data-filter');

                trainerCards.forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-category').includes(filter)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endsection