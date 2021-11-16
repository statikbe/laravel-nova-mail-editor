<?php

namespace Statikbe\NovaMailEditor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Statikbe\NovaMailEditor\MailEditor;

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
        $rules = [
            'name' => ['required', 'string'],
            'recipients' => ['required', 'array'],
            'cc' => ['nullable', 'array'],
            'cc.*' => ['nullable', 'email'],
            'bcc' => ['nullable', 'array'],
            'bcc.*' => ['nullable', 'email'],
            //'design' => ['required', 'string'],
            //'render_engine' => ['required', 'string'],
            'mail_class' => ['required', 'string' ], //TODO: validate mail_class
        ];

        $this->addTranslatableRules($rules);

        return $rules;
    }

    public function translatableRules(){
        return [
            'subject' => ['required', 'string'],
            'body' => ['required', 'string'],
            'sender_name' => ['required', 'string'],
            'sender_email' => ['required', 'email'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file'],
        ];
    }

    private function addTranslatableRules(array &$rules)
    {
        $locales = $this->getPossibleLocales();

        foreach ($locales as $locale){
            foreach ($this->translatableRules() as $key => $translatableRule){
                $localizedKey = $this->getLocalizedKey($key, $locale);
                $rules[$localizedKey] = $translatableRule;
            }
        }
    }

    private function getPossibleLocales()
    {
        $availableLocales = MailEditor::getLocales();

        //If only one locale available we dont have to loop through the possible locales
        if (count($availableLocales) === 1){
            return $availableLocales;
        }

        //We go through the sent data to see for which languages the form was filled out.
        // If we find at least one value for one of the locales we assume it is filled in
        // and that locale should be added to the rules.
        $filledLocales = [];

        foreach ($availableLocales as $locale){
            foreach ($this->translatableRules() as $key => $translatableRule){
                $localizedKey = $this->getLocalizedKey($key, $locale);
                $localizedValue = $this->get($localizedKey);

                //If we find a value that is filled in for this locale we look for values for the next locale.
                if (!empty($localizedValue)){
                    $filledLocales[$locale] = $locale;
                    continue 2;
                }
            }
        }

        //If no translatable fields are filled in the array would be empty,
        // we make sure atleast one locale gets validated
        if (empty($filledLocales)){
            return [$availableLocales[0]];
        }

        return $filledLocales;
    }

    private function getLocalizedKey($key, $locale)
    {
        return "{$key}___{$locale}";
    }

    public function validated()
    {
        $validated = parent::validated();

        foreach ($validated as $key => $value){
            [$keyWithoutLocale, $locale] = $this->removeLocaleFromKey($key);
            if ($locale){
                $validated[$keyWithoutLocale][$locale] = $value;
                unset($validated[$key]);
            }
        }

        return $validated;
    }

    private function removeLocaleFromKey($key)
    {
        $explodedKey = explode('___', $key);

        //check if we found a second variable
        if (isset($explodedKey[1])) {
            return $explodedKey;
        }

        //if not we add null as second variable as "locale"
        $explodedKey[] = null;

        return $explodedKey;
    }
}
