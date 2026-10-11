<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PortalInvitationNotification extends Notification
{
    public function __construct(public string $url, public string $schoolName, public string $type) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->schoolName.' — '.($this->type === 'guardian' ? 'Parent invitation' : 'Student invitation'))
            ->line($this->schoolName.' has invited you to set up your '.($this->type === 'guardian' ? 'parent' : 'student').' portal.')
            ->action('Set up your account', $this->url)
            ->line('This invitation expires in 7 days and can be accepted once. Set your own password; do not share another family member’s login.');
    }
}
