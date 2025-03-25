<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use function Symfony\Component\String\width;

class ResponseToQuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $supplier;
    public $product;
    public $quote;
    /**
     * Create a new message instance.
     */
    public function __construct($supplier, $product, $quote)
    {
        $this->supplier = $supplier;
        $this->product = $product;
        $this->quote = $quote;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = '';
        switch ($this->quote->is_accepted) {
            case config('const.accept_status.accepted'):
                $subject = "【入札システム】採用結果の通知";
                break;
            case config('const.accept_status.rejected'):
                $subject = "【入札システム】非採用結果の通知";
                break;
            case config('const.accept_status.returned'):
                $subject = "【入札システム】差し戻しの通知";
                break;
            default:
                $subject = '';
                break;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = '';
        switch ($this->quote->is_accepted) {
            case config('const.accept_status.accepted'):
                $view = "mail.response_quote_accepted";
                break;
            case config('const.accept_status.rejected'):
                $view = "mail.response_quote_rejected";
                break;
            case config('const.accept_status.returned'):
                $view = "mail.response_quote_returned";
                break;
        }
        return new Content(
            markdown: $view,
                with: [
                    'url' => config('app.frontend_url') .'/supplier/quotes/detail?id='. $this->product->id,
                    'supplier' => $this->supplier,
                    'product' => $this->product,
                    'quote' => $this->quote
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
