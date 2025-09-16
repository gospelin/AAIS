@extends('admin.layouts.app')

@section('title', 'Edit Student')

@section('description', 'Edit student details for Aunty Anne\'s International School.')

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

        .alert {
            padding: var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-md);
        }

        .alert-success {
            background: rgba(33, 160, 85, 0.1);
            color: var(--primary-green);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--error);
        }

        .text-danger {
            color: var(--error);
        }

        .is-invalid {
            border-color: var(--error);
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
        @if(!$currentSession)
            <div class="alert alert-danger">
                No current academic session is set. Please <a href="{{ route('admin.manage_academic_sessions') }}">set a current session</a> to edit students.
            </div>
        @else
            <div class="form-container">
                <h2 class="form-header">Edit Student: {{ $student->full_name ?? 'Unknown' }} ({{ $currentClassName }})</h2>
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form method="POST" action="{{ route('admin.students.update', $student->id) }}" id="editStudentForm">
                    @csrf
                    @method('PUT')

                    {{-- Student Personal Information --}}
                    <div class="form-section">
                        <h3 class="form-section-title">Personal Information</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="first_name" class="form-label required">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                       value="{{ old('first_name', $student->first_name) }}" placeholder="Enter first name" required>
                                @error('first_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" id="middle_name" class="form-control @error('middle_name') is-invalid @enderror" 
                                       value="{{ old('middle_name', $student->middle_name) }}" placeholder="Enter middle name (optional)">
                                @error('middle_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="last_name" class="form-label required">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                       value="{{ old('last_name', $student->last_name) }}" placeholder="Enter last name" required>
                                @error('last_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="gender" class="form-label required">Gender</label>
                                <select name="gender" id="gender" class="form-control form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}" placeholder="Select date of birth (optional)">
                                @error('date_of_birth')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="religion" class="form-label">Religion</label>
                                <input type="text" name="religion" id="religion" class="form-control @error('religion') is-invalid @enderror" 
                                       value="{{ old('religion', $student->religion) }}" placeholder="Enter religion (optional)">
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
                                <label for="parent_name" class="form-label">Parent/Guardian Name</label>
                                <input type="text" name="parent_name" id="parent_name" class="form-control @error('parent_name') is-invalid @enderror" 
                                       value="{{ old('parent_name', $student->parent_name) }}" placeholder="Enter parent/guardian name (optional)">
                                @error('parent_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="parent_phone_number" class="form-label">Parent/Guardian Phone Number</label>
                                <input type="tel" name="parent_phone_number" id="parent_phone_number" class="form-control @error('parent_phone_number') is-invalid @enderror" 
                                       value="{{ old('parent_phone_number', $student->parent_phone_number) }}" placeholder="Enter phone number (optional)">
                                @error('parent_phone_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="parent_occupation" class="form-label">Parent/Guardian Occupation</label>
                                <input type="text" name="parent_occupation" id="parent_occupation" class="form-control @error('parent_occupation') is-invalid @enderror" 
                                       value="{{ old('parent_occupation', $student->parent_occupation) }}" placeholder="Enter occupation (optional)">
                                @error('parent_occupation')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" 
                                          placeholder="Enter full address (optional)">{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="state_of_origin" class="form-label">State of Origin</label>
                                <input type="text" name="state_of_origin" id="state_of_origin" class="form-control @error('state_of_origin') is-invalid @enderror" 
                                       value="{{ old('state_of_origin', $student->state_of_origin) }}" placeholder="Enter state of origin (optional)">
                                @error('state_of_origin')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="local_government_area" class="form-label">Local Government Area</label>
                                <input type="text" name="local_government_area" id="local_government_area" class="form-control @error('local_government_area') is-invalid @enderror" 
                                       value="{{ old('local_government_area', $student->local_government_area) }}" placeholder="Enter LGA (optional)">
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
                                <label for="class_id" class="form-label required">Class/Section</label>
                                <select name="class_id" id="class_id" class="form-control form-select @error('class_id') is-invalid @enderror" required>
                                    <option value="">Select Class</option>
                                    @foreach($classChoices as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $currentClassId) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="start_term" class="form-label required">Starting Term</label>
                                <select name="start_term" id="start_term" class="form-control form-select @error('start_term') is-invalid @enderror" required>
                                    <option value="">Select Term</option>
                                    @foreach($termChoices as $term)
                                        <option value="{{ $term->value }}" {{ old('start_term', $currentStartTerm) == $term->value ? 'selected' : '' }}>
                                            {{ $term->label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('start_term')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="bx bx-save"></i> Update Student
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
