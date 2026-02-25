<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class MessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::query()->latest()->paginate(20);

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        return view('admin.messages.show', compact('message'));
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()
            ->route('admin.messages.index')
            ->with('success', 'Message deleted.');
    }

    public function markAsRead(ContactMessage $message)
    {
        $message->update(['is_read' => true]);

        return redirect()
            ->route('admin.messages.show', $message)
            ->with('success', 'Message marked as read.');
    }
}
