<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $code;
    public $username;
    
    public function __construct($code, $username)
    {
        $this->code = $code;
        $this->username = $username;
    }
    
    public function build()
    {
        return $this->subject('Código de recuperación de contraseña')
                    ->view('emails.password-reset');
    }
}