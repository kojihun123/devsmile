<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- 뒤로가기 --}}
        <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-900 mb-6 inline-block">
            ← 전체 상품으로
        </a>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            {{-- 썸네일 --}}
            <div class="aspect-square bg-gray-50 rounded-2xl flex items-center justify-center overflow-hidden">
                @if ($product->thumbnail)
                    <img src="{{ asset('storage/' . $product->thumbnail) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover">
                @else
                    <span class="text-8xl">📦</span>
                @endif
            </div>

            {{-- 상품 정보 --}}
            <div class="flex flex-col justify-center">

                <p class="text-sm text-gray-400 mb-2">{{ $product->category->name }}</p>

                <h1 class="text-2xl font-bold text-gray-900 mb-4 leading-snug">
                    {{ $product->name }}
                </h1>

                <p class="text-gray-600 text-sm leading-relaxed mb-6">
                    {{ $product->description }}
                </p>

                <p class="text-2xl font-bold text-gray-900 mb-2">
                    {{ number_format($product->price) }}원
                </p>

                <p class="text-sm mb-6
                    {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                    {{ $product->stock > 0 ? '재고 있음 (' . $product->stock . '개)' : '품절' }}
                </p>

                {{-- 수량 + 담기 --}}
                <div x-data="{ quantity : 1 }" class="flex items-center gap-3">
                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                        <button @click="quantity = Math.max(1, quantity - 1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition">−</button>
                        <span x-text="quantity" class="px-4 py-2 text-sm font-medium"></span>
                        <button @click="quantity = Math.min({{ $product->stock }}, quantity + 1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition">+</button>
                    </div>

                    <button
                        @click="addToCart({{ $product->id }}, quantity)"
                        @if($product->stock === 0) disabled @endif
                        class="flex-1 bg-gray-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 transition
                               disabled:bg-gray-300 disabled:cursor-not-allowed">
                        {{ $product->stock > 0 ? '장바구니 담기' : '품절' }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
