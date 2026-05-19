<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-2xl font-bold text-gray-900 mb-6">내 주문 목록</h1>

        @if ($orders->isEmpty())
            <div class="bg-white border border-gray-100 rounded-xl p-12 shadow-sm text-center text-gray-400 text-sm">
                주문 내역이 없습니다.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($orders as $order)
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
                    <a href="{{ route('orders.detail', $order) }}"
                       class="block bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">주문 #{{ $order->id }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2 py-1 rounded-full {{ $s['class'] }}">{{ $s['text'] }}</span>
                                <p class="text-sm font-bold text-gray-900 mt-1">{{ number_format($order->total_amount) }}원</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">{{ $orders->links() }}</div>
        @endif

    </div>
</x-app-layout>
