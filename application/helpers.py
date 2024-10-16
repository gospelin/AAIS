import random, string, time
from . import db
from flask import request, abort, flash
from .models import Student, Subject, Result
from sqlalchemy.exc import SQLAlchemyError
from functools import wraps
from datetime import datetime


login_attempts = {}


def rate_limit(limit, per):
    """
    Decorator function that limits the rate at which a function can be called.

    Args:
        limit (int): The maximum number of calls allowed within the specified time period.
        per (int): The time period (in seconds) within which the maximum number of calls is allowed.

    Returns:
        function: The decorated function.

    Raises:
        HTTPException: If the maximum number of calls has been exceeded, a 429 Too Many Requests error is raised.
    """
    def decorator(f):
        @wraps(f)
        def wrapped(*args, **kwargs):
            ip = request.remote_addr
            now = time.time()
            attempts = login_attempts.get(ip, [])
            attempts = [timestamp for timestamp in attempts if now - timestamp < per]

            if len(attempts) >= limit:
                abort(429)  # Too Many Requests

            attempts.append(now)
            login_attempts[ip] = attempts
            return f(*args, **kwargs)

        return wrapped

    return decorator


def generate_unique_username(first_name, last_name):
    """
    Generate a unique username based on the given first name and last name.

    Args:
        first_name (str): The first name of the user.
        last_name (str): The last name of the user.

    Returns:
        str: The generated unique username.
    """
    username = f"{first_name.strip().lower()}.{last_name.strip().lower()}"
    existing_user = Student.query.filter_by(username=username).first()
    if existing_user:
        random_suffix = "".join(
            random.choices(string.ascii_lowercase + string.digits, k=4)
        )
        username = f"{username}{random_suffix}"
    return username


def calculate_grade(total):
    if total >= 95:
        return "A+"
    elif total >= 80:
        return "A"
    elif total >= 70:
        return "B+"
    elif total >= 65:
        return "B"
    elif total >= 60:
        return "C+"
    elif total >= 50:
        return "C"
    elif total >= 40:
        return "D"
    elif total >= 30:
        return "E"
    else:
        return "F"


def generate_remark(total):
    if total >= 95:
        return "Outstanding"
    elif total >= 80:
        return "Excellent"
    elif total >= 70:
        return "Very Good"
    elif total >= 65:
        return "Good"
    elif total >= 60:
        return "Credit"
    elif total >= 50:
        return "Credit"
    elif total >= 40:
        return "Poor"
    elif total >= 30:
        return "Very Poor"
    else:
        return "Failed"


def calculate_grand_total(results):
    """Calculate the total score from all results."""
    # return sum(result.total for result in results if result.total is not None)
    return sum(result.total for result in results)


def get_last_term(current_term):
    """
    Get the last term in the academic sequence.

    Args:
        current_term (str): The current term in the academic sequence.

    Returns:
        str or None: The last term in the academic sequence, or None if the current term is not found in the sequence.
    """
    term_sequence = ["First Term", "Second Term", "Third Term"]
    if current_term in term_sequence:
        current_index = term_sequence.index(current_term)
        last_index = current_index - 1 if current_index > 0 else None
        return term_sequence[last_index] if last_index is not None else None
    return None


def calculate_average(results):
    """
    Calculate the average score from the results.

    Args:
        results (list): A list of result objects.

    Returns:
        float: The average score calculated from the valid results. If there are no valid results, returns 0.
    """
    grand_total = 0
    non_zero_subjects = 0

    for result in results:
        if result.total and result.total > 0:  # Ensure total is valid
            grand_total += result.total
            non_zero_subjects += 1

    return grand_total / non_zero_subjects if non_zero_subjects > 0 else 0


def calculate_cumulative_average(results, current_term_average):
    """
    Calculate the cumulative average from the current and last term averages.

    Args:
        results (list): A list of objects containing the last term average.
        current_term_average (float): The average for the current term.

    Returns:
        float: The calculated cumulative average.

    """
    # last_term_average = (
    #    float(results[0].last_term_average)
    #    if results and results[0].last_term_average
    #    else 0
    # )

    # if last_term_average and current_term_average:
    #    cumulative_average = (last_term_average + current_term_average) / 2
    # else:
    #    cumulative_average = current_term_average

    # return cumulative_average
    
    last_term_average = 0
    cumulative_average = current_term_average
    if results:
        last_term_average = (
            float(results[0].last_term_average) if results[0].last_term_average else 0
        )

    if last_term_average and current_term_average:
        cumulative_average = (last_term_average + current_term_average) / 2

    return cumulative_average


