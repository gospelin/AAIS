@foreach($subjects ?? [$subject] as $subject)
    <li class="list-group-item" data-subject-id="{{ $subject->id }}">
        {{ $subject->name }} {{ $subject->deactivated ? '(Deactivated)' : '' }}
        <span class="action-buttons">
            <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="action-btn edit">
                <i class="bx bx-edit"></i> Edit
            </a>
            @if(!$subject->deactivated)
                <form action="{{ route('admin.subjects.manage') }}" method="POST" style="display:inline;"
                    class="deactivate-subject-form">
                    @csrf
                    <input type="hidden" name="deactivate_subject_id" value="{{ $subject->id }}">
                    <button type="submit" class="action-btn deactivate" data-action="deactivate">
                        <i class="bx bx-block"></i> Deactivate
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.subjects.manage') }}" method="POST" style="display:inline;"
                class="delete-subject-form">
                @csrf
                <input type="hidden" name="delete_subject_id" value="{{ $subject->id }}">
                <button type="submit" class="action-btn delete" data-action="delete">
                    <i class="bx bx-trash"></i> Delete
                </button>
            </form>
        </span>
    </li>
@endforeach