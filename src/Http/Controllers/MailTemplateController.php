<?php

namespace Statikbe\NovaMailEditor\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Statikbe\LaravelMailEditor\MailTemplate;
use Statikbe\NovaMailEditor\Http\Requests\DeleteMailTemplateRequest;
use Statikbe\NovaMailEditor\Http\Requests\UpdateMailTemplateRequest;

class MailTemplateController extends Controller
{
    public function index(){
        return MailTemplate::all()->toArray();
    }

    public function store(UpdateMailTemplateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['design'] = array_keys(config('mail-template-engine.designs'))[0];
        $data['render_engine'] = config('nova-mail-editor.render_engine') ?? 'html';

        $mailTemplate = MailTemplate::create($data);

        return response()->json(['success' => true]);
    }

    public function edit($templateId){
        return MailTemplate::query()->where('id', $templateId)->first();
    }

    public function update(UpdateMailTemplateRequest $request, MailTemplate $template): JsonResponse
    {
        $data = $request->validated();

        $template->update($data);

        return response()->json(['success' => true]);
    }

    public function delete(DeleteMailTemplateRequest $request, MailTemplate $template): JsonResponse
    {
        $template->delete();

        return response()->json(['success' => true]);
    }
}
