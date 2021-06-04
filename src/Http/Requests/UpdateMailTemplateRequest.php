<?php

namespace Statikbe\NovaMailEditor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMailTemplateRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        //TODO optional: validate values inside the json
        return [
            //TODO: validate mail_type
            'mail_type' => ['required', 'string', ],
            'name' => ['required', 'string'],
            'subject' => ['required', 'json'],
            'body' => ['required', 'json'],
            'sender_name' => ['required', 'json'],
            'sender_email' => ['required', 'json'],
            'recipients' => ['required', 'json'],
            'cc' => ['required', 'json'],
            'bcc' => ['required', 'json'],
            'attachments' => ['required', 'json'],
            //'design' => ['required', 'string'],
            'render_engine' => ['required', 'string'],
        ];
    }
}
