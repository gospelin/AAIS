from flask_wtf import FlaskForm
from wtforms import StringField, SelectField, DateField, SubmitField, PasswordField, IntegerField
from wtforms.validators import DataRequired, Email, Length


class StudentRegistrationForm(FlaskForm):
    first_name = StringField("First Name", validators=[DataRequired(), Length(max=50)])
    last_name = StringField("Last Name", validators=[DataRequired(), Length(max=50)])
    email = StringField("Email", validators=[DataRequired(), Email(), Length(max=100)])
    gender = SelectField(
        "Gender",
        choices=[("male", "Male"), ("female", "Female")],
        validators=[DataRequired()],
    )
    date_of_birth = DateField("Date of Birth", validators=[DataRequired()])
    parent_phone_number = StringField(
        "Parent Phone Number", validators=[DataRequired(), Length(max=20)]
    )
    address = StringField("Address", validators=[DataRequired(), Length(max=255)])
    parent_occupation = StringField(
        "Parent Occupation", validators=[DataRequired(), Length(max=100)]
    )
    entry_class = SelectField(
        "Entry class",
        choices=[
            ("Creche"),
            ("Pre-Nursery"),
            ("Nursery 1"),
            ("Nursery 2"),
            ("Nursery 3"),
            ("Primary 1"),
            ("Primary 2"),
            ("Primary 3"),
            ("Primary 4"),
            ("Primary 5"),
            ("Primary 6"),
            ("JSS 1"),
            ("JSS 2"),
            ("JSS 3"),
        ],
        validate_choice=True,
        validators=[DataRequired()],
    )
    previous_class = SelectField(
        "Previous Class (if any)",
        choices=[
            ("Creche"),
            ("Pre-Nursery"),
            ("Nursery 1"),
            ("Nursery 2"),
            ("Nursery 3"),
            ("Primary 1"),
            ("Primary 2"),
            ("Primary 3"),
            ("Primary 4"),
            ("Primary 5"),
            ("Primary 6"),
            ("JSS 1"),
            ("JSS 2"),
            ("JSS 3"),
        ],
        validate_choice=True,
    )
    state_of_origin = StringField(
        "State of Origin", validators=[DataRequired(), Length(max=50)]
    )
    local_government_area = StringField(
        "Local Government Area", validators=[DataRequired(), Length(max=50)]
    )
    religion = StringField("Religion", validators=[DataRequired(), Length(max=50)])
    submit = SubmitField("Register")


class ScoreForm(FlaskForm):
    student_id = SelectField("Student", coerce=int, validators=[DataRequired()])
    subject_id = SelectField("Subject", coerce=int, validators=[DataRequired()])
    class_assessment = IntegerField("Class Assessment", validators=[DataRequired()])
    summative_test = IntegerField("Summative Test", validators=[DataRequired()])
    exam = IntegerField("Exam", validators=[DataRequired()])
    term = StringField("Term", validators=[DataRequired()])
    session = StringField("Session", validators=[DataRequired()])
    submit = SubmitField("Save")


class LoginForm(FlaskForm):
    username = StringField(
        "Username", validators=[DataRequired(), Length(min=2, max=20)]
    )
    password = PasswordField("Password", validators=[DataRequired()])
    submit = SubmitField("Login")


class EditStudentForm(FlaskForm):
    first_name = StringField("First Name", validators=[DataRequired()])
    last_name = StringField("Last Name", validators=[DataRequired()])
    username = StringField("Username", validators=[DataRequired()])
    entry_class = SelectField(
        "Class",
        choices=[
            ("Primary 1", "Primary 1"),
            ("Primary 2", "Primary 2"),
            ("Primary 3", "Primary 3"),
            ("Primary 4", "Primary 4"),
            ("Primary 5", "Primary 5"),
            ("Primary 6", "Primary 6"),
            ("JSS 1", "JSS 1"),
            ("JSS 2", "JSS 2"),
            ("JSS 3", "JSS 3"),
        ],
    )
    submit = SubmitField("Save")
