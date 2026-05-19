<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← 목록</a>
            <h1 class="text-2xl font-bold text-gray-900">주문 #{{ $order->id }}</h1>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

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
            <div class="space-y-2">
                @foreach ($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">{{ $item->product_name }} × {{ $item->quantity }}개</span>
                        <span class="font-medium">{{ number_format($item->price * $item->quantity) }}원</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between text-sm font-bold">
                <span>총 결제금액</span>
                <span>{{ number_format($order->total_amount) }}원</span>
            </div>
        </div>

        {{-- 상태 변경 --}}
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">주문 상태 변경</h2>
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex gap-3">
                @csrf
                @method('PUT')
                <select name="status"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    @foreach (['pending' => '결제대기', 'paid' => '결제완료', 'shipping' => '배송중', 'delivered' => '배달완료', 'cancelled' => '취소'] as $value => $label)
                        <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    변경
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
