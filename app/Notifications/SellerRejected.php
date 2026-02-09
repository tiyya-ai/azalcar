<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerRejected extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
            ->subject('Your Seller Application Has Been Rejected - Azal Cars')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('After review, your seller application on Azal Cars has been rejected.')
            ->line('This may be due to incomplete information or other requirements not being met.')
            ->action('Contact Support', route('support'))
            ->line('If you have questions or would like to reapply, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Seller Application Rejected',
            'message' => 'Your seller application has been rejected. Please contact support for more information.',
            'url' => route('support'),
        ];
    }
}
