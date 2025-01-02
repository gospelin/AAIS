"""
Generic functions/routes of the Admin Dashboard

    admin_dashboard: Displays the admin dashboard
    manage_sessions: Manages the current session
    change_session: Changes the current session
"""

"""
Route for managing sessions.

This route allows administrators to manage sessions. Only users with admin privileges can access this route.
Administrators can select a session from a form and change the current session to the selected one.

Returns:
    If the form is submitted successfully, the user is redirected to the "change_session" route with the selected session ID.
    Otherwise, the user is rendered the "admin/manage_sessions.html" template with the current session and the session form.
"""
import re
from datetime import datetime
from werkzeug.datastructures import MultiDict
from flask_sqlalchemy import pagination
from sqlalchemy import func, or_
from urllib.parse import unquote
from sqlalchemy.orm import joinedload
from flask_wtf.csrf import CSRFError, generate_csrf
from flask_login import login_required, current_user
from flask import (
    abort,
    render_template,
    redirect,
    url_for,
    session,
    flash,
    request,
    Response,
    jsonify,
    current_app as app,
)

from . import admin_bp
from ..helpers import (
    get_subjects_by_class_name,
    update_results,
    calculate_results,
    db,
    random,
    string,
    populate_form_with_results,
    generate_excel_broadsheet,
    prepare_broadsheet_data,
    calculate_grade,
    generate_remark,
    group_and_sort_students,
    save_result,
)
from ..models import Student, User, Subject, Result, Session, StudentClassHistory

from ..auth.forms import (
    EditStudentForm,
    ResultForm,
    SubjectForm,
    DeleteForm,
    SessionForm,
    ApproveForm,
    classForm,
    ManageResultsForm,
    StudentRegistrationForm,
)

@admin_bp.errorhandler(CSRFError)
def handle_csrf_error(e):
    app.logger.warning(f"CSRF error: {e.description}")
    flash("The form submission has expired. Please try again.", "alert-danger")
    return redirect(url_for("admins.admin_dashboard"))


@admin_bp.errorhandler(500)
def internal_error(error):
    app.logger.error(f"Server Error: {error}")
    return redirect(url_for("admins.admin_dashboard"))


@admin_bp.errorhandler(404)
def not_found_error(error):
    app.logger.warning(f"404 Error: {error}")
    return redirect(url_for("admins.admin_dashboard"))


@admin_bp.before_request
@login_required
def admin_before_request():
    if not current_user.role == "admin":
        flash("You are not authorized to access this page.", "alert-danger")
        app.logger.warning(
            f"Unauthorized access attempt by user {current_user.username}"
        )
        return redirect(url_for("main.index"))

@admin_bp.after_request
def add_csrf_token(response):
    csrf_token = generate_csrf()
    response.headers['X-CSRFToken'] = csrf_token
    return response

@admin_bp.route("/dashboard")
def admin_dashboard():
    app.logger.info(f"Admin dashboard accessed by user {current_user.username}")
    return render_template("admin/index.html")


@admin_bp.route("/manage_sessions", methods=["GET", "POST"])
@login_required
def manage_sessions():
    if not current_user.is_admin:
        abort(403)  # Restrict access for non-admins

    form = SessionForm()

    # Fetch all sessions and populate choices
    sessions = Session.query.all()
    form.session.choices = [(session.id, session.year) for session in sessions]

    # Include term options
    terms = ["First Term", "Second Term", "Third Term"]
    form.term.choices = [(term, term) for term in terms]
    
    # Retrieve current session and term
    # current_session, current_term = Session.get_current_session_and_term(include_term=True)

    if form.validate_on_submit():
        session_id = form.session.data
        term = form.term.data

        # Update the session and term in the database
        updated_session = Session.set_current_session(session_id, term)
        
        if updated_session:
            flash(
                f"Current session set to {updated_session.year} ({term}).",
                "alert-success",
            )
        else:
            flash("Failed to update the current session and term.", "alert-danger")

        return redirect(url_for("admins.manage_sessions"))
        
    # # Retrieve current session and term
    current_session, current_term = Session.get_current_session_and_term(include_term=True)
    
    return render_template(
        "admin/manage_sessions.html",
        form=form,
        current_session=current_session,
        current_term=current_term,
    )


""""
Student Management Section

This section displays the basic view of the student management page
The page displays the list of students and their details
It also provides links to edit, delete, approve, and deactivate students

    approve_students: Displays the list of students and their approval status
    approve_student: Approves a student
    deactivate_student: Deactivates a student
    regenerate_password: Regenerates the password for a student
    manage_classes: Displays the list of classes
    view_class_by_session: Displays the list of students in a class for a given session
    students_by_class: Displays the list of students in a class
    promote_student: Promotes a student to the next class
    edit_student: Edits the details of a student
    delete_student: Deletes a student
"""

from collections import defaultdict

@admin_bp.route("/register/student", methods=["GET", "POST"])
def add_student():
    form = StudentRegistrationForm()
    current_session = Session.get_current_session()

    try:
        if form.validate_on_submit():
            # Generate a temporary password
            temporary_password = "".join(random.choices(string.ascii_letters + string.digits, k=8))
            
            # Create the Student object
            student = Student(
                first_name=form.first_name.data,
                last_name=form.last_name.data,
                middle_name=form.middle_name.data,
                gender=form.gender.data,
                date_of_birth=form.date_of_birth.data,
                parent_name=form.parent_name.data,
                parent_phone_number=form.parent_phone_number.data,
                address=form.address.data,
                parent_occupation=form.parent_occupation.data,
                state_of_origin=form.state_of_origin.data,
                local_government_area=form.local_government_area.data,
                religion=form.religion.data,
                approved=False,
                password=temporary_password,
            )
            db.session.add(student)
            db.session.flush()  # Ensure the student gets an ID before generating reg_no

            # Generate reg_no and use it as the temporary password
            student.reg_no = f"AAIS/0559/{student.id:03}"
            student.password = student.reg_no  # Use reg_no as the temporary password

            # Create a User object for login credentials
            user = User(username=student.reg_no, role="student")
            user.set_password(student.password)  # Hash the reg_no as the password
            student.user = user  # Link the User to the Student

            db.session.add(user)

            # Check if the class already exists in the `Classes` table
            class_entry = Classes.query.filter_by(name=form.class_name.data).first()
            if not class_entry:
                # If the class does not exist, create it
                class_entry = Classes(name=form.class_name.data)
                db.session.add(class_entry)
                db.session.flush()  # Get the ID for the new class

            # Add entry to `StudentClassHistory` to track the student's class for the current session
            class_history = StudentClassHistory(
                student_id=student.id,
                session_id=current_session.id,  # Link to the current session
                class_name=form.class_name.data,  # Class entered during registration
                class_id=class_entry.id,         # Link to the `Classes` table
            )
            db.session.add(class_history)

            # Commit all changes (student, user, class, and class history)
            db.session.commit()

            # Log success and inform the user
            app.logger.info(f"Student registered successfully: {student.reg_no}")
            flash(
                f"Student registered successfully. Registration Number: {student.reg_no}.",
                "alert alert-success",
            )
            return redirect(url_for("admins.add_student"))

    except Exception as e:
        db.session.rollback()  # Rollback the session on error
        app.logger.error(f"Error registering student: {str(e)}")
        flash("An error occurred. Please try again later.", "alert alert-danger")

    return render_template(
        "admin/students/add_student.html",
        title="Register",
        form=form,
        school_name="Aunty Anne's International School",
    )



