<p align="center"><a href="https://auntyannesschools.com.ng" target="_blank"><img src="https://auntyannesschools.com.ng/images/school_logo.png" width="400" alt="School Logo"></a></p>

# Aunty Anne's International School (AAIS) - School Management System

**Aunty Anne's International School (AAIS)** is a web-based school management system built with **Laravel 12.28.1** and **PHP 8.2.12**. It provides administrative tools to manage classes, students, academic sessions, fee payments, and student class histories. The system features a modern, responsive UI with glassmorphism design, powered by Tailwind CSS, GSAP animations, and Boxicons. It includes robust filtering, pagination, and auditing capabilities for administrative tasks.

## Table of Contents
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Directory Structure](#directory-structure)
- [Usage](#usage)
  - [Managing Classes](#managing-classes)
  - [Managing Students](#managing-students)
  - [Filtering and Searching](#filtering-and-searching)
  - [Audit Logging](#audit-logging)
- [Database Schema](#database-schema)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## Features
- **Class Management**:
  - Create, edit, and delete classes with unique names and hierarchy levels.
  - Validate class hierarchy to prevent duplicates.
  - Prevent deletion of classes with active student enrollments.
- **Student Management**:
  - View students by class with pagination (10 per page).
  - Promote or demote students to higher/lower classes based on hierarchy.
  - Delete student class records with soft deactivation (sets `leave_date` and `is_active=false`).
- **Filtering and Searching**:
  - Filter students by enrollment status (active/inactive), fee status (paid/unpaid), and approval status (approved/pending).
  - Search students by name or registration number within a class.
- **Session and Term Management**:
  - Tracks academic sessions and terms (First, Second, Third) using `AcademicSession` and `TermEnum`.
  - Filters students based on session and term-specific class history.
- **Audit Logging**:
  - Logs administrative actions (e.g., class creation, student promotion) to `AuditLog` with user ID and timestamp.
- **UI/UX**:
  - Glassmorphism design with Tailwind CSS variables (e.g., `--glass-bg`, `--primary-green`).
  - GSAP animations for smooth transitions (forms, tables, pagination).
  - Responsive design with mobile-friendly layouts.
  - Custom pagination template (`vendor.pagination.custom`).
- **Security**:
  - Authorization checks via `manage_classes` permission.
  - CSRF protection on forms.
  - Validation for all inputs (e.g., unique class names, hierarchy).

## Tech Stack
- **Backend**: Laravel 12.28.1, PHP 8.2.12
- **Frontend**: Blade templates, Tailwind CSS, GSAP (GreenSock Animation Platform), Boxicons
- **Database**: MySQL (assumed; configurable via `.env`)
- **Dependencies**:
  - `illuminate/support` for collections and utilities
  - `illuminate/database` for Eloquent and query building
  - `spatie/laravel-enum` for `TermEnum` (assumed for term handling)
- **Environment**: Local development with `localhost:8000`

## Prerequisites
- **PHP**: 8.2.12 or higher
- **Composer**: 2.x
- **MySQL**: 8.0 or higher (or compatible DB)
- **Node.js**: 16.x or higher (for Tailwind CSS compilation)
- **Git**: For cloning the repository
- **Laravel CLI**: Optional for artisan commands

## Installation
1. **Clone the Repository**:
```bash
git clone https://github.com/gospelin/AAIS.git
cd AAIS
```

2. **Install PHP Dependencies**:

```bash
composer install
```

3. **Install Frontend Dependencies (for Tailwind CSS)**:
```bash
npm install
npm run dev
```

## Set Up Environment:
1.  **Copy .env.example to .env**:
```bash
cp .env.example .env
```

2. **Configure .env**:
APP_NAME="Aunty Anne's International School"
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aais
DB_USERNAME=your_username
DB_PASSWORD=your_password

3. **Generate Application Key**:
```bash
php artisan key:generate
```

4. **Run Migrations**:
```bash
php artisan migrate
```

5. **Seed the database**:
```bash
php artisan db:seed
```

6. **Clear Caches**:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

7. **Serve the Application**:
```bash
php artisan serve
```

8. Access at http://localhost:8000.



## Configuration

Database: Ensure MySQL is running and the **AAIS** database is created.
Tailwind CSS: Configure tailwind.config.js if custom variables (e.g., --primary-green, --glass-bg) are needed.
GSAP/Boxicons: Ensure CDN or local assets are included in admin.layouts.app.
Custom Pagination: Verify resources/views/vendor/pagination/custom.blade.php exists (uses Tailwind classes for styling).

## Time Zone: 
Set APP_TIMEZONE=Africa/Lagos in .env.

Directory Structure
AAIS/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminBaseController.php
│   │   │   │   ├── AdminClassController.php
│   ├── Models/
│   │   ├── AcademicSession.php
│   │   ├── AuditLog.php
│   │   ├── Classes.php
│   │   ├── Student.php
│   │   ├── StudentClassHistory.php
│   │   ├── FeePayment.php
│   ├── Enums/
│   │   ├── TermEnum.php
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   ├── classes/
│   │   │   │   ├── manage_classes.blade.php
│   │   │   │   ├── edit_class.blade.php
│   │   │   │   ├── delete_class.blade.php
│   │   │   │   ├── select_class.blade.php
│   │   │   │   ├── students_by_class.blade.php
│   │   │   │   ├── search_results.blade.php
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   ├── views/vendor/pagination/
│   │   ├── custom.blade.php
├── routes/
│   ├── web.php
├── public/
│   ├── css/
│   │   ├── app.css (Tailwind output)
├── storage/
│   ├── logs/
│   │   ├── laravel.log
├── .env
├── composer.json
├── package.json
├── tailwind.config.js

## Usage
1. Managing Classes

**Access**: Navigate to /admin/classes.
**Create**: POST to /admin/classes with name, section (optional), hierarchy (unique, min:1).
**Edit**: GET /admin/classes/{id}/edit, update via POST.
**Delete**: GET /admin/classes/{id}/delete, confirm via DELETE. Blocked if active students exist.
**Validation**: Unique name and hierarchy, max 50 chars for name, 20 for section.

2. Managing Students

**Select Class**: GET /admin/classes/select/{action} (e.g., view_students, promote, demote, delete_from_class).
**View Students**: GET /admin/classes/{className}/students/{action} with pagination (10 per page).
**Promote/Demote**: POST to /admin/classes/{className}/students/{studentId}/{action} (e.g., promote, demote). Updates StudentClassHistory with leave_date, is_active, and new record.
**Delete Record**: POST to /admin/classes/{className}/students/{studentId}/delete_from_class. Soft-deletes by setting leave_date and is_active=false.

3. Filtering and Searching

**Filters**: In students_by_class.blade.php, filter by:
**Enrollment Status**: active, inactive, all.
**Fee Status**: paid, unpaid, all.
**Approval Status**: approved, pending, all.
Persists via query params (e.g., ?enrollment_status=active).


4. Search: In search_results.blade.php, search by name or registration number within a class.

Audit Logging

All actions (create, update, delete, promote, demote) log to AuditLog with user_id, action, and timestamp.

Database Schema
Tables

students:
id, first_name, last_name, reg_no, approved (boolean), created_at, updated_at.


classes:
id, name (varchar, unique), section (varchar, nullable), hierarchy (integer, unique, min:1), created_at, updated_at.


student_class_history:
id, student_id (foreign key), session_id (foreign key), class_id (foreign key), start_term (enum: First, Second, Third), end_term (enum, nullable), join_date, leave_date (nullable), is_active (boolean), created_at, updated_at.


fee_payments:
id, student_id (foreign key), session_id (foreign key), term (enum: First, Second, Third), has_paid_fee (boolean), created_at, updated_at.


academic_sessions:
id, name, is_current (boolean), created_at, updated_at.


audit_logs:
id, user_id (foreign key), action (text), timestamp, created_at, updated_at.



Relationships

Student has many StudentClassHistory and FeePayment.
Classes has many StudentClassHistory.
AcademicSession has many StudentClassHistory and FeePayment.

Testing

Clear Caches:
```bash
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear
```

Unit Tests (if implemented):php artisan test


Manual Testing:
Visit /admin/classes → Create/edit/delete classes.
Visit /admin/classes/select/view_students → Select class → View students with pagination.
Apply filters (enrollment_status=active, etc.) → Verify pagination and data.
Search via /admin/classes/{className}/students/search → Check results.
Promote/demote/delete student records → Verify StudentClassHistory updates.


Tinker:php artisan tinker
>>> $controller = app('App\Http\Controllers\Admin\AdminBaseController');
>>> $students = $controller->getStudentsQuery(App\Models\AcademicSession::first(), 'First')->get();
>>> dd($controller->groupStudentsByClass($students));


Logs: Monitor storage/logs/laravel.log:tail -f storage/logs/laravel.log



Troubleshooting

"Call to a member function hasPages() on array":
Ensure studentsByClass passes $students (paginator) to the view.
Use $students->hasPages() and $students->links() in students_by_class.blade.php.


"Missing parameter: className":
Verify route parameters use className (camelCase) in redirects (e.g., ['className' => urlencode($className)]).


N+1 Queries:
In groupStudentsByClass, cache Classes hierarchies or eager load via with('class').


Database Errors:
Run php artisan migrate:fresh if schema issues occur.
Check .env for correct DB credentials.


CSS/JS Issues:
Re-run npm run dev for Tailwind.
Verify GSAP/Boxicons CDNs in admin.layouts.app.



Contributing

Fork the repository.
Create a feature branch (git checkout -b feature/YourFeature).
Commit changes (git commit -m "Add YourFeature").
Push to the branch (git push origin feature/YourFeature).
Open a pull request.

License
This project is licensed under the MIT License. See LICENSE for details.

Aunty Anne's International School
Developed with ❤️ for efficient school administration.
For support, contact [info@auntyannesschools.com.ng].