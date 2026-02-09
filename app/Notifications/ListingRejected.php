<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Listing;

class ListingRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $listing;

    /**
     * Create a new notification instance.
     */
    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
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
            ->subject('Your listing was not approved')
            ->greeting('We regret to inform you')
            ->line('Your listing "' . $this->listing->title . '" could not be approved at this time.')
            ->line('Please review our listing guidelines and try again.')
            ->action('Create New Listing', url('/post-ad'))
            ->line('If you have any questions, please contact our support team.')
            ->salutation('Best regards, AutoClassified Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'listing_rejected',
            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'message' => 'Your listing "' . $this->listing->title . '" was not approved.',
            'url' => '/post-ad',
        ];
    }
}