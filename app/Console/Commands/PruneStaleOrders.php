<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class PruneStaleOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-stale-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '30분 이상 된 pending 주문 자동 삭제';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Order::where('status', 'pending')->where('created_at', '<', now()->subMinutes(30))->delete();
    }
}
