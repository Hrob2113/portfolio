<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactNotification;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $message = ContactMessage::query()->create($request->validated());

        $recipient = Setting::getValue('contact_email', 'hello@robinhrdlicka.dev');

        Mail::to($recipient)->queue(new ContactNotification($message));

        return response()->json(['message' => 'Message sent successfully.']);
    }
}
