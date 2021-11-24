<?php

namespace Modules\Article\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Article\Entities\Invoice;
use App\EmailNotificationSetting;
use App\User;

class InvoicePaid extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $invoice;
    private $user;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->emailSetting = EmailNotificationSetting::where('slug', 'user-assign-to-task')->first();
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
        $this->url = route('member.article.invoices').'?view-invoice='.$this->invoice->id;
        $this->paymentTo = User::find($this->invoice->paid_to);
        $this->creator = $this->user;
        $this->headmessage = "Payslip status paid";
        $this->bodymessage = "updated an payslip status to paid.";
        return (new MailMessage)
        ->subject($this->headmessage . ' ' . $this->invoice->name . ' - ' . config('app.name') . '!')
        ->from(config('mail.from.address'), $this->user->name .' via '. config('app.name'))
        ->markdown('article::mail.templateInvoice', ['invoice' => $this->invoice, 'url' => $this->url, 'paymentTo' => $this->paymentTo, 'creator' => $this->creator, 'headmessage' => $this->headmessage, 'bodymessage' => $this->bodymessage, 'user' => $this->user]);

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
