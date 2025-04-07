"""Drop email and section from Teacher

Revision ID: 14a6443c2c69
Revises: 2837f8bf9b46
Create Date: 2025-03-03 17:47:03.527580

"""
from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import mysql

# revision identifiers, used by Alembic.
revision = '14a6443c2c69'
down_revision = '2837f8bf9b46'
branch_labels = None
depends_on = None


def upgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    # with op.batch_alter_table('student_class_history', schema=None) as batch_op:
    #     batch_op.drop_index('idx_student_session')

    with op.batch_alter_table('teacher', schema=None) as batch_op:
        batch_op.drop_index('email')
        batch_op.drop_column('section')
        batch_op.drop_column('email')

    # ### end Alembic commands ###


def downgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    with op.batch_alter_table('teacher', schema=None) as batch_op:
        batch_op.add_column(sa.Column('email', mysql.VARCHAR(length=100), nullable=True))
        batch_op.add_column(sa.Column('section', mysql.VARCHAR(length=20), nullable=False))
        batch_op.create_index('email', ['email'], unique=True)

    with op.batch_alter_table('student_class_history', schema=None) as batch_op:
        batch_op.create_index('idx_student_session', ['student_id', 'session_id'], unique=False)

    # ### end Alembic commands ###
