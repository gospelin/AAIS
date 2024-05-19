"""Reverting changes

Revision ID: b754c8c1f822
Revises: 350f988f87d8
Create Date: 2024-05-18 23:38:37.497742

"""
from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import mysql

# revision identifiers, used by Alembic.
revision = 'b754c8c1f822'
down_revision = '350f988f87d8'
branch_labels = None
depends_on = None


def upgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    op.drop_table('result')
    with op.batch_alter_table('admin', schema=None) as batch_op:
        batch_op.drop_index('username')

    op.drop_table('admin')
    with op.batch_alter_table('student_login', schema=None) as batch_op:
        batch_op.drop_index('username')

    op.drop_table('student_login')
    # ### end Alembic commands ###


def downgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    op.create_table('student_login',
    sa.Column('id', mysql.INTEGER(), autoincrement=True, nullable=False),
    sa.Column('username', mysql.VARCHAR(length=50), nullable=False),
    sa.Column('password_hash', mysql.VARCHAR(length=200), nullable=False),
    sa.Column('is_active', mysql.TINYINT(display_width=1), autoincrement=False, nullable=True),
    sa.PrimaryKeyConstraint('id'),
    mysql_collate='utf8mb4_0900_ai_ci',
    mysql_default_charset='utf8mb4',
    mysql_engine='InnoDB'
    )
    with op.batch_alter_table('student_login', schema=None) as batch_op:
        batch_op.create_index('username', ['username'], unique=True)

    op.create_table('admin',
    sa.Column('id', mysql.INTEGER(), autoincrement=True, nullable=False),
    sa.Column('username', mysql.VARCHAR(length=50), nullable=False),
    sa.Column('password_hash', mysql.VARCHAR(length=200), nullable=False),
    sa.Column('is_active', mysql.TINYINT(display_width=1), autoincrement=False, nullable=True),
    sa.PrimaryKeyConstraint('id'),
    mysql_collate='utf8mb4_0900_ai_ci',
    mysql_default_charset='utf8mb4',
    mysql_engine='InnoDB'
    )
    with op.batch_alter_table('admin', schema=None) as batch_op:
        batch_op.create_index('username', ['username'], unique=True)

    op.create_table('result',
    sa.Column('id', mysql.INTEGER(), autoincrement=True, nullable=False),
    sa.Column('student_id', mysql.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('class_assessment', mysql.FLOAT(), nullable=True),
    sa.Column('summative_test', mysql.FLOAT(), nullable=True),
    sa.Column('exam', mysql.FLOAT(), nullable=True),
    sa.Column('total', mysql.FLOAT(), nullable=True),
    sa.ForeignKeyConstraint(['student_id'], ['student.id'], name='result_ibfk_1'),
    sa.PrimaryKeyConstraint('id'),
    mysql_collate='utf8mb4_0900_ai_ci',
    mysql_default_charset='utf8mb4',
    mysql_engine='InnoDB'
    )
    # ### end Alembic commands ###