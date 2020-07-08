<?php

namespace Modules\Article\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Article\Entities\WriterRate as Rate;
use Modules\Article\Entities\Writer;
use App\EmailNotificationSetting;
use App\User;

class WriterRate extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $writer;
    public function __construct($old_rate)
    {
        $this->old_rate = $old_rate;
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
        $this->writer = $notifiable;
        $this->new_rate = Rate::where('user_id', $this->writer->id)->first()->rate;
        $this->headmessage = "Writer rate updated";
        $this->bodymessage = "updated your article writing rate.";
        $this->main_message = "Congratulations, your article writing rate has been chaged from $this->old_rate to $this->new_rate per 1000 words. Keep up the good work!";
        return (new MailMessage)
        ->subject($this->headmessage . ' - ' . config('app.name') . '!')
        ->from(config('mail.from.address'), auth()->user()->name .' via '. config('app.name'))
        ->markdown('article::mail.templateWriterRate', ['writer' => $this->writer, 'url' => $this->url, 'main_message' => $this->main_message, 'headmessage' => $this->headmessage, 'bodymessage' => $this->bodymessage, 'user' => auth()->user()]);

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
