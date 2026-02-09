<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerApproved extends Notification implements ShouldQueue
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
            ->subject('Your Seller Application Has Been Approved - Azal Cars')
            ->greeting('Congratulations ' . $notifiable->name . '!')
            ->line('Your application to become a seller on Azal Cars has been approved.')
            ->line('You can now create and manage listings on our platform.')
            ->action('Start Selling', route('listings.create'))
            ->line('Welcome to the Azal Cars seller community!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Seller Application Approved',
            'message' => 'Congratulations! Your seller application has been approved. You can now create listings.',
            'url' => route('listings.create'),
        ];
    }
}
