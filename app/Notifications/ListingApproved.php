<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Listing;

class ListingApproved extends Notification implements ShouldQueue
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
            ->subject('Your listing has been approved!')
            ->greeting('Great news!')
            ->line('Your listing "' . $this->listing->title . '" has been approved and is now live on our platform.')
            ->action('View Listing', url('/listing/' . $this->listing->slug))
            ->line('Thank you for using our platform!')
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
            'type' => 'listing_approved',
            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'message' => 'Your listing "' . $this->listing->title . '" has been approved and is now live.',
            'url' => '/listing/' . $this->listing->slug,
        ];
    }
}