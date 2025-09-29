<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageResultsRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'results.*.subject_id' => 'required|exists:subjects,id',
            'results.*.class_assessment' => 'nullable|numeric|min:0|max:20',
            'results.*.summative_test' => 'nullable|numeric|min:0|max:20',
            'results.*.exam' => 'nullable|numeric|min:0|max:60',
            'next_term_begins' => 'nullable|string',
            'date_issued' => 'nullable|string',
            'position' => 'nullable|string|max:255',
        ];
    }
}
