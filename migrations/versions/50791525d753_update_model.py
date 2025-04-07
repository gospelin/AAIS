"""update model

Revision ID: 50791525d753
Revises: d009cc6e5701
Create Date: 2025-02-28 22:24:14.337619

"""
from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import mysql

# revision identifiers, used by Alembic.
revision = '50791525d753'
down_revision = 'd009cc6e5701'
branch_labels = None
depends_on = None


def upgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    op.create_table('audit_log',
    sa.Column('id', sa.Integer(), nullable=False),
    sa.Column('user_id', sa.Integer(), nullable=False),
    sa.Column('action', sa.String(length=100), nullable=False),
    sa.Column('timestamp', sa.DateTime(), nullable=False),
    sa.ForeignKeyConstraint(['user_id'], ['user.id'], ),
    sa.PrimaryKeyConstraint('id')
    )
    with op.batch_alter_table('student', schema=None) as batch_op:
        batch_op.add_column(sa.Column('_parent_phone_number', sa.String(length=255), nullable=True))
        batch_op.drop_column('parent_phone_number')

    with op.batch_alter_table('user', schema=None) as batch_op:
        batch_op.add_column(sa.Column('mfa_secret', sa.String(length=32), nullable=True))

    # ### end Alembic commands ###


def downgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    with op.batch_alter_table('user', schema=None) as batch_op:
        batch_op.drop_column('mfa_secret')

    with op.batch_alter_table('student', schema=None) as batch_op:
        batch_op.add_column(sa.Column('parent_phone_number', mysql.VARCHAR(length=11), nullable=True))
        batch_op.drop_column('_parent_phone_number')

    op.drop_table('audit_log')
    # ### end Alembic commands ###
