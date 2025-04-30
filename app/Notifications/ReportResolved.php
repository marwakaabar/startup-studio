<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReportResolved extends Notification
{
    use Queueable;

    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre signalement a été traité')
            ->line('Le signalement que vous avez soumis a été traité par nos modérateurs.')
            ->line('Merci de contribuer à maintenir la qualité de notre communauté.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Votre signalement a été traité',
            'report_id' => $this->report->id
        ];
    }
}
