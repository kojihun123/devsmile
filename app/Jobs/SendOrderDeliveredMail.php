<?php

namespace App\Jobs;

use App\Mail\OrderDelivered;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderDeliveredMail implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        $order = $this->order->fresh();

        if ($order->status !== 'shipping') {
            return;
        }

        $order->update(['status' => 'delivered']);

        Mail::to($order->guest_email)->send(new OrderDelivered($order));
    }
}
