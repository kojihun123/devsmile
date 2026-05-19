<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-2xl font-bold text-gray-900 mb-8">주문서</h1>

        <form method="POST" action="{{ route('orders.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- 좌: 배송 정보 --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm space-y-4">
                    <h2 class="text-base font-semibold text-gray-900 mb-2">배송 정보</h2>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">이름</label>
                        <input type="text" name="name"
                               value="{{ old('name', Auth::user()?->name) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                               placeholder="홍길동" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">이메일</label>
                        <input type="email" name="email"
                               value="{{ old('email', Auth::user()?->email) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                               placeholder="example@email.com" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">전화번호</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', Auth::user()?->phone) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                               placeholder="010-0000-0000" required>
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- 우: 주문 요약 --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">주문 상품</h2>

                    <div class="space-y-3 mb-6">
                        @foreach ($items as $item)
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                    @if ($item->product->thumbnail)
                                        <img src="{{ asset('storage/' . $item->product->thumbnail) }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <span class="text-lg">📦</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ number_format($item->product->price) }}원 × {{ $item->quantity }}개</p>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 shrink-0">
                                    {{ number_format($item->product->price * $item->quantity) }}원
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 flex justify-between items-center mb-6">
                        <span class="text-sm text-gray-600">총 결제 금액</span>
                        <span class="text-lg font-bold text-gray-900">{{ number_format($total) }}원</span>
                    </div>

                    <button type="submit"
                            class="w-full bg-gray-900 text-white py-3 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                        주문하기
                    </button>
                </div>

            </div>
        </form>

    </div>    

    <script src="https://js.tosspayments.com/v2/standard"></script>
    <script>    
        const tossPayments = TossPayments("test_gck_docs_Ovk5rk1EwkEbP0W43n07xlzm");
        const widgets = tossPayments.widgets({ customerKey: TossPayments.ANONYMOUS });
    </script>
</x-app-layout>
