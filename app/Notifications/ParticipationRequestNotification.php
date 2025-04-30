<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Broadcast;

class ParticipationRequestNotification extends Notification
{
    use Queueable;

    public $forum;
    public $requester;

    public function __construct(Forum $forum, User $requester)
    {
        $this->forum = $forum;
        $this->requester = $requester;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'participation_request',
            'forum_id' => $this->forum->id,
            'forum_name' => $this->forum->name,
            'requester_id' => $this->requester->id,
            'requester_name' => $this->requester->name,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => 'participation_request',
            'forum_id' => $this->forum->id,
            'forum_name' => $this->forum->name,
            'requester_id' => $this->requester->id,
            'requester_name' => $this->requester->name,
        ]);
    }
}
