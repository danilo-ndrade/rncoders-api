<?php

namespace App\Http\Requests\Update;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @param string $categorySlug
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @param string $categorySlug
     *
     * @return array<string, string>
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:categories,name,' . $this->segment(3)
        ];
    }
}