@admin_bp.route("/students/<string:action>", methods=["GET", "POST"])
@login_required
def students(action):
    if not current_user.is_admin:
        abort(403)  # Restrict access for non-admins

    # Get current session and term
    current_session, current_term = Session.get_current_session_and_term(include_term=True)
    selected_session_id = Session.query.filter_by(year=current_session).first().id

    # Define pagination parameters
    page = request.args.get('page', 1, type=int)
    per_page = 5  # Adjust as needed for the number of students per page

    # Define the class hierarchy for ordering
    class_hierarchy = [
        "Creche",
        "Pre-Nursery",
        "Nursery 1",
        "Nursery 2",
        "Nursery 3",
        "Basic 1",
        "Basic 2",
        "Basic 3",
        "Basic 4",
        "Basic 5",
        "Basic 6",
        "JSS 1",
        "JSS 2",
        "JSS 3",
        "SSS",
    ]
    # Create a CASE statement to enforce the class hierarchy order
    case_statement = db.case(
        *(
            (StudentClassHistory.class_name == class_name, index)
            for index, class_name in enumerate(class_hierarchy)
        ),
        else_=len(class_hierarchy)  # Classes not in the hierarchy appear last
    )

    # Fetch students with pagination
    paginated_students = (
        db.session.query(Student, StudentClassHistory.class_name)
        .join(
            StudentClassHistory,
            (Student.id == StudentClassHistory.student_id)
            & (StudentClassHistory.session_id == selected_session_id),
        )
        .order_by(case_statement)  # Enforce custom order
        .paginate(page=page, per_page=per_page)
    )

    # Handle AJAX requests for pagination
    if request.args.get('ajax'):
        return render_template(
            "ajax/students/_students_table.html",
            students_classes=group_and_sort_students(paginated_students.items),
            pagination=paginated_students,
            action=action,
        )

    form = DeleteForm()

    return render_template(
        "admin/students/view_students.html",
        students_classes=group_and_sort_students(paginated_students.items),
        pagination=paginated_students,
        current_session=current_session,
        current_term=current_term,
        action=action,
        form=form,
    )


@admin_bp.route("/edit_student/<int:student_id>/<string:action>", methods=["GET", "POST"])
@login_required
def edit_student(student_id, action):
    """
    Edit a student's information.

    Args:
        student_id (int): The ID of the student to be edited.

    Returns:
        A rendered template for editing a student's information.

    Raises:
        403: If the current user is not an admin.

    """
    if not current_user.is_admin:
        abort(403)  # Restrict access for non-admins

    student = Student.query.get_or_404(student_id)
    current_session = Session.get_current_session()

    if not current_session:
        flash("No current session found.", "alert-danger")
        return redirect(url_for("admins.students", action=action))

    session_id = current_session.id
    form = EditStudentForm()

    # Retrieve the latest class history for the student in the current session
    student_class_history = (
        StudentClassHistory.query.filter_by(
            student_id=student.id, session_id=session_id
        )
        .order_by(StudentClassHistory.id.desc())
        .first()
    )

    if form.validate_on_submit():
        # Update student fields
        student.reg_no = form.reg_no.data
        student.first_name = form.first_name.data
        student.last_name = form.last_name.data
        student.middle_name = form.middle_name.data
        student.gender = form.gender.data

        # Update the class history for the current session
        if student_class_history:
            student_class_history.class_name = form.class_name.data
        else:
            # Create a new entry if no history exists for the current session
            new_class_history = StudentClassHistory(
                student_id=student.id,
                session_id=session_id,
                class_name=form.class_name.data,
            )
            db.session.add(new_class_history)

        # Update username in the User model
        user = User.query.filter_by(id=student.user_id).first()
        user.username = form.reg_no.data

        db.session.commit()
        flash("Student updated successfully!", "alert-success")
        return redirect(
            url_for(
                "admins.students",
                session_id=session_id,
                action=action,
            )
        )

    elif request.method == "GET":
        # Pre-fill form with student data
        form.reg_no.data = student.reg_no
        form.first_name.data = student.first_name
        form.last_name.data = student.last_name
        form.middle_name.data = student.middle_name
        form.gender.data = student.gender

        # Pre-fill the class name from StudentClassHistory if available
        if student_class_history:
            form.class_name.data = student_class_history.class_name

    return render_template(
        "admin/students/edit_student.html", form=form, student=student, action=action
    )

@admin_bp.route("/select_class/<string:action>", methods=["GET", "POST"])
@login_required
def select_class(action):
    class_form = classForm()
    
    if class_form.validate_on_submit():
        class_name = class_form.class_name.data.strip()
        
        # Redirect to the students_by_class route with class_name as a URL path segment
        return redirect(
            url_for(
                "admins.students_by_class",
                class_name=class_name,
                action=action
            )
        )
        
    return render_template(
        "admin/classes/select_class.html",
        class_form=class_form,
        action=action
    )

