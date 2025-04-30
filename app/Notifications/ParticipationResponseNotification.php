<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\ForumParticipation;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Broadcast;

class ParticipationResponseNotification extends Notification
{
    use Queueable;

    public $participation;

    public function __construct(ForumParticipation $participation)
    {
        $this->participation = $participation;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'participation_response',
            'forum_id' => $this->participation->forum_id,
            'forum_name' => $this->participation->forum->name,
            'status' => $this->participation->status,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => 'participation_response',
            'forum_id' => $this->participation->forum_id,
            'forum_name' => $this->participation->forum->name,
            'status' => $this->participation->status,
        ]);
    }
}
