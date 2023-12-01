<?php
namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string>
     */
    public function rules()
    {
        $email_rules = '';

        if (is_numeric($this::segment(3))) {
            $user = User::find($this::segment(3));
            if (!is_null($user)) {
                $email_rules = 'required|email|unique:App\Models\User,email,' . $user->id;
            }
        } else {
            $email_rules = 'required|email|unique:App\Models\User,email';
        }

        return [
            'name' => 'required|string|between:2,100',
            'email' => $email_rules,
            'password' => 'nullable|min:8|confirmed',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string>
     */
    public function messages()
    {
        return [
            'name.required'          => 'O nome é obrigatório!',
            'name.string'            => 'O nome deve ser uma palavra ou frase!',
            'name.between'           => 'O nome deve ter entre 2 e 100 caracteres!',
            'email.required'         => 'O email é obrigatório!',
            'email.unique'           => 'Email já cadastrado!',
            'password.confirmed'     => 'As senhas informadas não são iguais!',
            'password.min'           => 'A senha deve possuir pelo menos 8 characters!'
        ];
    }
}
