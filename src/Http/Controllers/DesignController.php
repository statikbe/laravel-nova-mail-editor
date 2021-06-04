<?php

namespace Statikbe\NovaMailEditor\Http\Controllers;

use Statikbe\LaravelMailEditor\MailTemplate;

class DesignController extends Controller
{
    public function index()
    {
        return config('mail-editor.designs');
    }
}
