<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
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
            ->subject('Reservation Confirmed: ' . $this->reservation->listing->title . ' - Azal Cars')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your reservation has been successfully created on Azal Cars.')
            ->line('**Car:** ' . $this->reservation->listing->title)
            ->line('**Deposit Paid:** ' . \App\Helpers\Helpers::formatPrice($this->reservation->deposit_amount))
            ->line('**Expires At:** ' . $this->reservation->expires_at->format('M d, Y H:i'))
            ->action('View Reservation', route('reservations.show', $this->reservation->id))
            ->line('Thank you for using Azal Cars!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'listing_id' => $this->reservation->listing_id,
            'title' => 'Reservation Confirmed',
            'message' => 'You have successfully reserved ' . $this->reservation->listing->title,
            'amount' => $this->reservation->deposit_amount,
        ];
    }
}
