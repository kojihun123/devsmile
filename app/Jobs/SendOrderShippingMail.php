<?php

namespace App\Jobs;

use App\Mail\OrderShipping;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderShippingMail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order->fresh();

        if ($order->status !== 'paid') {
            return;
        }

        $order->update(['status' => 'shipping']);

        Mail::to($order->guest_email)->send(new OrderShipping($order));
    }
}
