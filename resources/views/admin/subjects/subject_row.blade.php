<!-- resources/views/admin/subjects/subject_row.blade.php -->
<tr>
    <td>{{ $subject->name }}</td>
    <td>{{ $subject->code }}</td>
    <td>{{ $subject->description ?? 'N/A' }}</td>
    <td>{{ $subject->deactivated ? 'Deactivated' : 'Active' }}</td>
    <td>
        <div class="action-buttons">
            <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="action-btn">
                <i class="bx bx-edit"></i> Edit
            </a>
            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="action-btn" data-action="delete">
                    <i class="bx bx-trash"></i> Delete
                </button>
            </form>
            <a href="{{ route('admin.subjects.edit_assignment', urlencode($subject->classes->pluck('name')->first() ?? '')) }}" class="action-btn">
                <i class="bx bx-link"></i> Assign
            </a>
        </div>
    </td>
</tr>