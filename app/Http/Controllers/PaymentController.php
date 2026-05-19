<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderConfirmedMail;
use App\Jobs\SendOrderDeliveredMail;
use App\Jobs\SendOrderShippingMail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        return view('payments.success');
    }

    public function confirm(Request $request)
    {
        $paymentKey = $request->input('paymentKey');
        $orderId    = $request->input('orderId');
        $amount     = $request->input('amount');

        preg_match('/^order-(\d+)/', $orderId, $matches);
        $order = Order::with('items')->findOrFail($matches[1]);

        // 금액 변조 검증
        if ((int) $amount !== $order->total_amount) {
            return response()->json(['message' => '결제 금액이 일치하지 않습니다.', 'code' => 'AMOUNT_MISMATCH'], 400);
        }

        // 토스 승인 API 호출
        $response = Http::withBasicAuth(config('services.toss.secret_key'), '')
            ->post('https://api.tosspayments.com/v1/payments/confirm', [
                'paymentKey' => $paymentKey,
                'orderId'    => $orderId,
                'amount'     => $amount,
            ]);

        if (!$response->successful()) {
            return response()->json($response->json(), $response->status());
        }

        // 주문 상태 업데이트 + 재고 차감 + 결제 저장
        try {
            DB::transaction(function () use ($order, $paymentKey, $amount) {
                // 레이스 컨디션 방지: 트랜잭션 안에서 lock 후 상태 재확인
                $lockedOrder = Order::lockForUpdate()->find($order->id);
                if ($lockedOrder->status !== 'pending') {
                    return;
                }

                foreach ($order->items as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);

                    if ($product->stock < $item->quantity) {
                        throw new \RuntimeException("'{$product->name}' 의 재고가 부족하여 결제를 취소합니다.");
                    }

                    $product->decrement('stock', $item->quantity);
                }

                $lockedOrder->update(['status' => 'paid']);

                $lockedOrder->payment()->create([
                    'payment_key' => $paymentKey,
                    'amount'      => $amount,
                    'status'      => 'done',
                    'paid_at'     => now(),
                ]);
            });
        
            SendOrderConfirmedMail::dispatch($order);
            SendOrderShippingMail::dispatch($order)->delay(now()->addMinutes(5));
            SendOrderDeliveredMail::dispatch($order)->delay(now()->addMinutes(10));

        } catch (\RuntimeException $e) {
            Http::withBasicAuth(config('services.toss.secret_key'), '')
                ->post("https://api.tosspayments.com/v1/payments/{$paymentKey}/cancel", [
                    'cancelReason' => $e->getMessage(),
                ]);

            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['success' => true]);
    }

    public function fail(Request $request)
    {
        $message = $request->query('message', '결제에 실패했습니다.');
        $code    = $request->query('code', '');

        return view('payments.fail', compact('message', 'code'));
    }
}
