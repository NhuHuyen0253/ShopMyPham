<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new class($token) extends ResetPasswordNotification {
            public function toMail($notifiable)
            {
                $url = route('admin.password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]);

                return (new MailMessage)
                    ->subject('Đặt lại mật khẩu Admin')
                    ->line('Bạn đã yêu cầu đặt lại mật khẩu tài khoản quản trị.')
                    ->action('Đặt lại mật khẩu', $url)
                    ->line('Nếu không phải bạn yêu cầu, hãy bỏ qua email này.');
            }
        });
    }
}