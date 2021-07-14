<?php

namespace Statikbe\NovaMailEditor\Http\Controllers;

use Statikbe\LaravelMailEditor\MailTemplate;

class TemplateController extends Controller
{
    public function index(){
        $templateArray = [];
        $templates = config('mail-template-engine.templates');

        foreach($templates as $templateKey => $template){
            $templateArray[$templateKey] = $template::name();
        }

        return $templateArray;
    }

    public function variables($templateKey){
        $template = config('mail-template-engine.templates')[$templateKey] ?? null;
        if (!$template){
            abort(404);
        }
        $contentVariables = $template::getContentVariables();
        $recipientVariables = $template::getRecipientVariables();

        return [
            'content' => $contentVariables,
            'recipients' => $recipientVariables,
        ];
    }
}
