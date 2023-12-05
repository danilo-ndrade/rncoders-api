<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageFormRequest extends FormRequest
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
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp|max:299',
        ];
    }
}