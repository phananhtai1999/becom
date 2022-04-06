<?php

namespace App\Abstracts;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        if (Request::wantsJson() || Request::is('api/*')) {
            throw new ValidationException(
                $validator,
                new JsonResponse(
                    [
                        'status' => false,
                        "code" => 0,
                        "locale" => app()->getLocale(),
                        'message' => __("messages.given_data_invalid"),
                        'errors' => $validator->getMessageBag()->toArray()
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
