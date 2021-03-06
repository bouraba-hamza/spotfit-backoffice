<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Base64ToPngRequest extends FormRequest
{ 
    public const VALIDATION_MESSAGES = [
'base64ToPng.name' => 'le name du base64ToPng est obligatoire ', 
'base64ToPng.code' => 'le code du base64ToPng est obligatoire ', 

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
 'name' => 'required', 
'code' => 'required', 

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




