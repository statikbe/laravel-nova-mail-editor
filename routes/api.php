<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Statikbe\NovaMailEditor\Http\Controllers\DesignController;
use Statikbe\NovaMailEditor\Http\Controllers\LocaleController;
use Statikbe\NovaMailEditor\Http\Controllers\MailTemplateController;
use Statikbe\NovaMailEditor\Http\Controllers\TemplateController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/mail-templates', [MailTemplateController::class, 'index']);
Route::post('/mail-templates/store', [MailTemplateController::class, 'store']);
Route::get('/mail-templates/{mail_template}/edit', [MailTemplateController::class, 'edit']);
Route::post('/mail-templates/{mail_template}/update', [MailTemplateController::class, 'update']);

Route::get('/locales', LocaleController::class);

Route::get('/templates', [TemplateController::class, 'index']);
Route::get('/templates/{template}/variables', [TemplateController::class, 'variables']);

Route::get('/designs', [DesignController::class, 'index']);


