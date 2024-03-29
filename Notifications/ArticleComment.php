<?php

namespace Modules\Article\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleType;
use Modules\Article\Entities\ArticleComment as Comment;
use App\EmailNotificationSetting;
use App\User;

class ArticleComment extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $comment;
    private $user;
    private $article;

    public function __construct(Comment $comment)
    {
        $this->article = Article::find($comment->article_id);
        $this->comment = $comment;
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
        $this->url = route('member.article.index').'?view-article='.$this->article->id;
        $this->assignee = User::find($this->article->assignee);
        $this->creator = User::find($this->article->creator);
        $this->headmessage = "New comment added";
        $this->bodymessage = "added a comment on the assigned article page.";
        return (new MailMessage)
        ->subject($this->headmessage . ' #' . $this->article->id . ' - ' . config('app.name') . '!')
        ->from(config('mail.from.address'), $this->user->name .' via '. config('app.name'))
        ->markdown('article::mail.template', ['article' => $this->article, 'url' => $this->url, 'assignee' => $this->assignee, 'creator' => $this->creator, 'comment' => $this->comment, 'headmessage' => $this->headmessage, 'bodymessage' => $this->bodymessage, 'user' => $this->user]);

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
