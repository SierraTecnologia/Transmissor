<?php

namespace Transmissor\Notifications\Organizer;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Zenvia\ZenviaChannel;
use NotificationChannels\Zenvia\ZenviaMessage;
use Illuminate\Support\Facades\Log;
use Transmissor\Channels\SmsChannel;
use Transmissor\Channels\Messages\SmsMessage;

class ServiceDied extends Notification implements ShouldQueue
{
    use Queueable;

    protected $_business = null;

    /**
     * Create a new notification instance.
     *
     * @param $businessToken
     * @return void
     */
    public function __construct($business)
    {
        $this->_business = $business;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toSms($notifiable)
    {
        $number = \App\Sitec\Filter::phone($notifiable->telefone_celular);

        $nomeProdutora = '';
        if (!empty($this->_business) && isset($this->_business->nome)){
            $nomeProdutora = $this->_business->nome;
        }

        $message = $notifiable->token_sms ." Token SMS ". $nomeProdutora .". Insira o token para validar sua conta.";
        Log::debug(
            '[Notification] Esqueci minha Senha > Mensagem: '.$message.
            ' Numero: '.$number
        );

        return SmsMessage::create()
            ->from('Passepague') // optional
            ->to($number) // your user phone
            ->content($message);
            // ->id('your-sms-id');
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