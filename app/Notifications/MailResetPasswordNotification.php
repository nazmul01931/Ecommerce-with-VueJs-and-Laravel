<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;

class MailResetPasswordNotification extends ResetPassword
{
    use Queueable;

    public function __construct($token)
    {
        parent::__construct($token);
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $link = route('new_password_buyer', ['token' => $this->token]);
       
        return (new MailMessage )
            ->subject( 'Reset Password Notification' )
            ->line( "Hello! You are receiving this email because we received a password reset request for your account." )
            ->action( 'Reset Password', $link )
            ->line( "This password reset link will expire in ".config('auth.passwords.users.expire')." minutes" )
            ->line( "If you did not request a password reset, no further action is required." );    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}