@admin_bp.route("/students_by_class/<string:class_name>/<string:action>", methods=["GET", "POST"])
@login_required
def students_by_class(class_name, action):
    """
    Retrieve students by class for the current session with pagination.

    This function retrieves students in a specific class for the current session and term.
    It also handles the delete functionality.

    Returns:
        render_template: The rendered template with the students, session, class name, and form.
    """
    class_name = unquote(class_name).strip()

    if not class_name:
        flash("Invalid class name provided.", "alert-danger")
        return redirect(url_for("admins.select_class", action=action))

    # Get current session and term directly from the Session model
    current_session, current_term = Session.get_current_session_and_term(include_term=True)

    # Initialize the form for deletion
    form = DeleteForm()

    # Retrieve the session ID based on the current session
    selected_session_id = Session.get_current_session().id

    # Fetch the student history filtered by session and class with pagination
    page = request.args.get('page', 1, type=int)
    per_page = 5  # Number of students per page
    student_histories_pagination = StudentClassHistory.query.filter_by(
        session_id=selected_session_id, class_name=class_name
    ).paginate(page=page, per_page=per_page)

    # Extract students from the pagination object
    student_histories = student_histories_pagination.items
    students = [history.student for history in student_histories]

    if not students and page == 1:
        flash(
            f"No students found in {class_name} for {current_session} - {current_term}",
            "alert-danger",
        )
        return redirect(url_for("admins.select_class", action=action))

    return render_template(
        "admin/classes/students_by_class.html",
        students=students,
        session=current_session,
        current_session=current_session,
        class_name=class_name,
        form=form,
        current_term=current_term,
        action=action,
        pagination=student_histories_pagination,
    )

@admin_bp.route("/promote_student/<string:class_name>/<int:student_id>/<string:action>", methods=["POST"])
@login_required
def promote_student(class_name, student_id, action):
    """
    Promotes a student to the next class in the next academic session.
    Ensures only one class record exists per session.
    """
    if not current_user.is_admin:
        abort(403)  # Restrict access for non-admins

    student = Student.query.get_or_404(student_id)
    current_session = Session.get_current_session()
    class_name = unquote(class_name).strip()

    if not current_session:
        flash("No current session available for promotion.", "alert-danger")
        return redirect(url_for("admins.students_by_class", class_name=class_name, action=action))

    # **Fix: Retrieve the next academic session**
    next_session = Session.query.filter(
        Session.year > current_session.year
    ).order_by(Session.year.asc()).first()

    if not next_session:
        flash("No next academic session available for promotion.", "alert-danger")
        return redirect(url_for("admins.students_by_class", class_name=class_name, action=action))

    # Define the class hierarchy
    class_hierarchy = [
        "Creche",
        "Pre-Nursery",
        "Nursery 1",
        "Nursery 2",
        "Nursery 3",
        "Basic 1",
        "Basic 2",
        "Basic 3",
        "Basic 4",
        "Basic 5",
        "Basic 6",
        "JSS 1",
        "JSS 2",
        "JSS 3",
        "SSS",
    ]

    # Retrieve the latest class
    current_class = class_name

    # Promote the student to the next class if applicable
    if current_class in class_hierarchy:
        current_index = class_hierarchy.index(current_class)
        if current_index + 1 < len(class_hierarchy):
            new_class = class_hierarchy[current_index + 1]
        else:
            flash("This student has completed the highest class.", "alert-warning")
            return redirect(
                url_for(
                    "admins.students_by_class",
                    class_name=current_class,
                    action=action,
                )
            )
    else:
        flash("Current class not found in the hierarchy.", "alert-danger")
        return redirect(
            url_for(
                "admins.students_by_class",
                class_name=current_class,
                action=action,
            )
        )

    # **Fix: Ensure only one record exists for the next session**
    existing_record = StudentClassHistory.query.filter_by(
        student_id=student.id, session_id=next_session.id
    ).first()

    if existing_record:
        db.session.delete(existing_record)

    # Add a new StudentClassHistory record for the next session
    new_class_history = StudentClassHistory(
        student_id=student.id, session_id=next_session.id, class_name=new_class
    )
    db.session.add(new_class_history)
    db.session.commit()

    flash(
        f"{student.first_name} has been promoted to {new_class} in {next_session.year}.",
        "alert-success",
    )
    return redirect(
        url_for(
            "admins.students_by_class",
            class_name=new_class,
            action=action,
        )
    )




@admin_bp.route("/demote_student/<string:class_name>/<int:student_id>/<string:action>", methods=["POST"])
@login_required
def demote_student(class_name, student_id, action):
    """
    Demotes a student to the previous class in the next academic session.
    Ensures only one class record exists per session.
    """
    if not current_user.is_admin:
        abort(403)  # Restrict access for non-admins

    student = Student.query.get_or_404(student_id)
    current_session = Session.get_current_session()
    class_name = unquote(class_name).strip()
    
    if not current_session:
        flash("No current session available for demotion.", "alert-danger")
        return redirect(url_for("admins.students_by_class", class_name=class_name, action=action))

    # **Fix: Retrieve the next academic session**
    next_session = Session.query.filter(
        Session.year > current_session.year
    ).order_by(Session.year.asc()).first()

    if not next_session:
        flash("No next academic session available for demotion.", "alert-danger")
        return redirect(url_for("admins.students_by_class", class_name=class_name, action=action))

    # Define the class hierarchy
    class_hierarchy = [
        "Creche",
        "Pre-Nursery",
        "Nursery 1",
        "Nursery 2",
        "Nursery 3",
        "Basic 1",
        "Basic 2",
        "Basic 3",
        "Basic 4",
        "Basic 5",
        "Basic 6",
        "JSS 1",
        "JSS 2",
        "JSS 3",
        "SSS",
    ]

    # # Retrieve the latest class
    current_class = class_name

    # Demote the student to the previous class if applicable
    if current_class in class_hierarchy:
        current_index = class_hierarchy.index(current_class)
        if current_index > 0:
            new_class = class_hierarchy[current_index - 1]
        else:
            flash("This student is already in the lowest class.", "alert-warning")
            return redirect(
                url_for(
                    "admins.students_by_class",
                    class_name=current_class,
                    action=action,
                )
            )
    else:
        flash("Current class not found in the hierarchy.", "alert-danger")
        return redirect(
            url_for(
                "admins.students_by_class",
                class_name=current_class,
                action=action,
            )
        )

    # **Fix: Ensure only one record exists for the next session**
    existing_record = StudentClassHistory.query.filter_by(
        student_id=student.id, session_id=next_session.id
    ).first()

    if existing_record:
        db.session.delete(existing_record)

    # Add a new StudentClassHistory record for the next session
    new_class_history = StudentClassHistory(
        student_id=student.id, session_id=next_session.id, class_name=new_class
    )
    db.session.add(new_class_history)
    db.session.commit()

    flash(
        f"{student.first_name} has been demoted to {new_class} in {next_session.year}.",
        "alert-success",
    )
    return redirect(
        url_for(
            "admins.students_by_class",
            class_name=new_class,
            action=action,
        )
    )

