<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use function Symfony\Component\String\width;

class RequestQuoteSupplierMail extends Mailable
{
    use Queueable, SerializesModels;

    public $supplier;
    public $product;
    /**
     * Create a new message instance.
     */
    public function __construct($supplier, $product)
    {
        $this->supplier = $supplier;
        $this->product = $product;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "【入札システム】見積依頼の通知",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.request_quote',
                with: [
                    'url' => config('app.frontend_url') .'/supplier/quotes/detail?id='. $this->product->id,
                    'supplier' => $this->supplier,
                    'product' => $this->product
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
