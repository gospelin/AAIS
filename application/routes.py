from . import app, db
from flask import render_template, redirect, url_for, flash, request
from flask_login import login_required, login_user, logout_user, current_user
from .models import Student, User, Subject, Score
from .forms import StudentRegistrationForm, LoginForm, ScoreForm, EditStudentForm
import random, string

def generate_unique_username(first_name, last_name):
    username = f"{first_name.lower()}.{last_name.lower()}"
    existing_user = Student.query.filter_by(username=username).first()
    if existing_user:
        random_suffix = "".join(random.choices(string.ascii_lowercase + string.digits, k=4))
        username = f"{username}{random_suffix}"
    return username

@app.route('/')
@app.route('/index')
@app.route('/home')
def index():
    return render_template('index.html', title="Home", school_name="Aunty Anne's Int'l School")

@app.route('/about_us')
def about_us():
    return render_template('about_us.html', title="About Us", school_name="Aunty Anne's Int'l School")

@app.route("/register/student", methods=["GET", "POST"])
def student_registration():
    form = StudentRegistrationForm()
    if form.validate_on_submit():
        username = generate_unique_username(form.first_name.data, form.last_name.data)
        temporary_password = "".join(random.choices(string.ascii_letters + string.digits, k=8))
        student = Student(
            first_name=form.first_name.data,
            last_name=form.last_name.data,
            email=form.email.data,
            gender=form.gender.data,
            date_of_birth=form.date_of_birth.data,
            parent_phone_number=form.parent_phone_number.data,
            address=form.address.data,
            parent_occupation=form.parent_occupation.data,
            entry_class=form.entry_class.data,
            previous_class=form.previous_class.data,
            state_of_origin=form.state_of_origin.data,
            local_government_area=form.local_government_area.data,
            religion=form.religion.data,
            username=username,
            password = temporary_password
        )
        user = User(username=student.username, is_admin=False)
        user.set_password(student.password)
        student.user = user
        try:
            db.session.add(student)
            db.session.add(user)
            db.session.commit()
            flash(
                f"Student registered successfully. Username: {username}, Password: {temporary_password}",
                "alert alert-success",
            )
        except Exception as e:
            db.session.rollback()
            flash("An error occurred. Please try again later.", "alert alert-danger")
        return redirect(url_for("index"))
    return render_template(
        "student_registration.html",
        title="Register",
        form=form,
        school_name="Aunty Anne's Int'l School",
    )

@app.route("/login", methods=["GET", "POST"])
def login():
    form = LoginForm()
    if form.validate_on_submit():
        user = User.query.filter_by(username=form.username.data).first()
        if user and user.check_password(form.password.data):
            login_user(user)
            next_page = request.args.get("next")
            return redirect(next_page) if next_page else redirect(url_for("index"))
        else:
            flash("Login Unsuccessful. Please check username and password", "danger")
    return render_template("login.html", title="Login", form=form)

@app.route("/logout")
@login_required
def logout():
    logout_user()
    return redirect(url_for("index"))

@app.route("/admin")
@login_required
def admin_dashboard():
    if not current_user.is_authenticated or not current_user.is_admin:
        return redirect(url_for("login"))
    return render_template("/admin/index.html")

@app.route("/admin/manage_results", methods=["GET", "POST"])
@login_required
def manage_results():
    form = ScoreForm()
    students = Student.query.all()
    subjects = Subject.query.all()
    if request.method == "POST":
        for key, value in request.form.items():
            if key.startswith("student_id_new_"):
                student_id = value
                result_id = key.split("_")[-1]
                subject_id = request.form.get(f"subject_id_new_{result_id}")
                class_assessment = request.form.get(f"class_assessment_new_{result_id}")
                summative_test = request.form.get(f"summative_test_new_{result_id}")
                exam = request.form.get(f"exam_new_{result_id}")
                total = request.form.get(f"total_new_{result_id}")
                if subject_id and class_assessment and summative_test and exam and total:
                    new_score = Score(
                        student_id=student_id,
                        subject_id=subject_id,
                        class_assessment=class_assessment,
                        summative_test=summative_test,
                        exam=exam,
                        total=total,
                        term=form.term.data,
                        session=form.session.data,
                    )
                    db.session.add(new_score)
                else:
                    flash("All fields are required for new rows.", "error")
            else:
                try:
                    result_id = key.split("_")[2]
                except IndexError:
                    continue
                student_id = request.form.get(f"student_id_{result_id}")
                subject_id = request.form.get(f"subject_id_{result_id}")
                class_assessment = request.form.get(f"class_assessment_{result_id}")
                summative_test = request.form.get(f"summative_test_{result_id}")
                exam = request.form.get(f"exam_{result_id}")
                total = request.form.get(f"total_{result_id}")
                if "new_" in result_id:
                    continue
                else:
                    score = Score.query.get(result_id)
                    if score:
                        score.student_id = student_id
                        score.subject_id = subject_id
                        score.class_assessment = class_assessment
                        score.summative_test = summative_test
                        score.exam = exam
                        score.total = total
                    else:
                        flash(f"Score with ID {result_id} not found.", "error")
        db.session.commit()
        flash("Scores updated successfully!", "success")
        return redirect(url_for("manage_results"))
    results = Score.query.all()
    return render_template(
        "admin/manage_results.html",
        form=form,
        results=results,
        students=students,
        subjects=subjects,
    )

@app.route("/results")
@login_required
def student_result_portal():
    if current_user.is_admin:
        return redirect(url_for("admin_dashboard"))
    student = Student.query.filter_by(user_id=current_user.id).first()
    if not student:
        flash("Student not found", "alert alert-danger")
        return redirect(url_for("index"))
    results = Score.query.filter_by(student_id=student.id).all()
    if not results:
        flash("No results found for this student", "alert alert-info")
        return redirect(url_for("index"))
    return render_template(
        "view_results.html", title="View Results", student=student, results=results, 
        school_name="Aunty Anne's Int'l School"
    )


@app.route("/admin/classes/<entry_class>")
def students_by_class(entry_class):
    students = Student.query.filter_by(entry_class=entry_class).all()
    return render_template(
        "admin/students_by_class.html", students=students, entry_class=entry_class
    )


@app.route("/admin/edit_student/<int:student_id>", methods=["GET", "POST"])
def edit_student(student_id):
    student = Student.query.get_or_404(student_id)
    form = EditStudentForm()

    if form.validate_on_submit():
        student.username = form.username.data
        student.entry_class = form.entry_class.data
        student.first_name = form.first_name.data
        student.last_name = form.last_name.data
        # Update other fields as needed
        db.session.commit()
        flash('Student updated successfully!', 'success')
        return redirect(url_for('students_by_class', entry_class=student.entry_class))
    elif request.method == 'GET':
        form.username.data = student.username
        form.entry_class.data = student.entry_class
        form.first_name = student.first_name
        form.last_name = student.last_name
        # Populate other fields as necessary

    return render_template('admin/edit_student.html', form=form, student=student)
