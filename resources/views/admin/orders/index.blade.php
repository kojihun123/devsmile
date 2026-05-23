<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600 mb-4 inline-block">← 대시보드</a>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">주문 관리</h1>

        <form method="GET" class="flex gap-2 mb-4">
            <select name="status" onchange="this.form.submit()"
                    class="min-w-36 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                <option value="">전체 상태</option>
                @foreach (['pending' => '결제대기', 'paid' => '결제완료', 'shipping' => '배송중', 'delivered' => '배달완료', 'cancelled' => '취소'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            @if (request('status'))
                <a href="{{ route('admin.orders.index') }}"
                   class="px-3 py-2 text-sm text-gray-400 hover:text-gray-600">초기화</a>
            @endif
        </form>

        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">주문번호</th>
                        <th class="px-4 py-3 text-left">주문자</th>
                        <th class="px-4 py-3 text-right">결제금액</th>
                        <th class="px-4 py-3 text-center">상태</th>
                        <th class="px-4 py-3 text-left">주문일시</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($orders as $order)
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
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">#{{ $order->id }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $order->guest_name }}</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($order->total_amount) }}원</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs px-2 py-1 rounded-full {{ $s['class'] }}">{{ $s['text'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-xs text-gray-500 hover:text-gray-900">상세</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">주문이 없습니다.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $orders->links() }}</div>

    </div>
</x-app-layout>
