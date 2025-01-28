<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Api\V1\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTagRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $existingTags = Tag::whereIn('name', $this->input('tags'))->pluck('name')->toArray();
        return [
            'tags' => ['required', 'array'],
            'tags.*' => ['required', 'string', 'max:255', 'distinct', Rule::notIn($existingTags)]
        ];
    }


    public function messages(): array
    {
        return [
            'tags.*.not_in' => 'قيمة الحقل موجودة مسبقاً',
        ];
    }
}
