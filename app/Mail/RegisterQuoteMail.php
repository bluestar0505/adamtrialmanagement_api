<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use function Symfony\Component\String\width;

class RegisterQuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $supplier;
    public $product;
    public $quote;
    public $buyer;
    /**
     * Create a new message instance.
     */
    public function __construct($supplier, $product, $quote, $buyer)
    {
        $this->supplier = $supplier;
        $this->product = $product;
        $this->quote = $quote;
        $this->buyer = $buyer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "【入札システム】見積回答登録の通知",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.register_quote',
                with: [
                    'url' => config('app.frontend_url') ."/buyer/quotes/detail?request={$this->product->id}&id={$this->quote->id}" ,
                    'supplier' => $this->supplier,
                    'product' => $this->product,
                    'buyer' => $this->buyer,
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
