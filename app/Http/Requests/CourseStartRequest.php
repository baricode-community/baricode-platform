<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseStartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'days' => 'required|array',
            'days.*.sesi_1' => 'nullable|date_format:H:i',
            'days.*.sesi_2' => 'nullable|date_format:H:i',
            'days.*.sesi_3' => 'nullable|date_format:H:i',

        ];
    }
}