@admin_bp.route("/delete_student_record/<string:class_name>/<int:student_id>/<string:action>", methods=["POST"])
@login_required
def delete_student_record(class_name, student_id, action):
    """
    Deletes a student's class record for the current session.

    Args:
        student_id (int): The ID of the student whose record is to be deleted.

    Returns:
        redirect: Redirects to the manage students page or class-specific view.

    Raises:
        403: If the current user is not an admin.
        404: If the student or current session record is not found.
    """
    if not current_user.is_admin:
        abort(403)  # Restrict access for non-admins

    # Check if the student exists
    student = Student.query.get_or_404(student_id)
    
    class_name = unquote(class_name).strip()

    # Get the current session dynamically
    current_session = Session.get_current_session()
    if not current_session:
        flash("No current session available.", "alert-danger")
        return redirect(url_for("admins.students_by_class", class_name=class_name, action=action))

    # Find the student's class record for the current session
    class_record = StudentClassHistory.query.filter_by(
        student_id=student.id, session_id=current_session.id
    ).first()

    if not class_record:
        flash(
            f"No class record found for {student.first_name} in the current session.",
            "alert-danger",
        )
        return redirect(url_for("admins.students_by_class", class_name=class_name, action=action))

    # Delete the class record
    db.session.delete(class_record)
    db.session.commit()

    flash(
        f"Class record for {student.first_name} in session {current_session.year} has been deleted.",
        "alert-success",
    )
    return redirect(url_for("admins.students_by_class", class_name=class_name, action=action))

@admin_bp.route("/delete_student/<int:student_id>/<string:action>", methods=["POST"])
@login_required
def delete_student(student_id, action):
    """
    Delete a student and associated records.

    Args:
        student_id (int): The ID of the student to be deleted.

    Returns:
        redirect: Redirects to the student's class page.

    Raises:
        403: If the current user is not an admin.
        404: If the student with the given ID is not found.

    """
    if not current_user.is_admin:
        abort(403)  # Restrict access for non-admins

    form = DeleteForm()
    session_id = Session.query.filter_by(is_current=True).first().id

    if form.validate_on_submit():
        student = Student.query.get_or_404(student_id)
        student_class_history = (
            StudentClassHistory.query.filter_by(student_id=student.id)
            .order_by(StudentClassHistory.id.desc())
            .first()
        )

        try:
            # Delete all related results
            results = Result.query.filter_by(student_id=student.id).all()
            for result in results:
                db.session.delete(result)

            # Delete the associated User account
            user = User.query.get(student.user_id)
            if user:
                db.session.delete(user)

            # Delete all class history for the student
            class_history_records = StudentClassHistory.query.filter_by(
                student_id=student.id
            ).all()
            for history in class_history_records:
                db.session.delete(history)

            # Delete the student record itself
            db.session.delete(student)
            db.session.commit()

            flash(
                "Student and associated records deleted successfully!",
                "alert-success",
            )
        except Exception as e:
            db.session.rollback()
            flash(f"Error deleting student: {e}", "alert-danger")

    # Redirect to the student's class page, using the latest class from history or default if not found
    class_name = (
        student_class_history.class_name if student_class_history else "Unassigned"
    )
    return redirect(
        url_for(
            "admins.students", session_id=session_id, action=action
        )
    )


""""
Subject Management Section

This section displays the basic view of the subject management page
The page displays the list of subjects and their details
It also provides links to edit, delete, and add subjects

    manage_subjects: Displays the list of subjects and their details
    edit_subject: Edits the details of a subject
    delete_subject: Deletes a subject

"""


@admin_bp.route("/manage_subjects", methods=["GET", "POST"])
@login_required
def manage_subjects():
    """
    Route handler for managing subjects in the admin panel.
    Allows adding and deactivating subjects for the current session.
    """
    if not current_user.is_admin:
        abort(403)

    form = SubjectForm()
    if form.validate_on_submit():
        subjects_input = form.name.data
        subject_names = [name.strip() for name in subjects_input.split(",")]

        for subject_name in subject_names:
            for section in form.section.data:
                existing_subject = Subject.query.filter_by(
                    name=subject_name, section=section, deactivated=False
                ).first()
                if existing_subject is None:
                    subject = Subject(name=subject_name, section=section)
                    db.session.add(subject)
        db.session.commit()
        flash("Subject(s) added successfully!", "alert-success")
        return redirect(url_for("admins.manage_subjects"))

    # Check for deactivation request
    if request.method == "POST" and "deactivate_subject_id" in request.form:
        subject_id = int(request.form.get("deactivate_subject_id"))
        subject = Subject.query.get(subject_id)
        if subject:
            subject.deactivated = True
            db.session.commit()
            flash(
                f"Subject '{subject.name}' has been deactivated.", "alert-warning"
            )
        return redirect(url_for("admins.manage_subjects"))

    # Display only active subjects for the current session
    subjects = (
        Subject.query.order_by(Subject.section).all()
    )
    subjects_by_section = {}
    for subject in subjects:
        if subject.section not in subjects_by_section:
            subjects_by_section[subject.section] = []
        subjects_by_section[subject.section].append(subject)

    delete_form = DeleteForm()
    return render_template(
        "admin/subjects/subject_admin.html",
        form=form,
        subjects_by_section=subjects_by_section,
        delete_form=delete_form,
    )


@admin_bp.route("/edit_subject/<int:subject_id>", methods=["GET", "POST"])
@login_required
def edit_subject(subject_id):
    """
    Edit a subject with the given subject_id.

    Args:
        subject_id (int): The ID of the subject to be edited.

    Returns:
        A response object or a template with the form and subject data.

    Raises:
        404: If the subject with the given subject_id does not exist.
    """
    subject = Subject.query.get_or_404(subject_id)
    form = SubjectForm(obj=subject)
    if form.validate_on_submit():
        subject.name = form.name.data
        subject.section = form.section.data
        db.session.commit()

        # Update all related results with the new subject name and section
        results = Result.query.filter_by(subject_id=subject.id).all()
        for result in results:
            result.subject_name = subject.name
            result.subject_section = subject.section
        db.session.commit()

        flash("Subject updated successfully!", "alert-success")
        return redirect(url_for("admins.manage_subjects"))

    return render_template(
        "admin/subjects/edit_subject.html", form=form, subject=subject
    )


