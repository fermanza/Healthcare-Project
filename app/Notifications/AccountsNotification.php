<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AccountsNotification extends Notification
{
    use Queueable;

    protected $timestamp;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->from('emttgda@treehousetechgroup.com', 'Envision Reporting')
                    ->subject('Accounts Bulk Export')
                    ->line('Please download the file from the link below.')
                    ->line(route('admin.accounts.download', ["timestamp" => $this->timestamp]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
