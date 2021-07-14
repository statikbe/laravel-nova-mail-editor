<?php

namespace Statikbe\NovaMailEditor\Http\Controllers;

use Statikbe\LaravelMailEditor\MailTemplate;
use Statikbe\NovaMailEditor\MailEditor;

class LocaleController extends Controller
{
    public function __invoke()
    {
        return MailEditor::getLocales();
    }
}
