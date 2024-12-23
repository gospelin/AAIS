from werkzeug.security import generate_password_hash, check_password_hash
from application import db
from flask_login import UserMixin
from datetime import datetime


class Session(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    year = db.Column(db.String(20), unique=True, nullable=False)  # e.g., "2023/2024"
    is_current = db.Column(db.Boolean, default=False)
    current_term = db.Column(db.String(20), nullable=True)

    def __repr__(self):
        return f"<Session {self.year}>"

    @staticmethod
    def get_current_session():
        return Session.query.filter_by(is_current=True).first()
        # return session.year if session else None
        
    @staticmethod
    def get_current_session_and_term(include_term=False):
        """Helper function to get the current session and optionally the current term."""
        session = Session.query.filter_by(is_current=True).first()
        if not session:
            return None, None if include_term else None
    
        if include_term:
            return session.year, session.current_term
        return session.year

    @staticmethod
    def set_current_session(session_id, term):
        # First, unset the current session
        Session.query.update({Session.is_current: False})
        db.session.commit()

        # Set the new session as the current one
        new_session = Session.query.get(session_id)
        if new_session:
            new_session.is_current = True
            new_session.current_term = term
            db.session.commit()
            return new_session
        return None

# class Classes(db.Model):
#     id = db.Column(db.Integer, primary_key=True)
#     name = db.Column(db.String(100), nullable=False)
#     section = db.Column(db.String(50), nullable=True)
    
#     # Relationship with Subjects (A class can offer many subjects)
#     subjects = db.relationship("Subject", backref="class_offered", lazy=True)
    
#     # Relationship with Results (A class will have results for its students)
#     results = db.relationship("Result", backref="class_result", lazy=True)

#     def __repr__(self):
#         return f"<Class {self.name}>"

#     @classmethod
#     def create_class(cls, name, section):
#         """Helper function to create a new class."""
#         new_class = cls(name=name, section=section)
#         db.session.add(new_class)
#         db.session.commit()
#         return new_class

#     def edit_class(self, name=None, section=None):
#         """Helper function to edit an existing class."""
#         if name:
#             self.name = name
#         if section:
#             self.section = section
#         db.session.commit()
    
#     def delete_class(self):
#         """Helper function to delete an existing class."""
#         db.session.delete(self)
#         db.session.commit()


class StudentClassHistory(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    student_id = db.Column(db.Integer, db.ForeignKey("student.id"), nullable=False)
    session_id = db.Column(db.Integer, db.ForeignKey("session.id"), nullable=False)
    class_name = db.Column(db.String(50), nullable=False)
    # class_id = db.Column(db.Integer, db.ForeignKey("classes.id"), nullable=False)

    student = db.relationship("Student", backref="class_history", lazy=True)
    session = db.relationship("Session", backref="class_history", lazy=True)
    # classes = db.relationship("Class, backref="class_history", lazy=True)

    @classmethod
    def get_class_by_session(cls, student_id, session_year_str):
        """
        Get the student's class for a given academic session.
        If the session is a string, retrieve the session object first.
        """
        # If session is passed as a string (e.g., "2023/2024"), get the session object
        if isinstance(session_year_str, str):
            session_year = Session.query.filter_by(year=session_year_str).first()
        else:
            session_year = session_year_str  # Assume session is an object

        if not session_year:
            # Log or handle the case where the session doesn't exist
            return None

        # Query the class history for the student in the specified session
        class_history = cls.query.filter_by(
            student_id=student_id, session_id=session_year.id
        ).first()

        return class_history.class_name if class_history else None

    def __repr__(self):
        return f"<StudentClassHistory Student: {self.student_id}, Class: {self.class_name}, Session: {self.session.year}>"

class Student(db.Model, UserMixin):
    id = db.Column(db.Integer, primary_key=True)
    reg_no = db.Column(db.String(50), nullable=True)
    first_name = db.Column(db.String(50), nullable=False)
    middle_name = db.Column(db.String(50), nullable=True)
    last_name = db.Column(db.String(50), nullable=False)
    username = db.Column(db.String(50), unique=True, nullable=False)
    password = db.Column(db.String(200), nullable=False)
    gender = db.Column(db.String(10), nullable=False)
    date_of_birth = db.Column(db.String(20), nullable=True)  # Stored as "YYYY-MM-DD"
    parent_name = db.Column(db.String(70), nullable=True)
    previous_class = db.Column(db.String(50), nullable=True)
    parent_phone_number = db.Column(db.String(11), nullable=True)
    address = db.Column(db.String(255), nullable=True)
    parent_occupation = db.Column(db.String(100), nullable=True)
    state_of_origin = db.Column(db.String(50), nullable=True)
    local_government_area = db.Column(db.String(50), nullable=True)
    religion = db.Column(db.String(50), nullable=True)
    date_registered = db.Column(db.String(19), nullable=True)  # Stored as "YYYY-MM-DD HH:MM:SS"
    approved = db.Column(db.Boolean, default=False)
    user_id = db.Column(db.Integer, db.ForeignKey("user.id"))
    has_paid_fee = db.Column(db.Boolean, default=False)

    results = db.relationship("Result", backref="student", lazy=True)

    @staticmethod
    def get_latest_class(student_id):
        """
        Get the student's latest class by finding the latest session.
        Requires student_id to be passed since this is a static method.
        """
        latest_session = Student.get_latest_session(student_id)
        return Student.get_class_by_session(student_id, latest_session)

    @staticmethod
    def get_latest_session(student_id):
        """
        Get the most recent session from the class history.
        Requires student_id to be passed since this is a static method.
        """
        latest_class_history = (
            StudentClassHistory.query.filter_by(student_id=student_id)
            .order_by(StudentClassHistory.id.desc())
            .first()
        )
        return latest_class_history.session if latest_class_history else None

    def __repr__(self):
        return f"<Student {self.first_name} {self.last_name}>"


class User(db.Model, UserMixin):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(50), unique=True, nullable=False)
    password_hash = db.Column(db.String(200), nullable=False)
    is_admin = db.Column(db.Boolean, default=False)
    student = db.relationship("Student", backref="user", uselist=False)

    def __repr__(self):
        return f"<User {self.username}>"

    def set_password(self, password):
        self.password_hash = generate_password_hash(password)

    def check_password(self, password):
        return check_password_hash(self.password_hash, password)


class Subject(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    section = db.Column(db.String(20), nullable=False)
    deactivated = db.Column(db.Boolean, nullable=False, default=False)  # New attribute

    results = db.relationship("Result", backref="subject", lazy=True)
    __table_args__ = (db.UniqueConstraint("name", "section", name="_name_section_uc"),)

    def __repr__(self):
        return f"<Subject {self.name}>"


class Result(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    student_id = db.Column(db.Integer, db.ForeignKey("student.id"), nullable=False)
    subject_id = db.Column(db.Integer, db.ForeignKey("subject.id"), nullable=False)
    term = db.Column(db.String(20), nullable=False)
    session = db.Column(db.String(20), nullable=False)
    subjects_offered = db.Column(db.Integer, nullable=True)
    
    class_assessment = db.Column(db.Integer, nullable=True)
    summative_test = db.Column(db.Integer, nullable=True)
    exam = db.Column(db.Integer, nullable=True)
    total = db.Column(db.Integer, nullable=True)
    grade = db.Column(db.String(5), nullable=True)
    remark = db.Column(db.String(100), nullable=True)
    grand_total = db.Column(db.Integer, nullable=True)
   
    
    next_term_begins = db.Column(db.String(100), nullable=True)
    last_term_average = db.Column(db.Float, nullable=True)
    term_average = db.Column(db.Float, nullable=True)
    cumulative_average = db.Column(db.Float, nullable=True)
    position = db.Column(db.String(10), nullable=True)
    
    principal_remark = db.Column(db.String(100), nullable=True)
    teacher_remark = db.Column(db.String(100), nullable=True)
    
    created_at = db.Column(db.String(19), nullable=True)
    date_issued = db.Column(db.String(100), nullable=True)

    def __repr__(self):
        return f"<Result Student ID: {self.student_id}, Subject ID: {self.subject_id}>"
