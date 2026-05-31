<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public ?string $name;

    public function __construct(string $code, ?string $name = null)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Código de recuperación | Lorent Inmobiliaria')
                    ->view('emails.password_reset');
    }
}
