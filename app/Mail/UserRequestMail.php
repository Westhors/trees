<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $template;
    public $subject;
    /**$subject
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template, $subject)
    {
        $this->template = $template;
        $this->subject = $subject;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.userRequestMailTwo')
            ->subject($this->subject)
            ->with(['template' => $this->template]);
    }
}
