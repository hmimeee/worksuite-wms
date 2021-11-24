<?php

namespace Modules\Article\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Article\Entities\WriterLeave;
use App\User;

class LeaveGranted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $leave;
    private $user;

    public function __construct(WriterLeave $leave)
    {
        $this->leave = $leave;
        $this->user = user();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $this->url = route('member.article.leaves').'?application='.$this->leave->id;
        $this->writer = User::find($this->leave->user_id);
        $this->headmessage = "Leave application granted";
        $this->bodymessage = "granted your leave application.";
        return (new MailMessage)
        ->subject($this->headmessage . ' - ' . config('app.name'))
        ->from(config('mail.from.address'), $this->user->name .' via '. config('app.name'))
        ->markdown('article::mail.leave', ['leave' => $this->leave, 'url' => $this->url, 'writer' => $this->writer, 'headmessage' => $this->headmessage, 'bodymessage' => $this->bodymessage, 'user' => $this->user]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