@admin_bp.route("/delete_subject/<int:subject_id>", methods=["POST"])
def delete_subject(subject_id):
    """
    Delete a subject and its associated scores.

    Args:
        subject_id (int): The ID of the subject to be deleted.

    Returns:
        redirect: Redirects to the manage_subjects route.

    Raises:
        None

    """
    if not current_user.is_admin:
        abort(403)

    form = DeleteForm()  # Instantiate the DeleteForm

    if form.validate_on_submit():
        try:
            # Find the subject
            subject = Subject.query.get_or_404(subject_id)

            # Delete all scores associated with the subject
            Result.query.filter_by(subject_id=subject_id).delete()

            # Delete the subject
            db.session.delete(subject)
            db.session.commit()

            flash(
                "Subject and associated scores deleted successfully!",
                "alert-success",
            )
        except Exception as e:
            db.session.rollback()
            flash(f"Error deleting subject: {e}", "alert-danger")

    return redirect(url_for("admins.manage_subjects"))


""""
Result Management Section

Manages results by classes and sessions
This section allows the admin to view, edit, and delete results
It also provides the ability to generate broadsheets and download results

    select_term_session: Select the term and session for result management
    manage_results: Manage the results for a student
    broadsheet: Generate a broadsheet for a class
    download_broadsheet: Download a broadsheet for a class
    update_results: Update the results for a student
    calculate_results: Calculate the results for a student
    get_subjects_by_class_name: Get the subjects for a class
"""
@admin_bp.route("/manage_results/<string:class_name>/<int:student_id>/<string:action>", methods=["GET"])
@login_required
def manage_results(class_name, student_id, action):
    student = Student.query.get_or_404(student_id)
    class_name = unquote(class_name).strip()

    # Get the current session and term directly from the Session model
    current_session, current_term = Session.get_current_session_and_term(include_term=True)

    # Get subjects based on class and session (with deactivated subjects based on session year)
    subjects = get_subjects_by_class_name(
        class_name, include_deactivated=(current_session == "2023/2024")
    )

    # Initialize the forms
    result_form = ResultForm(term=current_term, session=current_session)
    form = ManageResultsForm()

    # Fetch results in a single query and map by subject_id
    results = Result.query.filter_by(
        student_id=student.id, term=current_term, session=current_session
    ).all()
    results_dict = {result.subject_id: result for result in results}

    # Populate form with existing results for GET requests
    populate_form_with_results(form, subjects, results_dict)

    # Default details for the results page
    details = {
        "grand_total": None,
        "last_term_average": None,
        "average": None,
        "cumulative_average": None,
        "subjects_offered": None,
        "next_term_begins": None,
        "position": None,
        "date_issued": None,
        "principal_remark": None,
        "teacher_remark": None,
    }

    if results:
        first_result = results[0]
        details.update({
            "grand_total": first_result.grand_total,
            "last_term_average": first_result.last_term_average,
            "average": first_result.term_average,
            "cumulative_average": first_result.cumulative_average,
            "subjects_offered": first_result.subjects_offered,
            "next_term_begins": first_result.next_term_begins,
            "position": first_result.position,
            "date_issued": first_result.date_issued,
            "principal_remark": first_result.principal_remark,
            "teacher_remark": first_result.teacher_remark,
        })

    # Render the template
    return render_template(
        "admin/results/manage_results.html",
        student=student,
        subjects=subjects,
        form=form,
        term=current_term,
        subject_results=zip(subjects, form.subjects),
        class_name=class_name,
        session=current_session,
        results_dict=results_dict,
        results=results,
        action=action,
        school_name="Aunty Anne's International School",
        **details,  # Unpack the details dictionary
    )


@admin_bp.route("/update_result/<string:class_name>/<int:student_id>/<string:action>", methods=["POST"])
@login_required
def update_result(class_name, student_id, action):
    student = Student.query.get_or_404(student_id)
    class_name = unquote(class_name).strip()

    # Get the current session and term directly from the Session model
    current_session, current_term = Session.get_current_session_and_term(include_term=True)

    # Initialize the forms
    result_form = ResultForm(term=current_term, session=current_session)
    form = ManageResultsForm()

    # Fetch results in a single query and map by subject_id
    results = Result.query.filter_by(
        student_id=student.id, term=current_term, session=current_session
    ).all()
    results_dict = {result.subject_id: result for result in results}

    # Handle form submission
    if form.validate_on_submit():
        try:
            # Update results in the database
            update_results(student, current_term, current_session, form, result_form)
            flash("Results updated successfully.", "alert-success")
            app.logger.info(
                f"Results for student {student.id} updated for term {current_term}, session {current_session}."
            )
            return redirect(
                url_for(
                    "admins.manage_results",
                    class_name=class_name,
                    student_id=student.id,
                    action=action,
                )
            )
        except Exception as e:
            # Log errors and flash a failure message
            app.logger.error(
                f"Error updating results for student {student.id}: {str(e)}"
            )
            flash(
                "An error occurred while updating the results. Please try again.",
                "alert-danger",
            )

    # Log validation errors if POST request fails
    elif request.method == "POST":
        app.logger.error(f"Form validation failed: {form.errors}")

    # Render the template if POST fails
    return redirect(
        url_for(
            "admins.manage_results",
            class_name=class_name,
            student_id=student.id,
            action=action,
        )
    )


