<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContentAuthorNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $contentType;
    public $isFirstWarning;

    public function __construct($contentType, $isFirstWarning)
    {
        $this->contentType = $contentType;
        $this->isFirstWarning = $isFirstWarning;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        $warningType = $this->isFirstWarning ? 'Premier avertissement' : 'Dernier avertissement';
        
        return (new MailMessage)
            ->subject('⚠️ Contenu supprimé - ' . $warningType)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('<div style="background-color: #FEF2F2; border-left: 4px solid #DC2626; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <strong style="color: #DC2626;">Important :</strong> Votre ' . strtolower($this->contentType) . ' a été supprimé suite à un signalement.
                   </div>')
            ->line('<div style="background-color: #F3F4F6; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                    <strong>Raison :</strong> Violation des règles de la communauté
                   </div>')
            ->line('<div style="margin-bottom: 20px;">' . 
                   ($this->isFirstWarning ? 
                    'Ceci est votre <strong>premier avertissement</strong>. Veuillez noter que si cette situation se reproduit, des mesures plus sévères pourraient être prises, pouvant aller jusqu\'à la désactivation de votre compte.' :
                    '<strong style="color: #DC2626;">⚠️ Attention : Ceci est votre dernier avertissement.</strong><br>Une prochaine violation entraînera la désactivation immédiate de votre compte.') .
                   '</div>')
            ->line('<div style="background-color: #EFF6FF; border-left: 4px solid #3B82F6; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <strong>Rappel des règles de la communauté :</strong><br>
                    • Respectez les autres membres<br>
                    • Évitez tout contenu inapproprié ou offensant<br>
                    • Contribuez de manière constructive aux discussions
                   </div>')
            ->action('Consulter les règles de la communauté', url('/rules'))
            ->line('<div style="font-size: 0.9em; color: #6B7280; margin-top: 20px;">
                    Si vous pensez que cette décision est une erreur, vous pouvez contacter notre équipe de support.
                   </div>')
            ->salutation('L\'équipe de modération')
            ->markdown('vendor.notifications.email');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'content_author_warning',
            'content_type' => $this->contentType,
            'is_first_warning' => $this->isFirstWarning,
            'message' => $this->isFirstWarning ? 
                'Votre contenu a été supprimé (Premier avertissement)' : 
                'Votre contenu a été supprimé (Dernier avertissement)'
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->id ?? uniqid(),
            'type' => static::class,
            'data' => [
                'type' => 'content_author_warning',
                'content_type' => $this->contentType,
                'is_first_warning' => $this->isFirstWarning,
                'message' => $this->isFirstWarning ? 
                    'Votre contenu a été supprimé (Premier avertissement)' : 
                    'Votre contenu a été supprimé (Dernier avertissement)'
            ]
        ]);
    }
}
