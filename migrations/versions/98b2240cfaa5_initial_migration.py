"""Initial migration

Revision ID: 98b2240cfaa5
Revises: 
Create Date: 2024-05-06 23:03:35.609840

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = '98b2240cfaa5'
down_revision = None
branch_labels = None
depends_on = None


def upgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    op.create_table('student',
    sa.Column('id', sa.Integer(), nullable=False),
    sa.Column('first_name', sa.String(length=50), nullable=False),
    sa.Column('last_name', sa.String(length=50), nullable=False),
    sa.Column('email', sa.String(length=100), nullable=False),
    sa.Column('gender', sa.String(length=10), nullable=False),
    sa.Column('date_of_birth', sa.Date(), nullable=False),
    sa.Column('parent_phone_number', sa.String(length=20), nullable=False),
    sa.Column('address', sa.String(length=255), nullable=False),
    sa.Column('parent_occupation', sa.String(length=100), nullable=False),
    sa.Column('entry_class', sa.String(length=50), nullable=False),
    sa.Column('previous_class', sa.String(length=50), nullable=True),
    sa.Column('state_of_origin', sa.String(length=50), nullable=False),
    sa.Column('local_government_area', sa.String(length=50), nullable=False),
    sa.Column('religion', sa.String(length=50), nullable=False),
    sa.Column('date_registered', sa.DateTime(), server_default=sa.text('now()'), nullable=True),
    sa.Column('approved', sa.Boolean(), nullable=True),
    sa.PrimaryKeyConstraint('id'),
    sa.UniqueConstraint('email')
    )
    # ### end Alembic commands ###


def downgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    op.drop_table('student')
    # ### end Alembic commands ###