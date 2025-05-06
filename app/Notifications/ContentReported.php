<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ContentReported extends Notification implements ShouldQueue
{
    use Queueable;

    private $reportedBy;
    private $contentType;
    private $reportReason;
    private $contentId;

    public function __construct($reportedBy, $contentType, $reportReason, $contentId)
    {
        $this->reportedBy = $reportedBy;
        $this->contentType = $contentType;
        $this->reportReason = $reportReason;
        $this->contentId = $contentId;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'content_reported',
            'reported_by' => $this->reportedBy,
            'content_type' => $this->contentType,
            'reason' => $this->reportReason,
            'content_id' => $this->contentId
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->id ?? uniqid(),
            'type' => static::class,
            'data' => [
                'type' => 'content_reported',
                'reported_by' => $this->reportedBy,
                'content_type' => $this->contentType,
                'reason' => $this->reportReason,
                'content_id' => $this->contentId
            ]
        ]);
    }
}
