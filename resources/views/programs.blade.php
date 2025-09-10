@extends('layouts.app')

@section('title', 'Our Programs | ' . config('app.name'))

@push('styles')
    <style>
        /* Hero Section */
        .programs-hero {
            position: relative;
            padding: 4rem 0;
            background: linear-gradient(45deg, var(--primary-green), var(--dark-green));
            color: var(--white);
            text-align: center;
            overflow: hidden;
        }

        .programs-hero::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100"><path fill="%23ffffff" d="M0,0C240,60 480,100 720,80C960,60 1200,20 1440,60V100H0Z"/></svg>') no-repeat center bottom;
            background-size: cover;
        }

        .programs-hero .intro-title {
            font-family: var(--font-display);
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }

        .programs-hero .intro-subtitle {
            font-family: var(--font-primary);
            font-size: clamp(1.2rem, 2.5vw, 1.8rem);
            font-weight: 300;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Programs Details Section */
        .programs-details {
            padding: 5rem 0;
            background: var(--light-gray);
        }

        .programs-details .section-heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--dark-green);
            margin-bottom: 1.5rem;
        }

        .program-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .program-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .program-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-bottom: 3px solid var(--gold);
        }

        .program-card .card-body {
            padding: 1.5rem;
        }

        .program-card h3 {
            font-family: var(--font-display);
            font-size: 1.8rem;
            color: var(--dark-green);
            margin-bottom: 1rem;
        }

        .program-card .lead {
            font-family: var(--font-primary);
            font-size: 1.2rem;
            line-height: 1.8;
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
        }

        .program-card ul {
            padding-left: 0;
            list-style: none;
        }

        .program-card ul li {
            position: relative;
            padding-left: 30px;
            margin-bottom: 0.8rem;
            font-family: var(--font-primary);
            font-size: 1.1rem;
            color: var(--dark-gray);
        }

        .program-card ul li::before {
            content: "★";
            position: absolute;
            left: 0;
            top: 2px;
            color: var(--gold);
            font-size: 1.2rem;
        }

        /* CTA Section */
        .programs-cta {
            padding: 5rem 0;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: var(--white);
            text-align: center;
        }

        .programs-cta .section-heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.5rem);
            margin-bottom: 1rem;
        }

        .programs-cta .lead {
            font-family: var(--font-primary);
            font-size: 1.3rem;
            max-width: 800px;
            margin: 0 auto 1.5rem;
        }

        .programs-cta .btn-pulse {
            background: var(--gold);
            color: var(--dark-green);
            font-weight: 600;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            animation: pulse 2s infinite ease-in-out;
        }

        .programs-cta .btn-pulse:hover {
            background: var(--white);
            animation: none;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .programs-hero .intro-title {
                font-size: clamp(2rem, 4vw, 2.5rem);
            }

            .programs-hero .intro-subtitle {
                font-size: clamp(1rem, 2vw, 1.5rem);
            }

            .program-card h3 {
                font-size: 1.5rem;
            }

            .program-card img {
                height: 180px;
            }
        }

        @media (max-width: 576px) {
            .programs-hero .intro-title {
                font-size: clamp(1.8rem, 3.5vw, 2rem);
            }

            .programs-hero .intro-subtitle {
                font-size: clamp(0.9rem, 1.8vw, 1.2rem);
            }

            .program-card h3 {
                font-size: 1.3rem;
            }

            .program-card .lead {
                font-size: 1rem;
            }

            .program-card ul li {
                font-size: 0.9rem;
            }

            .program-card img {
                height: 140px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="programs-hero">
        <div class="container text-center">
            <h1 class="intro-title gsap-fade-up">Our Educational Programs</h1>
            <p class="intro-subtitle gsap-fade-up" data-delay="0.2">Explore our comprehensive programs designed to nurture young minds at every stage.</p>
        </div>
    </section>

    <!-- Programs Details Section -->
    <section class="programs-details py-5 bg-light">
        <div class="container">
            <h2 class="section-heading text-center gsap-fade-up">Our Academic Offerings</h2>
            <p class="lead text-center gsap-fade-up" data-delay="0.2">Tailored programs to spark curiosity, foster growth, and prepare students for the future.</p>
            <div class="row mt-4">
                <div class="col-md-4 col-sm-6 gsap-stagger" data-stagger-delay="0">
                    <div class="program-card">
                        <img src="{{ asset('images/SAM_2122.jpg') }}" alt="Early Years Program" class="card-img-top" loading="lazy">
                        <div class="card-body">
                            <h3>Early Years (Ages 2-5)</h3>
                            <p class="lead">Our Early Years program focuses on play-based learning to spark curiosity, creativity, and social skills. We provide a nurturing environment where children can explore and grow.</p>
                            <ul>
                                <li>Hands-on activities</li>
                                <li>Language and literacy development</li>
                                <li>Social and emotional growth</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 gsap-stagger" data-stagger-delay="0.1">
                    <div class="program-card">
                        <img src="{{ asset('images/SAM_2128.jpg') }}" alt="Primary Program" class="card-img-top" loading="lazy">
                        <div class="card-body">
                            <h3>Primary Education (Ages 6-11)</h3>
                            <p class="lead">Our Primary program offers a balanced curriculum that blends academics, arts, and practical skills, preparing students for future challenges.</p>
                            <ul>
                                <li>Core subjects: Math, Science, English</li>
                                <li>Extracurricular activities</li>
                                <li>Focus on critical thinking</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 gsap-stagger" data-stagger-delay="0.2">
                    <div class="program-card">
                        <img src="{{ asset('images/SAM_2129.jpg') }}" alt="Secondary Program" class="card-img-top" loading="lazy">
                        <div class="card-body">
                            <h3>Secondary Education (Ages 12-17)</h3>
                            <p class="lead">Our Secondary program provides advanced learning with career-focused electives, leadership opportunities, and preparation for higher education.</p>
                            <ul>
                                <li>Specialized subjects</li>
                                <li>Leadership and teamwork projects</li>
                                <li>University preparation</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="programs-cta py-5 text-center">
        <div class="container">
            <h2 class="section-heading">Interested in Our Programs?</h2>
            <p class="lead gsap-fade-up">Contact us to learn more about how we can support your child’s education.</p>
            <a href="#contact" class="btn btn-pulse mt-3 gsap-scale">Get in Touch</a>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // GSAP Animations
        document.addEventListener('DOMContentLoaded', () => {
            // Hero Section Animations
            gsap.from('.gsap-fade-up', {
                opacity: 0,
                y: 50,
                duration: 1,
                ease: 'power3.out',
                stagger: { each: 0.2, from: 'start' }
            });

            // Programs Details Section Animations
            gsap.from('.programs-details .gsap-stagger', {
                scrollTrigger: {
                    trigger: '.programs-details',
                    start: 'top 80%',
                    toggleActions: 'play none none reset'
                },
                opacity: 0,
                y: 50,
                duration: 0.8,
                ease: 'power3.out',
                stagger: { each: 0.1, from: 'center' }
            });

            // CTA Section Animations
            gsap.from('.programs-cta .gsap-fade-up', {
                scrollTrigger: {
                    trigger: '.programs-cta',
                    start: 'top 80%',
                    toggleActions: 'play none none reset'
                },
                opacity: 0,
                y: 50,
                duration: 1,
                ease: 'power3.out',
                stagger: { each: 0.2, from: 'start' }
            });

            gsap.from('.programs-cta .gsap-scale', {
                scrollTrigger: {
                    trigger: '.programs-cta',
                    start: 'top 80%',
                    toggleActions: 'play none none reset'
                },
                opacity: 0,
                scale: 0.9,
                duration: 1,
                ease: 'power3.out'
            });
        });
    </script>
@endpush
