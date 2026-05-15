<x-app-layout>
    <div x-data="{ total: {{ $total }} }" @cart-updated.window="total = $event.detail.total" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-2xl font-bold text-gray-900 mb-8">장바구니</h1>

        @if ($items->isEmpty())
            <div class="text-center py-20">
                <p class="text-gray-400 text-sm mb-4">장바구니가 비어있어요</p>
                <a href="{{ route('home') }}" class="text-sm bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition">
                    쇼핑 계속하기
                </a>
            </div>
        @else
            <div class="space-y-4 mb-10">
                @foreach ($items as $item)
                    <div x-data="{ quantity: {{ $item->quantity }} }"
                         class="flex items-center gap-4 bg-white border border-gray-100 rounded-xl p-4 shadow-sm">

                        {{-- 썸네일 --}}
                        <div class="w-20 h-20 bg-gray-50 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                            @if ($item->product->thumbnail)
                                <img src="{{ asset('storage/' . $item->product->thumbnail) }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <span class="text-2xl">📦</span>
                            @endif
                        </div>

                        {{-- 상품 정보 --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">{{ number_format($item->product->price) }}원</p>
                        </div>

                        {{-- 수량 변경 --}}
                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                            <button @click="if (quantity > 1) { quantity--; updateCart({{ $item->id }}, 'decrease') }"
                                    class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 transition text-sm">−</button>
                            <span x-text="quantity" class="px-3 py-1.5 text-sm font-medium"></span>
                            <button @click="if (quantity < {{ $item->product->stock }}) { quantity++; updateCart({{ $item->id }}, 'increase') }"
                                    class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 transition text-sm">+</button>
                        </div>

                        {{-- 소계 --}}
                        <span class="text-sm font-bold text-gray-900 w-24 text-right shrink-0"
                              x-text="({{ $item->product->price }} * quantity).toLocaleString('ko-KR') + '원'"></span>

                        {{-- 삭제 --}}
                        <form method="POST" action="{{ route('cart.remove', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 transition text-sm">
                                ✕
                            </button>
                        </form>

                    </div>
                @endforeach
            </div>

            {{-- 합계 + 주문 --}}
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-gray-600">총 결제 금액</span>
                    <span class="text-xl font-bold text-gray-900"
                          x-text="total.toLocaleString('ko-KR') + '원'"></span>
                </div>
                <a href="#" class="block w-full text-center bg-gray-900 text-white py-3 rounded-lg font-medium hover:bg-gray-700 transition">
                    주문하기
                </a>
            </div>
        @endif

    </div>

    <script>
        async function updateCart(itemId, action) {
            const res = await fetch('/cart/' + itemId, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ action: action })
            });

            if (res.ok) {
                const json = await res.json();
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { total: json.total } }));

                if (action === 'increase') {
                    Alpine.store('cart').count++;
                } else {
                    Alpine.store('cart').count--;
                }
                
            } else {
                Alpine.store('toast').show('수량 변경에 실패했습니다.', 'error');
            }
        }
    </script>
</x-app-layout>