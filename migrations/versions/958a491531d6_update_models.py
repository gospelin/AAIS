"""Update models

Revision ID: 958a491531d6
Revises: 0a7f0f427344
Create Date: 2024-05-15 03:24:28.171967

"""
from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import mysql

# revision identifiers, used by Alembic.
revision = '958a491531d6'
down_revision = '0a7f0f427344'
branch_labels = None
depends_on = None


def upgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    with op.batch_alter_table('result', schema=None) as batch_op:
        batch_op.add_column(sa.Column('class_assessment', sa.Float(), nullable=True))
        batch_op.add_column(sa.Column('summative_test', sa.Float(), nullable=True))
        batch_op.add_column(sa.Column('exam', sa.Float(), nullable=True))
        batch_op.add_column(sa.Column('total', sa.Float(), nullable=True))
        batch_op.drop_column('math_score')
        batch_op.drop_column('science_score')

    with op.batch_alter_table('student', schema=None) as batch_op:
        batch_op.add_column(sa.Column('username', sa.String(length=50), nullable=False))
        batch_op.add_column(sa.Column('password_hash', sa.String(length=200), nullable=False))
        batch_op.create_unique_constraint(None, ['username'])

    # ### end Alembic commands ###


def downgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    with op.batch_alter_table('student', schema=None) as batch_op:
        batch_op.drop_constraint(None, type_='unique')
        batch_op.drop_column('password_hash')
        batch_op.drop_column('username')

    with op.batch_alter_table('result', schema=None) as batch_op:
        batch_op.add_column(sa.Column('science_score', mysql.FLOAT(), nullable=True))
        batch_op.add_column(sa.Column('math_score', mysql.FLOAT(), nullable=True))
        batch_op.drop_column('total')
        batch_op.drop_column('exam')
        batch_op.drop_column('summative_test')
        batch_op.drop_column('class_assessment')

    # ### end Alembic commands ###
