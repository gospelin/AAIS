"""Add class_id foreign key to result

Revision ID: 793d6e369a1a
Revises: fe90863c941d
Create Date: 2025-03-09 06:55:09.172134

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = '793d6e369a1a'
down_revision = 'fe90863c941d'
branch_labels = None
depends_on = None


def upgrade():
    with op.batch_alter_table('result', schema=None) as batch_op:
        # Check if the column already exists
        inspector = sa.inspect(op.get_bind())
        columns = inspector.get_columns('result')
        column_names = [column['name'] for column in columns]

        if 'class_id' not in column_names:
            batch_op.add_column(sa.Column('class_id', sa.Integer(), nullable=False, server_default='0'))  # Temporary default
            batch_op.create_foreign_key(None, 'classes', ['class_id'], ['id'])
            batch_op.drop_constraint('unique_result', type_='unique')
            batch_op.create_unique_constraint('unique_result', ['student_id', 'subject_id', 'class_id', 'term', 'session'])

    # ### end Alembic commands ###


def downgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    with op.batch_alter_table('student_class_history', schema=None) as batch_op:
        batch_op.create_index('idx_student_session', ['student_id', 'session_id'], unique=False)

    with op.batch_alter_table('result', schema=None) as batch_op:
        batch_op.drop_constraint(None, type_='foreignkey')
        batch_op.drop_constraint('unique_result', type_='unique')
        batch_op.create_unique_constraint('unique_result', ['student_id', 'subject_id', 'term', 'session'])
        batch_op.drop_column('class_id')

    # ### end Alembic commands ###
