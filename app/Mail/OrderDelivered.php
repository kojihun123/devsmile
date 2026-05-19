<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class OrderDelivered extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[DEVSMILE] 배달이 완료되었습니다 (#' . $this->order->id . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-delivered',
        );
    }

    public function attachments(): array
    {
        return $this->order->items()
            ->with('product')
            ->get()
            ->filter(fn($item) => $item->product?->delivery_image)
            ->map(fn($item) => Attachment::fromStorageDisk('public', $item->product->delivery_image)
                ->as($item->product_name . '.' . pathinfo($item->product->delivery_image, PATHINFO_EXTENSION)))
            ->values()
            ->all();
    }
}
