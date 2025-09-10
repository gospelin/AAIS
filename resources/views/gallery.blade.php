@extends('layouts.app')

@section('title', 'Gallery | ' . config('app.name'))

@push('styles')
    <style>
        /* Gallery Page Specific Styles */
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

        /* Hero Section */
        .gallery-hero {
            position: relative;
            padding: 4rem 0;
            background: linear-gradient(45deg, var(--primary-green), var(--dark-green));
            color: var(--white);
            text-align: center;
            overflow: hidden;
        }

        .gallery-hero::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100"><path fill="%23ffffff" d="M0,0C240,60 480,100 720,80C960,60 1200,20 1440,60V100H0Z"/></svg>') no-repeat center bottom;
            background-size: cover;
        }

        .gallery-hero .intro-title {
            font-family: var(--font-display);
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }

        .gallery-hero .intro-subtitle {
            font-family: var(--font-primary);
            font-size: clamp(1.2rem, 2.5vw, 1.8rem);
            font-weight: 300;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Gallery Grid Section */
        .gallery-grid {
            padding: 5rem 0;
            background: var(--light-gray);
        }

        .gallery-grid .section-heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--dark-green);
            margin-bottom: 1.5rem;
        }

        .gallery-item {
            position: relative;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-bottom: 3px solid var(--gold);
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.6);
            color: var(--white);
            padding: 1rem;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-overlay p {
            font-family: var(--font-primary);
            font-size: 1.1rem;
            margin: 0;
        }

        /* Video Gallery Section */
        .video-gallery {
            padding: 5rem 0;
        }

        .video-gallery .section-heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--dark-green);
            margin-bottom: 1.5rem;
        }

        .video-responsive {
            position: relative;
            padding-bottom: 56.25%;
            /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border: 3px solid var(--gold);
        }

        .video-responsive iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .gallery-hero .intro-title {
                font-size: clamp(2rem, 4vw, 2.5rem);
            }

            .gallery-hero .intro-subtitle {
                font-size: clamp(1rem, 2vw, 1.5rem);
            }

            .gallery-grid .section-heading,
            .video-gallery .section-heading {
                font-size: 1.8rem;
            }

            .gallery-item img {
                height: 200px;
            }

            .gallery-overlay p {
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .gallery-hero .intro-title {
                font-size: clamp(1.8rem, 3.5vw, 2rem);
            }

            .gallery-hero .intro-subtitle {
                font-size: clamp(0.9rem, 1.8vw, 1.2rem);
            }

            .gallery-grid .section-heading,
            .video-gallery .section-heading {
                font-size: 1.5rem;
            }

            .gallery-item img {
                height: 150px;
            }

            .gallery-overlay p {
                font-size: 0.9rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="gallery-hero">
        <div class="container text-center">
            <h1 class="intro-title gsap-fade-up">Our Gallery</h1>
            <p class="intro-subtitle gsap-fade-up" data-delay="0.2">A glimpse into life at Aunty Anne’s International
                School.</p>
        </div>
    </section>

    <!-- Gallery Grid Section -->
    <section class="gallery-grid py-5 bg-light">
        <div class="container">
            <h2 class="section-heading text-center gsap-fade-up">Photo Gallery</h2>
            <p class="lead text-center gsap-fade-up" data-delay="0.2">Explore moments from our vibrant school community.</p>
            <div class="row">
                <div class="col-md-4 col-sm-6 mb-4 gsap-stagger" data-stagger-delay="0">
                    <div class="gallery-item">
                        <img src="{{ asset('static/images/gallery1.jpg') }}" alt="Classroom Activity" class="img-fluid"
                            loading="lazy">
                        <div class="gallery-overlay">
                            <p>Classroom Activity</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4 gsap-stagger" data-stagger-delay="0.1">
                    <div class="gallery-item">
                        <img src="{{ asset('static/images/gallery2.jpg') }}" alt="Sports Day" class="img-fluid"
                            loading="lazy">
                        <div class="gallery-overlay">
                            <p>Sports Day</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4 gsap-stagger" data-stagger-delay="0.2">
                    <div class="gallery-item">
                        <img src="{{ asset('static/images/gallery3.jpg') }}" alt="Cultural Day" class="img-fluid"
                            loading="lazy">
                        <div class="gallery-overlay">
                            <p>Cultural Day</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4 gsap-stagger" data-stagger-delay="0.3">
                    <div class="gallery-item">
                        <img src="{{ asset('static/images/gallery4.jpg') }}" alt="Science Fair" class="img-fluid"
                            loading="lazy">
                        <div class="gallery-overlay">
                            <p>Science Fair</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4 gsap-stagger" data-stagger-delay="0.4">
                    <div class="gallery-item">
                        <img src="{{ asset('static/images/gallery5.jpg') }}" alt="Graduation Ceremony" class="img-fluid"
                            loading="lazy">
                        <div class="gallery-overlay">
                            <p>Graduation Ceremony</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4 gsap-stagger" data-stagger-delay="0.5">
                    <div class="gallery-item">
                        <img src="{{ asset('static/images/gallery6.jpg') }}" alt="School Trip" class="img-fluid"
                            loading="lazy">
                        <div class="gallery-overlay">
                            <p>School Trip</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Gallery Section -->
    <section class="video-gallery py-5">
        <div class="container">
            <h2 class="section-heading text-center gsap-fade-up">Video Gallery</h2>
            <p class="lead text-center gsap-fade-up" data-delay="0.2">Watch highlights from our school events and
                activities.</p>
            <div class="row">
                <div class="col-md-6 gsap-stagger" data-stagger-delay="0">
                    <div class="video-responsive">
                        <iframe src="https://www.youtube.com/embed/sample-video" title="School Tour" frameborder="0"
                            allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-md-6 gsap-stagger" data-stagger-delay="0.1">
                    <div class="video-responsive">
                        <iframe src="https://www.youtube.com/embed/sample-video2" title="Annual Day Celebration"
                            frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
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

            // Gallery Grid and Video Gallery Section Animations
            ['.gallery-grid', '.video-gallery'].forEach(section => {
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
            });
        });
    </script>
@endpush
