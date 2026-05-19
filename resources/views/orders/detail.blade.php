<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="flex items-center gap-3 mb-8">
            @auth
                <a href="{{ route('orders.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← 주문 목록</a>
            @endauth
            <h1 class="text-2xl font-bold text-gray-900">주문 #{{ $order->id }}</h1>
            @php
                $statusMap = [
                    'pending'   => ['text' => '결제대기', 'class' => 'bg-yellow-100 text-yellow-700'],
                    'paid'      => ['text' => '결제완료', 'class' => 'bg-blue-100 text-blue-700'],
                    'shipping'  => ['text' => '배송중',   'class' => 'bg-purple-100 text-purple-700'],
                    'delivered' => ['text' => '배달완료', 'class' => 'bg-green-100 text-green-700'],
                    'cancelled' => ['text' => '취소',     'class' => 'bg-gray-100 text-gray-500'],
                ];
                $s = $statusMap[$order->status] ?? ['text' => $order->status, 'class' => 'bg-gray-100 text-gray-500'];
            @endphp
            <span class="text-xs px-2 py-1 rounded-full {{ $s['class'] }}">{{ $s['text'] }}</span>
        </div>

        {{-- 주문자 정보 --}}
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm mb-4 space-y-1 text-sm text-gray-600">
            <h2 class="text-sm font-semibold text-gray-900 mb-3">주문자 정보</h2>
            <p>이름: {{ $order->guest_name }}</p>
            <p>이메일: {{ $order->guest_email }}</p>
            <p>전화번호: {{ $order->guest_phone }}</p>
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
                        <span class="font-semibold text-gray-900">{{ number_format($item->price * $item->quantity) }}원</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between items-center">
                <span class="text-sm text-gray-600">총 결제 금액</span>
                <span class="text-lg font-bold text-gray-900">{{ number_format($order->total_amount) }}원</span>
            </div>
        </div>

        {{-- 결제 정보 --}}
        @if ($order->payment)
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm mb-4 text-sm text-gray-600 space-y-1">
                <h2 class="text-sm font-semibold text-gray-900 mb-3">결제 정보</h2>
                <p>결제 수단: 카드</p>
                <p>결제 일시: {{ $order->payment->paid_at?->format('Y-m-d H:i') }}</p>
            </div>
        @endif

        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-gray-600">쇼핑 계속하기</a>
        </div>

    </div>
</x-app-layout>
