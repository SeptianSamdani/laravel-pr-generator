<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PrStatusChanged extends Notification
{
    use Queueable; // optional tapi recommended
    
    protected $pr;
    protected $status;
    
    public function __construct($pr, $status)
    {
        $this->pr = $pr;
        $this->status = $status;
    }
    
    public function via($notifiable)
    {
        return ['database', 'mail']; 
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("PR {$this->pr->pr_number} - Status {$this->status}")
            ->line("PR Anda telah {$this->status}.")
            ->action('Lihat Detail PR', route('pr.show', $this->pr->id));
    }
    
    public function toArray($notifiable)
    {
        return [
            'pr_id' => $this->pr->id,
            'pr_number' => $this->pr->pr_number,
            'status' => $this->status,
            'message' => "PR {$this->pr->pr_number} telah {$this->status}",
        ];
    }
}