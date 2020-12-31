<?php

namespace Modules\Article\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Article\Entities\WriterRate;
use Modules\Article\Entities\Writer;
use App\EmailNotificationSetting;
use App\User;

class WriterUnavailability extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $writer;
    public function __construct($writer)
    {
        $this->writer = Writer::find($writer);
        $this->emailSetting = EmailNotificationSetting::where('slug', 'user-assign-to-task')->first();
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
        $this->url = route('member.article.writers');
        $this->new_rate = WriterRate::where('user_id', $this->writer->id)->first()->rate;
        $this->headmessage = "Writer rate updated";
        $this->bodymessage = "updated article writing rate of an user.";
        return (new MailMessage)
        ->subject($this->headmessage . ' - ' . config('app.name') . '!')
        ->from(config('mail.from.address'), auth()->user()->name .' via '. config('app.name'))
        ->markdown('article::mail.templateWriterRateAdmin', ['writer' => $this->writer, 'url' => $this->url, 'new_rate' => $this->new_rate, 'old_rate' => $this->old_rate, 'headmessage' => $this->headmessage, 'bodymessage' => $this->bodymessage, 'user' => auth()->user()]);

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
