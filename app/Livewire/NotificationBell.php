<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $dropdownOpen = false;

    protected $listeners = ['notificationReceived' => '$refresh'];

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'unreadCount' => Auth::user()->unreadNotifications()->count(),
            'notifications' => Auth::user()->notifications()->take(10)->get(),
        ]);
    }
}
