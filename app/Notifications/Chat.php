<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Chat extends Notification
{
    use Queueable;
    private $message;
    private $IdConversation;

/* Create a new notification instance.
*
* @return void
*/
    public function __construct($message,$IdConversation)
    {
        $this->message=$message;
        $this->IdConversation=$IdConversation;

    }

/* Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /* Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */

    /* Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $user=User::where('id',$this->message->user_id)->get()->first();
        return [
            'id'=>$this->IdConversation,
            'title'=>$user->name,
            'body'=>$this->message->body,
        ];
    }
}
