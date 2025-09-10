@extends('layouts.app')

@section('title', 'Home | ' . config('app.name'))

@push('styles')
    <style>
        /* Carousel transitions */
        .carousel-item {
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .carousel-item:not(.active) {
            opacity: 0;
            transform: translateX(50px);
            pointer-events: none;
        }

        .carousel-item.active {
            opacity: 1;
            transform: translateX(0);
        }

        /* Responsive image adjustments */
        .card-img-top {
            object-fit: cover;
            height: 200px;
        }

        @media (max-width: 576px) {
            .card-img-top {
                height: 150px;
            }
        }
    </style>
@endpush

@section('content')
        <div class="hero" id="home">
            <div class="hero-background"></div>
            <div class="floating-elements">
                <div class="floating-element d-none d-md-block"
                    style="width: 100px; height: 100px; left: 10%; animation-delay: 0s;" data-parallax-speed="0.3"></div>
                <div class="floating-element d-none d-md-block"
                    style="width: 150px; height: 150px; left: 70%; animation-delay: -5s;" data-parallax-speed="0.5"></div>
                <div class="floating-element d-none d-md-block"
                    style="width: 80px; height: 80px; left: 40%; animation-delay: -10s;" data-parallax-speed="0.2"></div>
                <div class="floating-element d-none d-md-block"
                    style="width: 120px; height: 120px; left: 85%; animation-delay: -15s;" data-parallax-speed="0.4"></div>
            </div>
            <div class="hero-content container text-center">
                <h1 class="hero-title display-4 display-md-3 gsap-fade-down" id="heroTitle">{{ config('app.name') }}</h1>
                <h2 class="hero-subtitle lead my-4 gsap-fade-up">Empowering Young Minds Through Practical Learning</h2>
                <p class="hero-subtitle gsap-fade-up">We craft transformative educational journeys, blending hands-on skills
                    with
                    unwavering confidence to shape tomorrow’s leaders.</p>
                <div class="hero-buttons d-flex justify-content-center gap-3 flex-wrap">
                    <a href="#programs" class="btn btn-primary">Explore Programs</a>
                    <a href="#admissions" class="btn btn-secondary">Apply Now</a>
                </div>
            </div>
        </div>

        <section id="about" class="about py-5 bg-light">
            <div class="container">
                <h2 class="section-heading mb-4 gsap-fade-up text-center">About Us</h2>
                <div class="row">
                    <div>
                        <p class="about-text">{{ config('app.name') }} (AAIS), established in 2005, is a leading educational
                            institution in Aba, Nigeria, committed to nurturing young minds and
                            offering a well-rounded education. With a strong emphasis on practical learning, knowledge, and
                            building
                            confidence, this sets us apart as a leading institution for excellence in education.</p>
                        <p class="about-text">Located at No. 6 Oomnne Drive, Abayi, Aba, AAIS serves a diverse student body,
                            offering a
                            nurturing environment where every child can thrive. We emphasize practical learning, encouraging
                            students to apply
                            knowledge through projects, experiments, and community engagement.</p>
                        <div class="text-center">
                            <a href="{{ route('about') }}" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="container my-5">
            <div class="row g-4">
                <div class="col-12 col-md-4 gsap-fade-up">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="my-0">Practical Learning</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title text-center">Our <small class="text-muted">Motto</small></h1>
                            <p class="lead">We believe in hands-on, experiential learning that prepares students for real-world
                                challenges.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 gsap-fade-up">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-info text-white text-center">
                            <h4 class="my-0">Knowledge Empowerment</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title text-center">Our <small class="text-muted">Values</small></h1>
                            <p class="lead">We strive to empower our students with a deep understanding of diverse subjects,
                                fostering intellectual curiosity and a love for learning.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 gsap-fade-up">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-success text-white text-center">
                            <h4 class="my-0">Confidence Building</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title text-center">Our <small class="text-muted">Vision</small></h1>
                            <p class="lead">We focus on building confidence and resilience in our students, ensuring they have
                                the self-assurance to pursue their dreams.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="programs" class="py-5 bg-light">
            <div class="container">
                <h2 class="section-heading text-center mb-5 gsap-fade-up">Our Educational Programs</h2>
                <div class="row g-4">
                    <div class="col-12 col-md-4 gsap-fade-up">
                        <div class="card h-100">
                            <img src="{{ asset('images/SAM_2122.jpg') }}" alt="Early Years Program" class="card-img-top"
                                loading="lazy">
                            <div class="card-body">
                                <h3 class="card-title text-center">Early Years</h3>
                                <p class="lead">A nurturing foundation with play-based learning to spark curiosity and social
                                    skills for ages 2-5.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 gsap-fade-up">
                        <div class="card h-100">
                            <img src="{{ asset('images/SAM_2128.jpg') }}" alt="Primary Program" class="card-img-top"
                                loading="lazy">
                            <div class="card-body">
                                <h3 class="card-title text-center">Primary Education</h3>
                                <p class="lead">A balanced curriculum blending academics, arts, and practical skills for ages
                                    6-11.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 gsap-fade-up">
                        <div class="card h-100">
                            <img src="{{ asset('images/SAM_2129.jpg') }}" alt="Secondary Program" class="card-img-top"
                                loading="lazy">
                            <div class="card-body">
                                <h3 class="card-title text-center">Secondary Education</h3>
                                <p class="lead">Advanced learning with career-focused electives and leadership opportunities for
                                    ages 12-17.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('contact') }}" class="btn btn-primary">Inquire About Programs</a>
                </div>
            </div>
        </section>

        <section id="achievements" class="py-5">
            <div class="container text-center">
                <h2 class="section-heading mb-5 gsap-fade-up">Our Achievements</h2>
                <div class="row g-4">
                    <div class="col-12 col-sm-6 col-md-3 gsap-fade-up">
                        <div class="card h-100">
                            <div class="card-body">
                                <i class="fas fa-trophy fa-3x mb-3 achievement-icon"></i>
                                <h3>95% Pass Rate</h3>
                                <p>Consistently high performance in national exams.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 gsap-fade-up">
                        <div class="card h-100">
                            <div class="card-body">
                                <i class="fas fa-award fa-3x mb-3 achievement-icon"></i>
                                <h3>Top Academic Awards</h3>
                                <p>Multiple wins in regional academic competitions.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 gsap-fade-up">
                        <div class="card h-100">
                            <div class="card-body">
                                <i class="fas fa-users fa-3x mb-3 achievement-icon"></i>
                                <h3>Community Impact</h3>
                                <p>Recognized for outstanding community service projects.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 gsap-fade-up">
                        <div class="card h-100">
                            <div class="card-body">
                                <i class="fas fa-graduation-cap fa-3x mb-3 achievement-icon"></i>
                                <h3>100% University Placement</h3>
                                <p>All graduates accepted into top institutions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <div class="container mt-5 mb-5">
        <h2 class="section-heading text-center gsap-fade-up">What People Say About Us</h2>
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="0" class="active"
                    aria-current="true"></button>
                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <blockquote class="testimonial">
                        <p>{{ config('app.name') }} has provided me with a nurturing environment to grow both academically
                            and personally. The school's dedication and innovative approach to education have truly set me
                            on a path to success.</p>
                        <footer>- Mrs. Anyanwu, Alumni</footer>
                    </blockquote>
                </div>
                <div class="carousel-item">
                    <blockquote class="testimonial">
                        <p>As a parent, I am extremely pleased with the holistic education my child is receiving at
                            {{ config('app.name') }}. The emphasis on both academics and character development is
                            remarkable, and I have seen tremendous growth in my child's confidence and abilities.
                        </p>
                        <footer>- Mr. Ezechukwu Chukwudi, Parent</footer>
                    </blockquote>
                </div>
                <div class="carousel-item">
                    <blockquote class="testimonial">
                        <p>The teachers at {{ config('app.name') }} are incredibly supportive and dedicated. They go above
                            and beyond to ensure each student understands the material and feels confident in their
                            abilities.</p>
                        <footer>- Victoria Uwadiegwu, Current Student</footer>
                    </blockquote>
                </div>
                <div class="carousel-item">
                    <blockquote class="testimonial">
                        <p>I have seen my child's academic performance and social skills improve significantly since joining
                            {{ config('app.name') }}. The school's focus on a well-rounded education is truly commendable.
                        </p>
                        <footer>- Beckley Gideon, Parent</footer>
                    </blockquote>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span><span
                    class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <section id="parent-resources" class="py-5 bg-light">
        <div class="container">
            <h2 class="section-heading text-center mb-5 gsap-fade-up">Resources for Parents</h2>
            <div class="row g-4">
                <div class="col-12 col-md-6 gsap-fade-up">
                    <h3 class="text-center">Admission Process</h3>
                    <p class="lead">Step-by-step guidance on how to enroll your child, including forms and
                        deadlines.</p>
                    <a href="{{ route('admissions') }}" class="btn btn-primary">Start Admission</a>
                </div>
                <div class="col-12 col-md-6 gsap-fade-up">
                    <h3 class="text-center">Parent Handbook</h3>
                    <p class="lead">Download our handbook for policies, schedules, and tips to support your
                        child’s journey.</p>
                    <a href="/pdf/parent_handbook.pdf" class="btn btn-primary" download>Download Now</a>
                </div>
            </div>
        </div>
    </section>

        <section id="enroll-now" class="py-5 text-center"
            style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); color: var(--white);">
            <div class="container">
                <h2 class="section-heading mb-4 gsap-fade-up">Ready to Join Us?</h2>
                <p class="lead gsap-fade-up">Give your child the gift of a world-class education. Enroll today and secure
                    their future!</p>
                <a href="{{ route('admissions') }}" class="btn btn-secondary mt-3" style="color: #f5f5f5;">Enroll Now</a>
            </div>
        </section>

        <section id="contact-us" class="py-5">
            <div class="container text-center">
                <h2 class="section-heading mb-4 gsap-fade-up">Get in Touch</h2>
                <p class="gsap-fade-up">We’d love to hear from you! Contact us for any inquiries or support.</p>
                <a href="{{ route('contact') }}" class="btn btn-primary">Contact Us</a>
            </div>
        </section>
@endsection
