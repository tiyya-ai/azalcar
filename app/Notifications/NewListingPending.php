<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewListingPending extends Notification implements ShouldQueue
{
    use Queueable;

    public $listing;

    /**
     * Create a new notification instance.
     */
    public function __construct($listing)
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
            ->subject('New Listing Pending Approval - Azal Cars')
            ->greeting('Hello Admin,')
            ->line('A new listing has been submitted on Azal Cars and requires your approval.')
            ->line('**Listing:** ' . $this->listing->title)
            ->line('**User:** ' . $this->listing->user->name . ' (' . $this->listing->user->email . ')')
            ->line('**Price:** ₩' . number_format($this->listing->price))
            ->action('Review Listing', route('admin.listings.index'))
            ->line('Please review and approve or reject this listing.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'listing_id' => $this->listing->id,
            'title' => 'New Listing Pending Approval',
            'message' => 'A new listing "' . $this->listing->title . '" by ' . $this->listing->user->name . ' requires approval.',
            'url' => route('admin.listings.index'),
        ];
    }
}