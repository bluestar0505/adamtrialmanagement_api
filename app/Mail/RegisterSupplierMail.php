<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use function Symfony\Component\String\width;

class RegisterSupplierMail extends Mailable
{
    use Queueable, SerializesModels;

    public $supplier;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct($supplier, $password)
    {
        $this->supplier = $supplier;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "【入札システム】サプライヤー登録の通知",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.register_supplier',
                with: [
                    'url' => config('app.frontend_url') ."/supplier/login" ,
                    'supplier' => $this->supplier,
                    'password' => $this->password
                ]

        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
