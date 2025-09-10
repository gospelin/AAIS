@extends('layouts.app')

@section('title', 'About Us | ' . config('app.name'))

@push('styles')
    <style>
        /* About Page Specific Styles */
        :root {
            --primary-green: #21a055;
            --dark-green: #006400;
            --gold: #D4AF37;
            --white: #ffffff;
            --dark-gray: #6c757d;
            --light-gray: #f8f9fa;
            --font-display: "Playfair Display", serif;
            --font-primary: "Inter", sans-serif;
        }

        /* Introductory Banner */
        .about-intro {
            position: relative;
            padding: 4rem 0;
            background: linear-gradient(45deg, var(--primary-green), var(--dark-green));
            color: var(--white);
            text-align: center;
            overflow: hidden;
        }

        .about-intro::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100"><path fill="%23ffffff" d="M0,0C240,60 480,100 720,80C960,60 1200,20 1440,60V100H0Z"/></svg>') no-repeat center bottom;
            background-size: cover;
        }

        .about-intro .intro-title {
            font-family: var(--font-display);
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }

        .about-intro .intro-subtitle {
            font-family: var(--font-primary);
            font-size: clamp(1.2rem, 2.5vw, 1.8rem);
            font-weight: 300;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Main Section */
        .about-main {
            padding: 5rem 0;
        }

        .about-main .section-heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--dark-green);
            margin-bottom: 1.5rem;
        }

        .about-main .lead {
            font-size: 1.2rem;
            line-height: 1.8;
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
        }

        .about-main .values-list {
            padding-left: 0;
            list-style: none;
        }

        .about-main .values-list li {
            position: relative;
            padding-left: 40px;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .about-main .values-list li::before {
            content: "★";
            position: absolute;
            left: 0;
            top: 2px;
            color: var(--gold);
            font-size: 1.5rem;
        }

        .director-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .director-card:hover {
            transform: scale(1.05);
        }

        .director-photo {
            width: 100%;
            max-width: 280px;
            border-radius: 50%;
            border: 4px solid var(--gold);
            margin-bottom: 1rem;
        }

        .director-info h3 {
            font-family: var(--font-display);
            font-size: 1.8rem;
            color: var(--dark-green);
            margin: 0;
        }

        .director-info p {
            font-size: 1.2rem;
            color: var(--dark-gray);
            margin: 0;
        }

        .about-cta {
            padding: 5rem 0;
            position: relative;
            color: var(--white);
        }

        .about-cta .lead {
            font-size: 1.3rem;
        }

        .about-cta .btn-pulse {
            background: var(--gold);
            color: var(--dark-green);
            font-weight: 600;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            animation: pulse 2s infinite ease-in-out;
        }

        .about-cta .btn-pulse:hover {
            background: var(--white);
            animation: none;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* History Section */
        .history-section {
            padding: 5rem 0;
            background: var(--light-gray);
        }

        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 4px;
            background: var(--primary-green);
            transform: translateX(-50%);
        }

        .timeline-item {
            position: relative;
            margin: 2rem 0;
            width: 50%;
        }

        .timeline-item:nth-child(odd) {
            left: 0;
            text-align: right;
            padding-right: 3rem;
        }

        .timeline-item:nth-child(even) {
            left: 50%;
            padding-left: 3rem;
        }

        .timeline-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .timeline-content::before {
            content: '';
            position: absolute;
            top: 20px;
            width: 16px;
            height: 16px;
            background: var(--gold);
            border-radius: 50%;
            border: 2px solid var(--white);
        }

        .timeline-item:nth-child(odd) .timeline-content::before {
            right: -8px;
        }

        .timeline-item:nth-child(even) .timeline-content::before {
            left: -8px;
        }

        .timeline-content h3 {
            font-family: var(--font-display);
            font-size: 1.5rem;
            color: var(--dark-green);
            margin-bottom: 0.5rem;
        }

        .timeline-content p {
            font-size: 1.1rem;
            color: var(--dark-gray);
        }

        /* Impact Section */
        .impact-section {
            padding: 5rem 0;
        }

        .impact-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .impact-card:hover {
            transform: translateY(-10px);
        }

        .impact-card h3 {
            font-family: var(--font-display);
            font-size: 2.5rem;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
        }

        .impact-card p {
            font-size: 1.2rem;
            color: var(--dark-gray);
        }


        /* Location Section */
        .location-section {
            padding: 5rem 0;
        }

        .location-section .map-responsive {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border: 3px solid var(--gold);
        }

        /* FAQs Section */
        .faqs-section {
            padding: 5rem 0;
            background: var(--light-gray);
        }

        .faqs-section .accordion-button {
            font-family: var(--font-display);
            font-size: 1.3rem;
            color: var(--dark-green);
            border-left: 5px solid var(--primary-green);
        }

        .faqs-section .accordion-button:not(.collapsed) {
            background: linear-gradient(90deg, var(--light-gray), var(--white));
            color: var(--primary-green);
        }

        .faqs-section .accordion-body {
            font-size: 1.1rem;
            color: var(--dark-gray);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .about-intro .intro-title {
                font-size: clamp(2rem, 4vw, 2.5rem);
            }

            .about-intro .intro-subtitle {
                font-size: clamp(1rem, 2vw, 1.5rem);
            }

            .director-photo {
                max-width: 200px;
            }

            .director-info h3 {
                font-size: 1.5rem;
            }

            .timeline::before {
                left: 20px;
            }

            .timeline-item {
                width: 100%;
                padding-left: 40px;
                padding-right: 20px;
                text-align: left;
            }

            .timeline-item:nth-child(odd),
            .timeline-item:nth-child(even) {
                left: 0;
            }

            .timeline-item:nth-child(odd) .timeline-content::before,
            .timeline-item:nth-child(even) .timeline-content::before {
                left: -8px;
            }
        }

        @media (max-width: 576px) {
            .about-intro .intro-title {
                font-size: clamp(1.8rem, 3.5vw, 2rem);
            }

            .about-intro .intro-subtitle {
                font-size: clamp(0.9rem, 1.8vw, 1.2rem);
            }

            .director-photo {
                max-width: 160px;
            }

            .director-info h3 {
                font-size: 1.2rem;
            }

            .impact-card h3 {
                font-size: 2rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Introductory Banner -->
    <section class="about-intro">
        <div class="container text-center">
            <h1 class="intro-title gsap-fade-up">{{ config('app.name') }}</h1>
            <p class="intro-subtitle gsap-fade-up" data-delay="0.2">Welcome to Aunty Anne's International School, where practical learning enhances knowledge and confidence.</p>
        </div>
    </section>

    <!-- Mission, Vision, and Director Section -->
    <section class="about-main py-5">
        <div class="container">
            <div class="row align-items-center">
                <!-- Mission, Vision, and Values -->
                <div class="col-lg-7 gsap-slide-left">
                    <div class="content-card">
                        <h2 class="section-heading">Our Mission</h2>
                        <p class="lead">At {{ config('app.name') }}, our mission is to nurture a community of lifelong learners who are prepared to thrive in a dynamic world. We are dedicated to providing a well-rounded education that fosters intellectual growth, character development, and a commitment to global citizenship.</p>

                        <h2 class="section-heading">Our Vision</h2>
                        <p class="lead">We envision a world where every student is empowered to reach their full potential, equipped with the knowledge and skills necessary to make meaningful contributions to society. Our vision is to be a leader in innovative education, inspiring excellence and shaping the future.</p>

                        <h2 class="section-heading">Our Values</h2>
                        <ul class="lead values-list">
                            <li><strong>Practical Learning:</strong> Hands-on experiences that prepare students for real-world challenges.</li>
                            <li><strong>Knowledge Empowerment:</strong> Fostering curiosity and a deep understanding across diverse disciplines.</li>
                            <li><strong>Confidence Building:</strong> Nurturing resilience and self-assurance to pursue ambitious goals.</li>
                        </ul>
                    </div>
                </div>
                <!-- Director's Image -->
                <div class="col-lg-5 text-center gsap-slide-right">
                    <div class="director-card gsap-tilt">
                        <img src="{{ asset('images/anne_isaac.jpg') }}" alt="Mrs. Anne Isaac, Director" class="director-photo" loading="lazy">
                        <div class="director-info">
                            <h3>Mrs. Anne Isaac</h3>
                            <p>Director</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- History Section -->
    <section class="history-section py-5 bg-light">
        <div class="container">
            <h2 class="section-heading text-center gsap-fade-up">Our History</h2>
            <p class="lead text-center gsap-fade-up" data-delay="0.2">Discover the milestones that have shaped {{ config('app.name') }} into a beacon of innovative education.</p>
            <div class="timeline">
                @foreach([
    ['year' => '2005', 'event' => 'Founded Aunty Anne’s International School with a vision to provide holistic education in Aba.'],
    ['year' => '2010', 'event' => 'Introduced innovative STEM programs, enhancing practical learning for students.'],
    ['year' => '2015', 'event' => 'Expanded campus facilities to include modern science labs and a library.'],
    ['year' => '2020', 'event' => 'Launched community outreach programs to support local education initiatives.'],
    ['year' => '2025', 'event' => 'Celebrated 20 years of excellence with a new scholarship program for talented students.']
] as $index => $milestone)
                    <div class="timeline-item gsap-stagger" data-stagger-delay="{{ $index * 0.1 }}">
                        <div class="timeline-content">
                            <h3>{{ $milestone['year'] }}</h3>
                            <p>{{ $milestone['event'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Impact Section -->
    <section class="impact-section py-5">
        <div class="container">
            <h2 class="section-heading text-center gsap-fade-up">Our Impact</h2>
            <p class="lead text-center gsap-fade-up" data-delay="0.2">See how {{ config('app.name') }} is making a difference in education and beyond.</p>
            <div class="row mt-4">
                @foreach([
    ['stat' => '500+', 'description' => 'Students Graduated with Excellence'],
    ['stat' => '20+', 'description' => 'Community Projects Supported'],
    ['stat' => '95%', 'description' => 'Student Satisfaction Rate'],
    ['stat' => '50+', 'description' => 'Awards and Recognitions']
] as $index => $impact)
                    <div class="col-md-3 col-sm-6 gsap-stagger" data-stagger-delay="{{ $index * 0.1 }}">
                        <div class="impact-card">
                            <h3 class="counter" data-target="{{ preg_replace('/[^0-9]/', '', $impact['stat']) }}">{{ $impact['stat'] }}</h3>
                            <p>{{ $impact['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="about-cta py-5 text-center" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
        <div class="container">
            <h2 class="section-heading">Become Part of Our Journey</h2>
            <p class="lead">Experience the unique education at {{ config('app.name') }}. Reach out or start your admission process today!</p>
            <a href="{{ route('admissions') }}" class="btn btn-pulse mt-3 gsap-scale">Apply Now</a>
        </div>
    </section>

    <!-- Location Section -->
    <section class="location-section py-5">
        <div class="container">
            <h2 class="section-heading gsap-fade-up">Our Campus</h2>
            <p class="lead text-center gsap-fade-up" data-delay="0.2">Come explore our vibrant learning environment in person.</p>
            <div class="map-responsive gsap-scale">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d993.4329494977746!2d7.3309549798887925!3d5.146810527312397!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x10429b8a252a1025%3A0x9ac7285163476022!2sAunty%20Anne's%20International%20School%20Aba!5e0!3m2!1sen!2sng!4v1714916226431!5m2!1sen!2sng" width="600" height="450" style="border: 0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

    <!-- FAQs Section -->
    <section class="faqs-section py-5 bg-light">
        <div class="container">
            <h2 class="section-heading text-center gsap-fade-up">Frequently Asked Questions</h2>
            <div class="accordion" id="faqAccordion">
                @foreach([
    ['question' => 'What are the school hours?', 'answer' => 'Our school hours are from 7:00 AM to 3:30 PM, Monday to Friday. After-school activities are available until 5:00 PM.'],
    ['question' => 'What is the admission process?', 'answer' => 'The admission process involves submitting an application form, attending an entrance assessment, and completing the enrollment process. Visit our <a href="' . route('admissions') . '">Admissions page</a> for more details.'],
    ['question' => 'What are the school fees?', 'answer' => 'School fees vary by program. Please contact us at <a href="' . route('contact') . '">contact</a> for a detailed fee structure.'],
    ['question' => 'Do you offer transportation services?', 'answer' => "No. Our transportation services aren't functional, but we are open to support in that area."]
] as $index => $faq)
                    <div class="accordion-item gsap-stagger" data-stagger-delay="{{ $index * 0.1 }}">
                        <h2 class="accordion-header" id="heading{{ $index + 1 }}">
                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index + 1 }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index + 1 }}">
                                {{ $faq['question'] }}
                            </button>
                        </h2>
                        <div id="collapse{{ $index + 1 }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index + 1 }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {!! $faq['answer'] !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            // GSAP Animations
            document.addEventListener('DOMContentLoaded', () => {
                // Intro Section Animations
                gsap.from('.gsap-fade-up', {
                    opacity: 0,
                    y: 50,
                    duration: 1,
                    ease: 'power3.out',
                    stagger: { each: 0.2, from: 'start' }
                });

                // Main Section Animations
                gsap.from('.gsap-slide-left', {
                    scrollTrigger: {
                        trigger: '.about-main',
                        start: 'top 80%',
                        toggleActions: 'play none none reset'
                    },
                    opacity: 0,
                    x: -100,
                    duration: 1,
                    ease: 'power3.out'
                });

                gsap.from('.gsap-slide-right', {
                    scrollTrigger: {
                        trigger: '.about-main',
                        start: 'top 80%',
                        toggleActions: 'play none none reset'
                    },
                    opacity: 0,
                    x: 100,
                    duration: 1,
                    ease: 'power3.out'
                });

                // 3D Tilt Effect for Director Card
                gsap.from('.gsap-tilt', {
                    scrollTrigger: {
                        trigger: '.about-main',
                        start: 'top 80%',
                        toggleActions: 'play none none reset'
                    },
                    opacity: 0,
                    rotationX: 20,
                    rotationY: 20,
                    duration: 1,
                    ease: 'power3.out'
                });

                // History and Impact Section Animations
                ['.history-section', '.impact-section'].forEach(section => {
                    gsap.from(`${section} .gsap-stagger`, {
                        scrollTrigger: {
                            trigger: section,
                            start: 'top 80%',
                            toggleActions: 'play none none reset'
                        },
                        opacity: 0,
                        y: 50,
                        duration: 0.8,
                        ease: 'power3.out',
                        stagger: { each: 0.1, from: 'center' }
                    });
                });

                // Counter Animation for Impact Section
                document.querySelectorAll('.counter').forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'));
                    gsap.fromTo(counter, 
                        { innerText: 0 },
                        {
                            innerText: target,
                            duration: 2,
                            ease: 'power1.out',
                            snap: { innerText: 1 },
                            scrollTrigger: {
                                trigger: '.impact-section',
                                start: 'top 80%',
                                toggleActions: 'play none none reset'
                            }
                        }
                    );
                });

                // CTA, Location, and FAQs Animations
                ['.about-cta', '.location-section', '.faqs-section'].forEach(section => {
                    gsap.from(`${section} .gsap-fade-up`, {
                        scrollTrigger: {
                            trigger: section,
                            start: 'top 80%',
                            toggleActions: 'play none none reset'
                        },
                        opacity: 0,
                        y: 50,
                        duration: 1,
                        ease: 'power3.out',
                        stagger: { each: 0.2, from: 'start' }
                    });

                    gsap.from(`${section} .gsap-scale`, {
                        scrollTrigger: {
                            trigger: section,
                            start: 'top 80%',
                            toggleActions: 'play none none reset'
                        },
                        opacity: 0,
                        scale: 0.9,
                        duration: 1,
                        ease: 'power3.out'
                    });
                });
            });
        </script>
    @endpush
@endsection
