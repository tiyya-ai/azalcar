<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSellerApplication extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Seller Application Pending Review')
            ->greeting('Hello Admin,')
            ->line('A new user has applied to become a seller and requires approval.')
            ->line('**User:** ' . $this->user->name)
            ->line('**Email:** ' . $this->user->email)
            ->line('**Phone:** ' . ($this->user->phone ?? 'Not provided'))
            ->action('Review Application', route('admin.users.index'))
            ->line('Please review the application and approve or reject accordingly.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'title' => 'New Seller Application',
            'message' => $this->user->name . ' has applied to become a seller and needs approval.',
            'url' => route('admin.users.index'),
        ];
    }
}