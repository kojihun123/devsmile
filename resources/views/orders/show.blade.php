<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-2xl font-bold text-gray-900 mb-8">주문 상세</h1>

        {{-- 배송 정보 --}}
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm mb-4">
            <h2 class="text-sm font-semibold text-gray-900 mb-3">배송 정보</h2>
            <div class="space-y-1 text-sm text-gray-600">
                <p>이름: {{ $order->guest_name }}</p>
                <p>이메일: {{ $order->guest_email }}</p>
                <p>전화번호: {{ $order->guest_phone }}</p>
            </div>
        </div>

        {{-- 주문 상품 --}}
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm mb-4">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">주문 상품</h2>
            <div class="space-y-3">
                @foreach ($order->items as $item)
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                            <p class="text-gray-400">{{ number_format($item->price) }}원 × {{ $item->quantity }}개</p>
                        </div>
                        <span class="font-semibold text-gray-900">
                            {{ number_format($item->price * $item->quantity) }}원
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between items-center">
                <span class="text-sm text-gray-600">총 결제 금액</span>
                <span class="text-lg font-bold text-gray-900">{{ number_format($order->total_amount) }}원</span>
            </div>
        </div>

        {{-- 토스 결제 위젯 --}}
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm mb-4">
            <div id="payment-method"></div>
            <div id="agreement"></div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs px-4 py-3 rounded-lg mb-3 text-center">
            ⚠️ 포트폴리오 데모입니다. 테스트 결제 환경으로 실제 금액이 청구되지 않습니다.
        </div>

        <button id="payment-button"
                class="w-full bg-gray-900 text-white py-3 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
            결제하기
        </button>

        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-gray-600">
                쇼핑 계속하기
            </a>
        </div>

    </div>

    <script src="https://js.tosspayments.com/v2/standard"></script>
    <script>
        main();

        async function main() {
            const clientKey = "{{ config('services.toss.client_key') }}";
            const tossPayments = TossPayments(clientKey);

            @auth
                const widgets = tossPayments.widgets({ customerKey: "user-{{ Auth::id() }}" });
            @else
                const widgets = tossPayments.widgets({ customerKey: TossPayments.ANONYMOUS });
            @endauth

            await widgets.setAmount({
                currency: "KRW",
                value: {{ $order->total_amount }},
            });

            await Promise.all([
                widgets.renderPaymentMethods({ selector: "#payment-method", variantKey: "DEFAULT" }),
                widgets.renderAgreement({ selector: "#agreement", variantKey: "AGREEMENT" }),
            ]);

            document.getElementById("payment-button").addEventListener("click", async function () {
                await widgets.requestPayment({
                    orderId: "order-{{ $order->id }}-{{ Str::uuid() }}",
                    orderName: "{{ $order->items->first()->product_name }}{{ $order->items->count() > 1 ? ' 외 ' . ($order->items->count() - 1) . '건' : '' }}",
                    successUrl: "{{ route('payment.success') }}",
                    failUrl: "{{ route('payment.fail') }}",
                    customerEmail: "{{ $order->guest_email }}",
                    customerName: "{{ $order->guest_name }}",
                    customerMobilePhone: "{{ preg_replace('/[^0-9]/', '', $order->guest_phone) }}",
                });
            });
        }
    </script>
</x-app-layout>