@admin_bp.route("/broadsheet/<string:class_name>/<string:action>", methods=["GET", "POST"])
@login_required
def broadsheet(class_name, action):
    try:
        # Decode and normalize class_name
        class_name = unquote(class_name).strip()
        form = classForm()
        
        # Get the current session and term directly from the Session model
        current_session, current_term = Session.get_current_session_and_term(include_term=True)

        if not current_session or not current_term:
            flash("Current session or term is not set", "alert-danger")
            return redirect(url_for("admins.students_by_class", class_name=class_name, action=action))

        # Fetch the selected session object
        selected_session = Session.get_current_session()

        if not selected_session:
            flash(f"Session '{current_session}' not found.", "alert-danger")
            return redirect(url_for("admins.students_by_class", class_name=class_name, action=action))

        # Fetch students and subjects based on class_name from StudentClassHistory
        student_class_histories = (
            StudentClassHistory.query.filter_by(
                class_name=class_name, session_id=selected_session.id
            )
            .options(joinedload(StudentClassHistory.student))
            .all()
        )

        students = [history.student for history in student_class_histories]
        subjects = get_subjects_by_class_name(class_name=class_name)

        if not students or not subjects:
            flash(
                f"No students or subjects found for class {class_name}.",
                "alert-info",
            )
            return render_template(
                "admin/results/broadsheet.html",
                students=[],
                subjects=[],
                broadsheet_data=[],
                subject_averages={},
                class_name=class_name,
                action=action,
            )

        broadsheet_data = []
        subject_averages = {
            subject.id: {"total": 0, "count": 0} for subject in subjects
        }

        # Iterate through students and their results
        for student in students:
            student_results = {
                "student": student,
                "results": {
                    subject.id: {
                        "class_assessment": "",
                        "summative_test": "",
                        "exam": "",
                        "total": "",
                        "grade": "",
                        "remark": "",
                    }
                    for subject in subjects
                },
                "grand_total": "",
                "average": "",
                "cumulative_average": "",
                "position": "",
            }

            # Fetch student results for the specific term and session
            results = Result.query.filter_by(
                student_id=student.id, term=current_term, session=current_session
            ).all()

            # Process each result for the student
            for result in results:
                if result.subject_id in student_results["results"]:
                    student_results["results"][result.subject_id] = {
                        "class_assessment": result.class_assessment or "",
                        "summative_test": result.summative_test or "",
                        "exam": result.exam or "",
                        "total": result.total or "",
                        "grade": result.grade or "",
                        "remark": result.remark or "",
                    }
                    
                    if result.total is not None and result.total > 0:
                        subject_averages[result.subject_id]["total"] += result.total
                        subject_averages[result.subject_id]["count"] += 1
            
                # Safely set grand total, average, and position
                student_results["grand_total"] = (
                    int(result.grand_total) if result.grand_total not in ("", None) else ""
                )
                student_results["cumulative_average"] = (
                    float(result.cumulative_average) if result.cumulative_average not in ("", None) else ""
                )
                student_results["average"] = (
                    float(result.term_average) if result.term_average not in ("", None) else ""
                )
                student_results["position"] = result.position if result.position not in ("", None) else ""

            # Add student results to the broadsheet data
            broadsheet_data.append(student_results)

        # Calculate class averages for each subject
        for subject_id, values in subject_averages.items():
            values["average"] = (
                round(values["total"] / values["count"], 1)
                if values["count"]
                else ""
            )

        # Safely sort students by their average
        broadsheet_data.sort(
            key=lambda x: float(x["average"]) if isinstance(x["average"], (int, float, str)) and x["average"] not in ("", None) else 0,
            reverse=True
        )

        return render_template(
            "admin/results/broadsheet.html",
            students=students,
            subjects=subjects,
            broadsheet_data=broadsheet_data,
            subject_averages=subject_averages,
            class_name=class_name,
            current_session=current_session,
            current_term=current_term,
            form=form,
            action=action,
        )

    except Exception as e:
        app.logger.error(
            f"Error generating broadsheet for class_name: {class_name} - {str(e)}"
        )
        flash("An error occurred. Please try again later.", "alert-danger")
        return redirect(url_for("admins.students_by_class", class_name=class_name, action=action))
        
        
@admin_bp.route("/update-broadsheet/<string:class_name>/<string:action>", methods=["POST"])
@login_required
def update_broadsheet(class_name, action):
    class_name = unquote(class_name).strip()
    
    # Get the current session and term
    current_session, current_term = Session.get_current_session_and_term(include_term=True)
    
    # Fetch all students and subjects for the given class
    student_class_histories = (
        StudentClassHistory.query.filter_by(
            class_name=class_name, session_id=current_session
        )
        .options(joinedload(StudentClassHistory.student))
        .all()
    )

    students = [history.student for history in student_class_histories]
    subjects = get_subjects_by_class_name(class_name=class_name)

    # Parse the form data
    results_data = {}
    for key, value in request.form.items():
        if key.startswith('results['):
            match = re.match(r'results\[(\d+)\]\[(\d+)\]\[(\w+)\]', key)
            if match:
                student_id, subject_id, field = match.groups()
                student_id, subject_id = int(student_id), int(subject_id)
                
                value = None if not value else int(value)
                results_data.setdefault(student_id, {}).setdefault(subject_id, {})[field] = value

    if not results_data:
        app.logger.error("No results data provided.")
        flash("No data was submitted for update.", "alert-danger")
        return redirect(url_for('admins.broadsheet', class_name=class_name, action=action))
    
    try:
        for student_id, subject_scores in results_data.items():
            for subject_id, scores in subject_scores.items():
                
                # Use None as the default for missing values
                class_assessment = scores.get('class_assessment')
                summative_test = scores.get('summative_test')
                exam = scores.get('exam')
                
                # Calculate total only if all components have values
                if class_assessment is not None and summative_test is not None and exam is not None:
                    total = class_assessment + summative_test + exam
                else:
                    total = None


                # Check if a result already exists
                result = Result.query.filter_by(
                    student_id=student_id,
                    subject_id=subject_id,
                    term=current_term,
                    session=current_session,
                ).first()

                if result:
                    result.class_assessment = class_assessment
                    result.summative_test = summative_test
                    result.exam = exam
                    result.total = total
                else:
                    result = Result(
                        student_id=student_id,
                        subject_id=subject_id,
                        term=current_term,
                        session=current_session,
                        class_assessment=class_assessment,
                        summative_test=summative_test,
                        exam=exam,
                        total=total,
                    )
                    db.session.add(result)

            # Call calculate_results for the student
            # student = Student.query.get(student_id)
            (
                grand_total, 
                average, 
                cumulative_average, 
                last_term_average, 
                subjects_offered
            ) = calculate_results(student_id, current_term, current_session)

            # Update aggregated fields for the student's results
            Result.query.filter_by(
                student_id=student_id,
                term=current_term,
                session=current_session,
            ).update({
                Result.grand_total: grand_total,
                Result.term_average: average,
                Result.cumulative_average: cumulative_average,
                Result.subjects_offered: subjects_offered
            })

        db.session.commit()
        flash("Broadsheet updated successfully.", "alert-success")
        app.logger.info(
            f"Broadsheet updated for class {class_name}, term {current_term}, session {current_session}."
        )

    except Exception as e:
        db.session.rollback()
        app.logger.error(f"Error updating broadsheet: {str(e)}")
        flash("An error occurred while updating the broadsheet.", "alert-danger")
    
    return redirect(url_for('admins.broadsheet', class_name=class_name, action=action))

