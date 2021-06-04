<?php

namespace Statikbe\NovaMailEditor\Http\Controllers;

use Statikbe\LaravelMailEditor\MailTemplate;

class LocaleController extends Controller
{
    public function __invoke()
    {
        return config('translatable.locales');
    }
}
