{{-- resources/views/admin/students/add_student.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Add New Student')

@section('description', 'Register a new student to Aunty Anne\'s International School.')

@push('styles')
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-2xl);
            position: relative;
            overflow: hidden;
            margin-bottom: var(--space-2xl);
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .form-header {
            font-family: var(--font-display);
            font-size: clamp(1.5rem, 3.5vw, 2rem);
            font-weight: 700;
            background: var(--gradient-primary);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .form-section {
            margin-bottom: var(--space-xl);
        }

        .form-section-title {
            font-family: var(--font-display);
            font-size: clamp(1rem, 2.5vw, 1.125rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
            padding-left: var(--space-sm);
            border-left: 3px solid var(--primary-green);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-md);
        }

        .form-group {
            margin-bottom: var(--space-md);
        }

        .form-label {
            display: block;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: var(--space-xs);
        }

        .form-control {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right var(--space-md) center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .btn-submit {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            font-weight: 600;
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            margin-top: var(--space-md);
        }

        .btn-submit:hover,
        .btn-submit:focus-visible {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: var(--white);
        }

        .required::after {
            content: ' *';
            color: var(--error);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: var(--space-sm);
            }

            .form-container {
                padding: var(--space-xl);
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <div class="form-container">
            <h2 class="form-header">Add New Student</h2>
            <form method="POST" action="{{ route('admin.add_student') }}" id="addStudentForm">
                @csrf
                <input type="hidden" name="class_id" id="class_id">
                <input type="hidden" name="start_term" id="start_term">

                {{-- Student Personal Information --}}
                <div class="form-section">
                    <h3 class="form-section-title">Personal Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name" class="form-label required">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                   value="{{ old('first_name') }}" placeholder="Enter first name" required>
                            @error('first_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" class="form-control @error('middle_name') is-invalid @enderror" 
                                   value="{{ old('middle_name') }}" placeholder="Enter middle name (optional)">
                            @error('middle_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="last_name" class="form-label required">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                   value="{{ old('last_name') }}" placeholder="Enter last name" required>
                            @error('last_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="gender" class="form-label required">Gender</label>
                            <select name="gender" id="gender" class="form-control form-select @error('gender') is-invalid @enderror" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="religion" class="form-label">Religion</label>
                            <input type="text" name="religion" id="religion" class="form-control @error('religion') is-invalid @enderror" 
                                   value="{{ old('religion') }}" placeholder="Enter religion (optional)">
                            @error('religion')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Parent/Guardian Information --}}
                <div class="form-section">
                    <h3 class="form-section-title">Parent/Guardian Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="parent_name" class="form-label required">Parent/Guardian Name</label>
                            <input type="text" name="parent_name" id="parent_name" class="form-control @error('parent_name') is-invalid @enderror" 
                                   value="{{ old('parent_name') }}" placeholder="Enter parent/guardian name" required>
                            @error('parent_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="parent_phone_number" class="form-label required">Parent/Guardian Phone Number</label>
                            <input type="tel" name="parent_phone_number" id="parent_phone_number" class="form-control @error('parent_phone_number') is-invalid @enderror" 
                                   value="{{ old('parent_phone_number') }}" placeholder="Enter phone number (e.g., +234xxxxxxxxx)" required>
                            @error('parent_phone_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="parent_occupation" class="form-label">Parent/Guardian Occupation</label>
                            <input type="text" name="parent_occupation" id="parent_occupation" class="form-control @error('parent_occupation') is-invalid @enderror" 
                                   value="{{ old('parent_occupation') }}" placeholder="Enter occupation (optional)">
                            @error('parent_occupation')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="address" class="form-label required">Address</label>
                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" 
                                      placeholder="Enter full address">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="state_of_origin" class="form-label">State of Origin</label>
                            <input type="text" name="state_of_origin" id="state_of_origin" class="form-control @error('state_of_origin') is-invalid @enderror" 
                                   value="{{ old('state_of_origin') }}" placeholder="Enter state of origin (optional)">
                            @error('state_of_origin')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="local_government_area" class="form-label">Local Government Area</label>
                            <input type="text" name="local_government_area" id="local_government_area" class="form-control @error('local_government_area') is-invalid @enderror" 
                                   value="{{ old('local_government_area') }}" placeholder="Enter LGA (optional)">
                            @error('local_government_area')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Academic Information --}}
                <div class="form-section">
                    <h3 class="form-section-title">Academic Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="class_select" class="form-label required">Class/Section</label>
                            <select id="class_select" class="form-control form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classChoices as $class)
                                    <option value="{{ $class->id }}" data-term="{{ $currentTerm->value }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="term_select" class="form-label required">Starting Term</label>
                            <select id="term_select" class="form-control form-select" required>
                                <option value="">Select Term</option>
                                @foreach($termChoices as $term)
                                    <option value="{{ $term->value }}" {{ $term->value == $currentTerm->value ? 'selected' : '' }}>{{ $term->label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="bx bx-user-plus"></i> Register Student
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const classSelect = document.getElementById('class_select');
                const termSelect = document.getElementById('term_select');
                const classIdInput = document.getElementById('class_id');
                const startTermInput = document.getElementById('start_term');
                const form = document.getElementById('addStudentForm');

                function updateHiddenInputs() {
                    classIdInput.value = classSelect.value;
                    startTermInput.value = termSelect.value;
                }

                classSelect.addEventListener('change', updateHiddenInputs);
                termSelect.addEventListener('change', updateHiddenInputs);

                form.addEventListener('submit', (e) => {
                    if (!classSelect.value || !termSelect.value) {
                        e.preventDefault();
                        alert('Please select class and starting term.');
                        return false;
                    }
                });

                // GSAP Animation for form elements
                gsap.from('.form-group', {
                    opacity: 0,
                    y: 20,
                    stagger: 0.1,
                    duration: 0.6,
                    delay: 0.2
                });
            });
        </script>
    @endpush
@endsection