@admin_bp.route("/download_broadsheet/<string:class_name>/<string:action>", methods=["GET"])
def download_broadsheet(class_name, action):
    try:
        # Decode and normalize class_name
        session_year, term = Session.get_current_session_and_term(include_term=True)
        class_name = unquote(class_name).strip()

        if not term or not session_year:
            flash("Term and session must be provided to download broadsheet.", "alert-danger")
            return redirect(url_for("admins.broadsheet", class_name=class_name, action=action))

        # Validate session
        session = Session.query.filter_by(year=session_year).first()
        if not session:
            flash(f"Session '{session_year}' not found.", "alert-danger")
            return redirect(url_for("admins.admin_dashboard"))

        # Retrieve student and subject data
        student_class_histories = (
            StudentClassHistory.query.filter_by(class_name=class_name, session_id=session.id)
            .options(joinedload(StudentClassHistory.student))
            .all()
        )
        students = [history.student for history in student_class_histories]
        subjects = get_subjects_by_class_name(class_name=class_name)

        if not students or not subjects:
            flash(f"No students or subjects found for class {class_name}.", "alert-info")
            return redirect(url_for("admins.broadsheet", class_name=class_name, action=action))

        # Prepare broadsheet data
        broadsheet_data, subject_averages = prepare_broadsheet_data(students, subjects, term, session_year)

        # Generate Excel file
        output = generate_excel_broadsheet(class_name, term, session_year, broadsheet_data, subjects, subject_averages)

        # Return the Excel file as a downloadable response
        filename = f"Broadsheet_{class_name}_{term}_{session_year}.xlsx"
        return Response(
            output,
            mimetype="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            headers={"Content-Disposition": f"attachment;filename={filename}"}
        )

    except Exception as e:
        app.logger.error(f"Error generating broadsheet for class_name: {class_name} - {str(e)}")
        flash("An error occurred while downloading the broadsheet. Please try again later.", "alert-danger")
        return redirect(url_for("admins.broadsheet", class_name=class_name, action=action))

# @admin_bp.route("/add_class", methods=["GET", "POST"])
# def add_class():
#     form = classForm()
#     if form.validate_on_submit():
#         name = form.name.data
#         section = form.section.data
        
#         existing_class = Classes.query.filter_by(name=name).first()
        
#         if existing_class is None:
#             new_class = Classes.create_class(name=name, section=section)
#             flash("{new_class} class created successfully!", "alert-success")
    
#     classes = Classes.query.all()
#     return render_template(
#         "admin/classes/manage_classes.html",
#         form=form,
#         classes=classes
#     )
    
# @admin_bp.route("/manage_classes", methods=["GET", "POST"])
# def manage_classes():
#     form = ClassForm()
#     if form.validate_on_submit():
#         name = form.name.data
#         section = form.section.data

#         # Create a new class
#         if "create" in request.form:
#             try:
#                 Classes.create_class(name=name, section=section)
#                 flash("Class created successfully!", "alert-success")
#             except Exception as e:
#                 db.session.rollback()
#                 flash(f"Error creating class {str(e)}", "alert-danger")

#         # Edit an existing class
#         elif "edit" in request.form:
#             class_id = form.class_id.data  # Hidden field for identifying the class to edit
#             cls = Classes.query.get(class_id)
#             if cls:
#                 try:
#                     cls.edit_class(name=name, section=section)
#                     flash("Class updated successfully!", "alert-success")
#                 except Exception as e:
#                     db.session.rollback()
#                     flash(f"Error updating class: {str(e)}", "alert-danger")
#             else:
#                 flash("Class not found!", "alert-warning")

#     # Fetch all classes to display
#     classes = Classes.query.all()
#     return render_template("admin/manage_classes.html", form=form, classes=classes)

# @admin_bp.route("/delete-class/<int:class_id>", methods=["POST"])
# def delete_class(class_id):
#     cls = Classes.query.get(class_id)
#     if cls:
#         try:
#             cls.delete_class()
#             flash("Class deleted successfully!", "alert-success")
#         except Exception as e:
#             db.session.rollback()
#             flash(f"Error deleting class: {str(e)}", "alert-danger")
#     else:
#         flash("Class not found!", "alert-warning")

#     return redirect(url_for("admin.manage_classes")) 
        


# AJAX ROUTES ===========================================================================================================================
@admin_bp.route("/print_student_message", methods=["GET", "POST"])
@login_required
def print_student_message():
    if not current_user.is_admin:
        abort(403)
        
    form = ApproveForm()
        
    # Fetch all students from the database
    students = Student.query.all()
    selected_student = None

    if request.method == "POST":
        student_id = request.form.get("student_id")
        selected_student = Student.query.get(student_id)

    return render_template(
        "admin/students/print_student_message.html",
        students=students,
        selected_student=selected_student,
        form=form,
    )

@admin_bp.route('/get-student/<int:student_id>', methods=['GET'])
def get_student(student_id):
    student = Student.query.get(student_id)
    if student:
        return jsonify({
            "first_name": student.first_name,
            "last_name": student.last_name,
            "reg_no": student.reg_no
        })
    return jsonify({"error": "Student not found"}), 404

@admin_bp.route('/stats', methods=['GET'])
@login_required
def get_stats():
    """Route to fetch updated stats based on current session and term."""
    
    # Get the current session and term
    current_session, current_term = Session.get_current_session_and_term(include_term=True)

    if not current_session or not current_term:
        return jsonify({"error": "Current session or term is not set."}), 400

    # Query to count students based on the session and term
    base_query = StudentClassHistory.query.join(StudentClassHistory.session).filter(
        Session.year == current_session
    ).join(Student)

    # Total students in the current session and term
    total_students = base_query.count()

    # Approved students in the current session and term
    approved_students = base_query.filter(Student.approved == True).count()

    # Students who have paid fees in the current session and term
    fees_paid = base_query.filter(Student.has_paid_fee == True).count()

    # Students who have not paid fees in the current session and term
    fees_not_paid = base_query.filter(Student.has_paid_fee == False).count()
    
    stats = {
        "total_students": total_students,
        "approved_students": approved_students,
        "fees_paid": fees_paid,
        "fees_not_paid": fees_not_paid
    }

    # Return the stats
    return jsonify(stats)

@admin_bp.route('/toggle_fee_status/<int:student_id>', methods=['POST'])
@login_required
def toggle_fee_status(student_id):
    """Route to toggle fee status of a student."""
    
    if not current_user.is_admin:
        return jsonify({"success": False, "error": "Unauthorized access"}), 403

    student = Student.query.get_or_404(student_id)
    student.has_paid_fee = not student.has_paid_fee
    db.session.commit()
    
    status = "paid" if student.has_paid_fee else "unpaid"
    message = f"Fee status for {student.first_name} {student.last_name} has been marked as {status}."
    return jsonify({
        "success": True,
        "message": message,
        "has_paid_fee": student.has_paid_fee
    })


@admin_bp.route('/toggle_approval_status/<int:student_id>', methods=['POST'])
@login_required
def toggle_approval_status(student_id):
    """Route to toggle approval status of a student."""
    if not current_user.is_admin:
        return jsonify({"success": False, "error": "Unauthorized access"}), 403
    
    student = Student.query.get_or_404(student_id)
    student.approved = not student.approved
    
    db.session.commit()
    
    message = f"{student.first_name} {student.last_name} has been {'approved' if student.approved == True else 'deactivated'} successfully"
    return jsonify({
        "success": True,
        "message": message,
        "approved": student.approved,
    })


