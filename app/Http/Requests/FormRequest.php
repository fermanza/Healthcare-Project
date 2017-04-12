<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest
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
     * Determine if this is a create request.
     *
     * @return bool
     */
    public function isCreate()
    {
        return $this->method() == 'POST';
    }

    /**
     * Determine if this is an edit request.
     *
     * @return bool
     */
    public function isEdit()
    {
        return $this->method() == 'PATCH';
    }
}
