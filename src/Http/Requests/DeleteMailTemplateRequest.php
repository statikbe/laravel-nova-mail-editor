<?php

namespace Statikbe\NovaMailEditor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMailTemplateRequest extends FormRequest
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

    public function rules()
    {
        return [];
    }
}
