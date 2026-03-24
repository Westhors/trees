<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $template;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($template, $subject)
    {
        $this->template = $template;
        $this->subject = $subject;
    }

    public function build(): ContactUsRequestMail
    {
        return $this->markdown('mail.ContactUsRequestMail')
            ->subject($this->subject)  // Use $this->subject here
            ->with(['template' => $this->template]);
    }
}
