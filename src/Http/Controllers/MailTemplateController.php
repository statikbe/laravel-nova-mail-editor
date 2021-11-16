<?php

namespace Statikbe\NovaMailEditor\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Statikbe\LaravelMailEditor\MailTemplate;
use Illuminate\Support\Facades\DB;
use Statikbe\NovaMailEditor\Http\Requests\DeleteMailTemplateRequest;
use Statikbe\NovaMailEditor\Http\Requests\UpdateMailTemplateRequest;

class MailTemplateController extends Controller
{
    public function index()
    {
        return MailTemplate::all()->toArray();
    }

    public function store(UpdateMailTemplateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['design'] = array_keys(config('mail-template-engine.designs'))[0];
        $data['render_engine'] = config('nova-mail-editor.render_engine') ?? 'html';

        DB::beginTransaction();
        try {
            $template = new MailTemplate;
            $template->name         = $data['name'];
            $template->design       = $data['design'];
            $template->render_engine= $data['render_engine'];
            $template->mail_class   = $data['mail_class'];
            $template->subject      = $data['subject'] ?? [];
            $template->body         = $data['body'] ?? [];
            $template->sender_name  = $data['sender_name'] ?? [];
            $template->sender_email = $data['sender_email'] ?? [];
            $template->recipients   = $data['recipients'] ?? [];
            $template->cc           = $data['cc'] ?? [];
            $template->bcc          = $data['bcc'] ?? [];
            $template->addAttachments($data['attachments'] ?? []);
            $template->save();

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return response()->json(['success' => true]);
    }

    public function edit($templateId)
    {
        return MailTemplate::query()->where('id', $templateId)->first();
    }

    public function update(UpdateMailTemplateRequest $request, MailTemplate $template): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $template->name         = $data['name'];
            $template->mail_class   = $data['mail_class'];
            $template->subject      = $data['subject'] ?? [];
            $template->body         = $data['body'] ?? [];
            $template->sender_name  = $data['sender_name'] ?? [];
            $template->sender_email = $data['sender_email'] ?? [];
            $template->recipients   = $data['recipients'] ?? [];
            $template->cc           = $data['cc'] ?? [];
            $template->bcc          = $data['bcc'] ?? [];
            $template->addAttachments($data['attachments'] ?? []);
            $template->save();

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return response()->json(['success' => true]);
    }

    public function delete(DeleteMailTemplateRequest $request, MailTemplate $template): JsonResponse
    {
        $template->delete();

        return response()->json(['success' => true]);
    }
}
