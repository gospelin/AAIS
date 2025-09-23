<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResultFieldRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'field' => 'required|in:class_assessment,summative_test,exam,next_term_begins,date_issued,position',
            'value' => 'nullable',
            'class_assessment' => 'nullable|numeric|min:0|max:20',
            'summative_test' => 'nullable|numeric|min:0|max:20',
            'exam' => 'nullable|numeric|min:0|max:60',
            'next_term_begins' => 'nullable|date',
            'date_issued' => 'nullable|date',
            'position' => 'nullable|string|max:255',
        ];
    }
}
