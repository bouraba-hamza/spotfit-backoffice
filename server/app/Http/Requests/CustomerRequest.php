<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerRequest extends FormRequest
{
    public const VALIDATION_MESSAGES = [
        'email.required' => "Le champ email est requis",
        'email.email' => "Il faut respecter le format de mail",
        'email.unique' => "L'email déjà pris",
        'password.required' => "Le champ mote de passe est requis",
        'password.min' => "La longueur du mot de passe doit être d'au moins 6 caractères",
        'password.confirmed' => "La confirmation du mot de passe ne correspond pas.",
    ];

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
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:accounts,email',
            'password' => 'required|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return Self::VALIDATION_MESSAGES;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(["errors" => $validator->errors()->all()], 200));
    }
}
