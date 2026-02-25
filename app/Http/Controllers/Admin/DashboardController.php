<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class DashboardController extends Controller
{
    public function index()
    {
        $unreadCount = ContactMessage::query()->unread()->count();
        $recentMessages = ContactMessage::query()->latest()->limit(5)->get();

        return view('admin.dashboard', compact('unreadCount', 'recentMessages'));
    }
}
