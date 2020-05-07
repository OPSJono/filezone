<?php

namespace App\Notifications;

use DateTimeInterface;
use http\Exception\InvalidArgumentException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Laravel\Passport\HasApiTokens;

class PasswordReset extends BaseNotification
{

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->passwordResetUrl($notifiable);

        return (new MailMessage)
            ->subject(Lang::get('Password Reset Request'))
            ->line(Lang::get('Please click the button below to reset your password.'))
            ->action(Lang::get('Reset Password'), $verificationUrl)
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }

    /**
     * Get the password reset URL for the given user.
     *
     * @param  Notifiable|HasApiTokens|MustVerifyEmail|Model $notifiable
     * @return string
     */
    protected function passwordResetUrl($notifiable)
    {
        $apiUrl = $this->temporarySignedRoute(
            'password.reset',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Allow any front end to handle the password reset form.
        $feUrl = $notifiable->getPasswordResetUrl();
        if(!empty($feUrl)) {
            $apiUrl = str_replace(route('password.reset'), $feUrl, $apiUrl);
        }

        return $apiUrl;
    }


}
