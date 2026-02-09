<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingPromoted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $listing;
    public $package;

    public function __construct($listing, $package)
    {
        $this->listing = $listing;
        $this->package = $package;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'listing_id' => $this->listing->id,
            'title' => 'Listing Promoted',
            'message' => "Your listing '{$this->listing->title}' has been promoted via {$this->package->name}.",
            'url' => route('listings.show', $this->listing->slug)
        ];
    }
}