def get_subjects_by_class_name(student_class_history):
    """Get subjects based on the student's entry class.

    Args:
        student_class_history (object): The student's class history object.

    Returns:
        list: A list of subjects based on the student's entry class.
    """
    # Assuming 'student_class_history' has an attribute 'class_name' that contains the class name
    class_name = (
        student_class_history.class_name
    )  # Adjust this if the attribute name is different

    if "Nursery" in class_name:
        return Subject.query.filter_by(section="Nursery").all()
    elif "Basic" in class_name:
        return Subject.query.filter_by(section="Basic").all()
    else:
        return Subject.query.filter_by(section="Secondary").all()


def update_results(student, subjects, term, session_year, form):
    """
    Update or create results for the student based on form data.

    Args:
        student (Student): The student object for which the results are being updated.
        subjects (list): A list of subject objects for which the results are being updated.
        term (str): The term for which the results are being updated.
        session_year (str): The session year for which the results are being updated.
        form (Form): The form object containing the data for updating the results.

    Raises:
        Exception: If an error occurs while updating the results.
    """
    try:
        for subject in subjects:
            class_assessment = request.form.get(f"class_assessment_{subject.id}", "")
            summative_test = request.form.get(f"summative_test_{subject.id}", "")
            exam = request.form.get(f"exam_{subject.id}", "")

            class_assessment_value = int(class_assessment) if class_assessment else None
            summative_test_value = int(summative_test) if summative_test else None
            exam_value = int(exam) if exam else None
            total = (
                (class_assessment_value or 0)
                + (summative_test_value or 0)
                + (exam_value or 0)
            )

            grade = calculate_grade(total)
            remark = generate_remark(total)

            result = Result.query.filter_by(
                student_id=student.id,
                subject_id=subject.id,
                term=term,
                session_year=session_year,  # Use session_year directly
            ).first()

            if result:
                result.class_assessment = class_assessment_value
                result.summative_test = summative_test_value
                result.exam = exam_value
                result.total = total
                result.grade = grade
                result.remark = remark
                result.next_term_begins = form.next_term_begins.data
                result.last_term_average = form.last_term_average.data
                result.position = form.position.data
                result.date_issued = form.date_issued.data
            else:
                new_result = Result(
                    student_id=student.id,
                    subject_id=subject.id,
                    term=term,
                    session_year=session_year,  # Use session_year directly
                    class_assessment=class_assessment_value,
                    summative_test=summative_test_value,
                    exam=exam_value,
                    total=total,
                    grade=grade,
                    remark=remark,
                    next_term_begins=form.next_term_begins.data,
                    last_term_average=form.last_term_average.data,
                    position=form.position.data,
                    date_issued=form.date_issued.data,
                )
                db.session.add(new_result)

        db.session.commit()
    except Exception as e:
        db.session.rollback()
        raise e


def calculate_results(student_id, term, session_year):
    """Calculate student results and averages.

    Args:
        student_id (int): The ID of the student.
        term (str): The current term.
        session_year (str): The session year.

    Returns:
        tuple: A tuple containing the following values:
            - results (list): A list of Result objects for the current term and session.
            - grand_total (float): The grand total of all results.
            - average (float): The average of all results.
            - cumulative_average (float): The cumulative average of all results.
            - results_dict (dict): A dictionary mapping subject IDs to Result objects.
    """
    # Fetch results for the current term and session
    results = Result.query.filter_by(
        student_id=student_id,
        term=term,
        session_year=session_year,  # Use session_year directly
    ).all()

    grand_total = calculate_grand_total(results)
    average = round(calculate_average(results), 1)

    last_term = get_last_term(term)
    last_term_results = Result.query.filter_by(
        student_id=student_id,
        term=last_term,
        session_year=session_year,  # Use session_year directly
    ).all()

    last_term_average = (
        round(calculate_average(last_term_results), 1) if last_term_results else 0
    )

    for res in results:
        res.last_term_average = last_term_average

    cumulative_average = round(calculate_cumulative_average(results, average), 1)
    results_dict = {result.subject_id: result for result in results}

    return results, grand_total, average, cumulative_average, results_dict
