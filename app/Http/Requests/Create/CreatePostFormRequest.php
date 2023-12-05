<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostFormRequest extends FormRequest
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
            'title' => 'required|max:255|unique:posts',
            'slug' => 'required|max:255|unique:posts',
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp|max:299',
            'published_at' => 'required|date',
            'description' => 'required|max:255',
            'content' => 'required',
        ];
    }
}