<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewTopicActivityNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $type;
    public $topic;
    public $content;
    public $author;

    public function __construct($type, $topic, $content, $author)
    {
        $this->type = $type;
        $this->topic = $topic;
        $this->content = $content;
        $this->author = $author;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'topic_activity',
            'activity_type' => $this->type,
            'topic_id' => $this->topic->id,
            'topic_title' => $this->topic->title,
            'forum_name' => $this->topic->forum->name,
            'author_name' => $this->author->name,
            'content_preview' => substr(strip_tags($this->content), 0, 100)
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => uniqid('notification_'),
            'type' => get_class($this),
            'activity_type' => $this->type,
            'topic_id' => $this->topic->id,
            'topic_title' => $this->topic->title,
            'forum_name' => $this->topic->forum->name,
            'author_name' => $this->author->name,
            'content_preview' => substr(strip_tags($this->content), 0, 100),
            'created_at' => now()->toISOString(),
            'data' => $this->toArray($notifiable)
        ]);
    }
}