@admin_bp.route('/student_stats/<string:class_name>', methods=['GET'])
@login_required
def get_student_stats_by_class(class_name):
    """Route to fetch stats based on the current session, term, and class."""
    # Get the current session and term
    current_session, current_term = Session.get_current_session_and_term(include_term=True)
    class_name = unquote(class_name).strip()

    if not current_session or not current_term:
        return jsonify({"error": "Current session or term is not set."}), 400

    # Base query with session and term filters
    base_query = StudentClassHistory.query.join(StudentClassHistory.session).filter(
        Session.year == current_session,
    ).join(Student).filter(StudentClassHistory.class_name == class_name)

    # Total students in the class, session, and term
    total_students = base_query.count()

    # Approved students in the class, session, and term
    approved_students = base_query.filter(Student.approved == True).count()

    # Students who have paid fees in the class, session, and term
    fees_paid = base_query.filter(Student.has_paid_fee == True).count()

    # Students who have not paid fees in the class, session, and term
    fees_not_paid = base_query.filter(Student.has_paid_fee == False).count()

    stats = {
        "class_name": class_name,
        "total_students": total_students,
        "approved_students": approved_students,
        "fees_paid": fees_paid,
        "fees_not_paid": fees_not_paid
    }

    # Return the stats
    return jsonify(stats)


@admin_bp.route('/search_students/<string:action>', methods=['GET'])
@login_required
def search_students(action):
    search_query = request.args.get('query', '', type=str).strip()
    ajax_request = request.args.get('ajax', False, type=bool)
    page = request.args.get('page', 1, type=int)

    # Get the current session
    current_session, current_term = Session.get_current_session_and_term(include_term=True)
    form = DeleteForm()
    selected_session_id = Session.get_current_session().id
    
    if not current_session:
        app.logger.error('No active session found.')
        return jsonify({'error': 'No active session found.'}), 400

    # Query students and their class history for the current session
    students_query = (
        db.session.query(Student, StudentClassHistory.class_name)
        .join(StudentClassHistory, Student.id == StudentClassHistory.student_id)
        .filter(StudentClassHistory.session_id == selected_session_id)
    )
    
    # Apply search filters
    if search_query:
        search_filter = or_(
            func.lower(Student.first_name).contains(search_query.lower()),
            func.lower(Student.last_name).contains(search_query.lower()),
            func.lower(Student.reg_no).contains(search_query.lower()),
            func.lower(StudentClassHistory.class_name).contains(search_query.lower()),
        )
        students_query = students_query.filter(search_filter)
        
        app.logger.info(f"Query count: {students_query.count()}")

    # Paginate the results
    pagination = students_query.paginate(page=page, per_page=5, error_out=False)
    
    app.logger.info(f"Pagination total: {pagination.total}, Pages: {pagination.pages}")

    students_classes = group_and_sort_students(pagination.items)
    
    # Handle AJAX requests for pagination
    # if ajax_request:
    return render_template(
        "ajax/students/pagination.html",
        students_classes=students_classes,
        current_session=current_session,
        current_term=current_term,
        pagination=pagination,
        action=action,
        form=form,
        search_query=search_query,
    )

    # return render_template(
    #     "admin/students/view_students.html",
    #     students_classes=students_classes,
    #     pagination=pagination,
    #     current_session=current_session,
    #     current_term=current_term,
    #     action=action,
    #     form=form,
    #     search_query=search_query,
    # )

@admin_bp.route('/search_students_by_class/<string:class_name>/<string:action>', methods=['GET'])
@login_required
def search_students_by_class(class_name, action):
    search_query = request.args.get('query', '', type=str).strip()
    ajax_request = request.args.get('ajax', False, type=bool)
    
    class_name = unquote(class_name).strip()
    page = request.args.get('page', 1, type=int)

    current_session, current_term = Session.get_current_session_and_term(include_term=True)
    form = DeleteForm()

    selected_session_id = Session.get_current_session().id

    # Query for all students in the specified class
    students_query = StudentClassHistory.query.join(StudentClassHistory.session).filter(
        Session.year == current_session,
    ).join(Student).filter(StudentClassHistory.class_name == class_name)

    # Apply search filter if query is not empty
    if search_query:
        search_filter = or_(
            func.lower(Student.first_name).contains(search_query.lower()),
            func.lower(Student.last_name).contains(search_query.lower()),
            func.lower(Student.reg_no).contains(search_query.lower()),
        )
        students_query = students_query.filter(search_filter)

    # Paginate results
    pagination = students_query.paginate(page=page, per_page=5, error_out=False)
    student_histories = pagination.items
    students = [history.student for history in student_histories]

    # Handle AJAX requests
    # if ajax_request:
    return render_template(
        "ajax/classes/students_by_class.html",
        students=students,
        session=current_session,
        current_session=current_session,
        form=form,
        current_term=current_term,
        pagination=pagination,
        action=action,
        class_name=class_name,
    )
    
    # # Fallback: Redirect to main route if not AJAX
    # return render_template(
    #     "admin/classes/students_by_class.html",
    #     students=students,
    #     session=current_session,
    #     current_session=current_session,
    #     class_name=class_name,
    #     form=form,
    #     current_term=current_term,
    #     action=action,
    #     pagination=pagination,
    # )
    
@admin_bp.route("/update_result_field", methods=["POST"])
@login_required
def update_result_field():
    try:
        data = request.get_json()
        current_session, current_term = Session.get_current_session_and_term(include_term=True)
        
        subject_id = data.get("subject_id")
        student_id = data.get("student_id")
        
        if not subject_id or not student_id:
            return jsonify({"status": "error", "message": "Invalid subject or student ID."}), 400

        class_assessment = data.get("class_assessment")
        summative_test = data.get("summative_test")
        exam = data.get("exam")
        next_term_begins = data.get("next_term_begins")
        position = data.get("position")
        date_issued = data.get("date_issued")
        
        for key, value in data.items():
            app.logger.info(f"{key}: {value}")
        
        app.logger.info(f"Data:=>> {data}")
        
        save_result(student_id, subject_id, current_term, current_session, data)
        
        return jsonify({"status": "success", "message": "Result saved successfully."})
    except Exception as e:
        app.logger.error(f"Error saving result via AJAX: {str(e)}")
        return jsonify({"status": "error", "message": "Failed to save result."}), 400



