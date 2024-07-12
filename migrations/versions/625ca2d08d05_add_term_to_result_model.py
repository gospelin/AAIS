"""Add term to Result model

Revision ID: 625ca2d08d05
Revises: 91848338660c
Create Date: 2024-06-14 02:41:52.471070

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = '625ca2d08d05'
down_revision = '91848338660c'
branch_labels = None
depends_on = None


def upgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    with op.batch_alter_table('result', schema=None) as batch_op:
        batch_op.add_column(sa.Column('session', sa.String(length=15), nullable=False))

    # ### end Alembic commands ###


def downgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    with op.batch_alter_table('result', schema=None) as batch_op:
        batch_op.drop_column('session')

    # ### end Alembic commands ###
