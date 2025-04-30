<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ContentDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $contentType;
    private $reportId;

    public function __construct($contentType, $reportId)
    {
        $this->contentType = $contentType;
        $this->reportId = $reportId;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'content_deleted',
            'content_type' => $this->contentType,
            'report_id' => $this->reportId
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->id ?? uniqid(),
            'type' => static::class,
            'content_type' => $this->contentType,
            'report_id' => $this->reportId
        ]);
    }
}
