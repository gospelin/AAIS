{{-- resources/views/admin/classes/class_table.blade.php --}}
@forelse($classes as $class)
    <tr>
        <td>{{ $class->name }}</td>
        <td>{{ $class->section ?? 'N/A' }}</td>
        <td>{{ $class->hierarchy }}</td>
        <td>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.classes.edit', $class->id) }}" class="action-btn" title="Edit Class">
                    <i class="bx bx-edit"></i>
                </a>
                <a href="{{ route('admin.classes.delete', $class->id) }}" class="action-btn" title="Delete Class">
                    <i class="bx bx-trash"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center text-[var(--text-secondary)]">No classes found.</td>
    </tr>
@endforelse
