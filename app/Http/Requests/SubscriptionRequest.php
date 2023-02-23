<?php

namespace App\Http\Requests;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SubscriptionRequest extends FormRequest
{
    use ResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'cardToken'   => ['required_without:cardNo'],
            'cardNo'      => ['required_without:cardToken', 'numeric', 'digits_between:14,19'],
            'cardOwner'   => ['required_without:cardToken'],
            'expireMonth' => ['required_without:cardToken', 'min:2', 'max:2'],
            'expireYear'  => ['required_without:cardToken', 'min:2', 'max:2'],
            'cvv'         => ['required_without:cardToken', 'numeric', 'digits_between:3,4'],
            'packageId'   => ['required'],
            'platform'    => ['required'],
            'language'    => ['required']
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'required' => 'This field is required'
        ];
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            $this->sendError(message: 'Invalid parameters', data: $errors, code: JsonResponse::HTTP_BAD_REQUEST)
        );
    }
}
