@extends('layouts.app')

@section('title', 'Contact Us | ' . config('app.name'))

@push('styles')
    <style>
        /* Contact Page Specific Styles */

        /* Hero Section */
        .contact-hero {
            position: relative;
            padding: 4rem 0;
            background: linear-gradient(45deg, var(--primary-green), var(--dark-green));
            color: var(--white);
            text-align: center;
            overflow: hidden;
        }

        .contact-hero::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100"><path fill="%23ffffff" d="M0,0C240,60 480,100 720,80C960,60 1200,20 1440,60V100H0Z"/></svg>') no-repeat center bottom;
            background-size: cover;
        }

        .contact-hero .intro-title {
            font-family: var(--font-display);
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }

        .contact-hero .intro-subtitle {
            font-family: var(--font-primary);
            font-size: clamp(1.2rem, 2.5vw, 1.8rem);
            font-weight: 300;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Contact Details Section */
        .contact-details {
            padding: 5rem 0;
            background: var(--light-gray);
        }

        .contact-details .section-heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--dark-green);
            margin-bottom: 1.5rem;
        }

        .contact-info-card, .contact-form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-info-card:hover, .contact-form-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .contact-info-card p {
            font-family: var(--font-primary);
            font-size: 1.1rem;
            color: var(--dark-gray);
            margin-bottom: 1rem;
        }

        .contact-info-card i {
            color: var(--gold);
            margin-right: 0.8rem;
        }

        .contact-info-card a {
            color: var(--primary-green);
            text-decoration: none;
        }

        .contact-info-card a:hover {
            color: var(--dark-green);
        }

        .social-icons a {
            font-size: 1.5rem;
            color: var(--dark-gray);
            margin-right: 1rem;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: var(--gold);
        }

        .contact-form-card .form-label {
            font-family: var(--font-primary);
            font-size: 1.1rem;
            color: var(--dark-green);
        }

        .contact-form-card .form-control {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid var(--gold);
            border-radius: 8px;
            font-family: var(--font-primary);
            font-size: 1rem;
            color: var(--dark-gray);
        }

        .contact-form-card .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 5px rgba(33, 160, 85, 0.5);
        }

        .contact-form-card .form-check-label {
            font-family: var(--font-primary);
            font-size: 1rem;
            color: var(--dark-gray);
        }

        .contact-form-card .btn {
            background: var(--gold);
            color: var(--dark-green);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-family: var(--font-primary);
        }

        .contact-form-card .btn:hover {
            background: var(--primary-green);
            color: var(--white);
        }

        /* Map Section */
        .contact-map {
            padding: 5rem 0;
        }

        .contact-map .section-heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--dark-green);
            margin-bottom: 1.5rem;
        }

        .contact-map .map-responsive {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border: 3px solid var(--gold);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .contact-hero .intro-title {
                font-size: clamp(2rem, 4vw, 2.5rem);
            }

            .contact-hero .intro-subtitle {
                font-size: clamp(1rem, 2vw, 1.5rem);
            }

            .contact-details .section-heading {
                font-size: 1.8rem;
            }

            .contact-info-card p,
            .contact-form-card .form-label,
            .contact-form-card .form-control,
            .contact-form-card .form-check-label {
                font-size: 1rem;
            }

            .social-icons a {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 576px) {
            .contact-hero .intro-title {
                font-size: clamp(1.8rem, 3.5vw, 2rem);
            }

            .contact-hero .intro-subtitle {
                font-size: clamp(0.9rem, 1.8vw, 1.2rem);
            }

            .contact-details .section-heading {
                font-size: 1.5rem;
            }

            .contact-info-card p,
            .contact-form-card .form-label,
            .contact-form-card .form-control,
            .contact-form-card .form-check-label {
                font-size: 0.9rem;
            }

            .social-icons a {
                font-size: 1.2rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container text-center">
            <h1 class="intro-title gsap-fade-up">Get in Touch</h1>
            <p class="intro-subtitle gsap-fade-up" data-delay="0.2">We’re here to answer your questions.</p>
        </div>
    </section>

    <!-- Contact Details Section -->
    <section class="contact-details py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6 gsap-stagger" data-stagger-delay="0">
                    <div class="contact-info-card">
                        <h2 class="section-heading">Contact Information</h2>
                        <p><i class="fas fa-map-marker-alt"></i> No 6 Oomnne Drive, Abayi, Aba, Abia, Nigeria</p>
                        <p><i class="fas fa-phone-alt"></i> +234-806-967-8968</p>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:info@auntyannesschools.com.ng">info@auntyannesschools.com.ng</a></p>
                        <div class="social-icons mt-4">
                            <a href="https://facebook.com/auntyannesschools" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com/auntyannesschools" target="_blank"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com/auntyannesschools" target="_blank"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 gsap-stagger" data-stagger-delay="0.1">
                    <div class="contact-form-card">
                        <h2 class="section-heading">Send Us a Message</h2>
                        <form action="mailto:info@auntyannesschools.com.ng" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="body" rows="5" required maxlength="1000"></textarea>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter" value="1">
                                <label class="form-check-label" for="newsletter">Subscribe to our newsletter</label>
                            </div>
                            <button type="submit" class="btn w-100">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="contact-map py-5">
        <div class="container">
            <h2 class="section-heading text-center">Find Us</h2>
            <p class="lead text-center">Visit our campus in Aba, Nigeria.</p>
            <div class="map-responsive gsap-scale">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d993.4329494977746!2d7.3309549798887925!3d5.146810527312397!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x10429b8a252a1025%3A0x9ac7285163476022!2sAunty%20Anne's%20International%20School%20Aba!5e0!3m2!1sen!2sng!4v1714916226431!5m2!1sen!2sng" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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

            // Contact Details Section Animations
            gsap.from('.contact-details .gsap-stagger', {
                scrollTrigger: {
                    trigger: '.contact-details',
                    start: 'top 80%',
                    toggleActions: 'play none none reset'
                },
                opacity: 0,
                y: 50,
                duration: 0.8,
                ease: 'power3.out',
                stagger: { each: 0.1, from: 'center' }
            });

            // Map Section Animations
            gsap.from('.contact-map .gsap-fade-up', {
                scrollTrigger: {
                    trigger: '.contact-map',
                    start: 'top 80%',
                    toggleActions: 'play none none reset'
                },
                opacity: 0,
                y: 50,
                duration: 1,
                ease: 'power3.out',
                stagger: { each: 0.2, from: 'start' }
            });

            gsap.from('.contact-map .gsap-scale', {
                scrollTrigger: {
                    trigger: '.contact-map',
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
