@extends('layouts.app')

@section('title', 'Admissions | ' . config('app.name'))

@push('styles')
    <style>
        /* Admissions Page Specific Styles */
        /* Hero Section */
        .admissions-hero {
            position: relative;
            padding: 4rem 0;
            background: linear-gradient(45deg, var(--primary-green), var(--dark-green));
            color: var(--white);
            text-align: center;
            overflow: hidden;
        }

        .admissions-hero::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100"><path fill="%23ffffff" d="M0,0C240,60 480,100 720,80C960,60 1200,20 1440,60V100H0Z"/></svg>') no-repeat center bottom;
            background-size: cover;
        }

        .admissions-hero .intro-title {
            font-family: var(--font-display);
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }

        .admissions-hero .intro-subtitle {
            font-family: var(--font-primary);
            font-size: clamp(1.2rem, 2.5vw, 1.8rem);
            font-weight: 300;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Admission Process Section */
        .admissions-process {
            padding: 5rem 0;
            background: var(--light-gray);
        }

        .admissions-process .section-heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--dark-green);
            margin-bottom: 1.5rem;
        }

        .process-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .process-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .process-card i {
            font-size: 3rem;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .process-card h3 {
            font-family: var(--font-display);
            font-size: 1.8rem;
            color: var(--dark-green);
            margin-bottom: 1rem;
        }

        .process-card p {
            font-family: var(--font-primary);
            font-size: 1.1rem;
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
        }

        .process-card .btn {
            background: var(--gold);
            color: var(--dark-green);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .process-card .btn:hover {
            background: var(--primary-green);
            color: var(--white);
        }

        /* Admission Requirements Section */
        .admissions-requirements {
            padding: 5rem 0;
        }

        .requirements-list {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .requirements-list:hover {
            transform: translateY(-10px);
        }

        .requirements-list ul {
            padding-left: 0;
            list-style: none;
        }

        .requirements-list li {
            position: relative;
            padding-left: 30px;
            margin-bottom: 0.8rem;
            font-family: var(--font-primary);
            font-size: 1.1rem;
            color: var(--dark-gray);
        }

        .requirements-list li::before {
            content: "★";
            position: absolute;
            left: 0;
            top: 2px;
            color: var(--gold);
            font-size: 1.2rem;
        }

        /* CTA Section */
        .admissions-cta {
            padding: 5rem 0;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: var(--white);
            text-align: center;
        }

        .admissions-cta .section-heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.5rem);
            margin-bottom: 1rem;
        }

        .admissions-cta .lead {
            font-family: var(--font-primary);
            font-size: 1.3rem;
            max-width: 800px;
            margin: 0 auto 1.5rem;
        }

        .admissions-cta .btn-pulse {
            background: var(--gold);
            color: var(--dark-green);
            font-weight: 600;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            animation: pulse 2s infinite ease-in-out;
        }

        .admissions-cta .btn-pulse:hover {
            background: var(--white);
            animation: none;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* FAQs Section */
        .admissions-faq {
            padding: 5rem 0;
            background: var(--light-gray);
        }

        .admissions-faq .section-heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--dark-green);
            margin-bottom: 1.5rem;
        }

        .admissions-faq .accordion-button {
            font-family: var(--font-display);
            font-size: 1.3rem;
            color: var(--dark-green);
            border-left: 5px solid var(--primary-green);
        }

        .admissions-faq .accordion-button:not(.collapsed) {
            background: linear-gradient(90deg, var(--light-gray), var(--white));
            color: var(--primary-green);
        }

        .admissions-faq .accordion-body {
            font-family: var(--font-primary);
            font-size: 1.1rem;
            color: var(--dark-gray);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .admissions-hero .intro-title {
                font-size: clamp(2rem, 4vw, 2.5rem);
            }

            .admissions-hero .intro-subtitle {
                font-size: clamp(1rem, 2vw, 1.5rem);
            }

            .process-card h3,
            .admissions-faq .accordion-button {
                font-size: 1.5rem;
            }

            .requirements-list li,
            .admissions-faq .accordion-body {
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .admissions-hero .intro-title {
                font-size: clamp(1.8rem, 3.5vw, 2rem);
            }

            .admissions-hero .intro-subtitle {
                font-size: clamp(0.9rem, 1.8vw, 1.2rem);
            }

            .process-card h3,
            .admissions-faq .accordion-button {
                font-size: 1.3rem;
            }

            .process-card p,
            .requirements-list li,
            .admissions-faq .accordion-body {
                font-size: 0.9rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="admissions-hero">
        <div class="container text-center">
            <h1 class="intro-title gsap-fade-up">Join Our Community</h1>
            <p class="intro-subtitle gsap-fade-up" data-delay="0.2">Discover how to enroll your child at Aunty Anne’s International School and start their journey to excellence.</p>
        </div>
    </section>

    <!-- Admission Process Section -->
    <section class="admissions-process py-5 bg-light">
        <div class="container">
            <h2 class="section-heading text-center gsap-fade-up">Admission Process</h2>
            <p class="lead text-center gsap-fade-up" data-delay="0.2">Follow these simple steps to join our school community.</p>
            <div class="row mt-4">
                <div class="col-md-4 col-sm-6 gsap-stagger" data-stagger-delay="0">
                    <div class="process-card">
                        <i class="fas fa-file-alt"></i>
                        <h3>Step 1: Application</h3>
                        <p>Download and fill out the application form. Submit it along with the required documents.</p>
                        <a href="{{ asset('pdf/application_form.pdf') }}" class="btn" download>Download Form</a>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 gsap-stagger" data-stagger-delay="0.1">
                    <div class="process-card">
                        <i class="fas fa-calendar-check"></i>
                        <h3>Step 2: Assessment</h3>
                        <p>Schedule an entrance assessment for your child to evaluate their readiness.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 gsap-stagger" data-stagger-delay="0.2">
                    <div class="process-card">
                        <i class="fas fa-check-circle"></i>
                        <h3>Step 3: Enrollment</h3>
                        <p>Upon successful assessment, complete the enrollment process and pay the fees.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Admission Requirements Section -->
    <section class="admissions-requirements py-5">
        <div class="container">
            <h2 class="section-heading text-center">Admission Requirements</h2>
            <p class="lead text-center">Ensure you have the following documents ready for a smooth application process.</p>
            <div class="row justify-content-center">
                <div class="col-md-8 gsap-stagger" data-stagger-delay="0">
                    <div class="requirements-list">
                        <ul>
                            <li>Completed application form</li>
                            <li>Birth certificate (copy)</li>
                            <li>Recent passport photographs (2)</li>
                            <li>Previous school records (if applicable)</li>
                            <li>Proof of payment of application fee</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="admissions-cta py-5 text-center">
        <div class="container">
            <h2 class="section-heading">Ready to Apply?</h2>
            <p class="lead">Take the first step towards a world-class education for your child.</p>
            <a href="#contact" class="btn btn-pulse mt-3 gsap-scale">Contact Us for Inquiries</a>
        </div>
    </section>

    <!-- FAQs Section -->
    <section class="admissions-faq py-5 bg-light">
        <div class="container">
            <h2 class="section-heading text-center gsap-fade-up">Frequently Asked Questions</h2>
            <p class="lead text-center gsap-fade-up" data-delay="0.2">Find answers to common questions about our admissions process.</p>
            <div class="accordion" id="admissionsAccordion">
                <div class="accordion-item gsap-stagger" data-stagger-delay="0">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            What is the application deadline?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#admissionsAccordion">
                        <div class="accordion-body">
                            Applications are accepted year-round, contact the school for further details.
                        </div>
                    </div>
                </div>
                <div class="accordion-item gsap-stagger" data-stagger-delay="0.1">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Are there scholarships available?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#admissionsAccordion">
                        <div class="accordion-body">
                            Yes, we offer merit-based scholarships for exceptional students. Contact our admissions office for more details.
                        </div>
                    </div>
                </div>
                <div class="accordion-item gsap-stagger" data-stagger-delay="0.2">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            What is the assessment process like?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#admissionsAccordion">
                        <div class="accordion-body">
                            The assessment includes a written test and an interview to evaluate your child’s academic readiness and social skills.
                        </div>
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

            // Admission Process, Requirements, and FAQs Section Animations
            ['.admissions-process', '.admissions-requirements', '.admissions-faq'].forEach(section => {
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

            // CTA Section Animations
            gsap.from('.admissions-cta .gsap-fade-up', {
                scrollTrigger: {
                    trigger: '.admissions-cta',
                    start: 'top 80%',
                    toggleActions: 'play none none reset'
                },
                opacity: 0,
                y: 50,
                duration: 1,
                ease: 'power3.out',
                stagger: { each: 0.2, from: 'start' }
            });

            gsap.from('.admissions-cta .gsap-scale', {
                scrollTrigger: {
                    trigger: '.admissions-cta',
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
