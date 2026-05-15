<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- 페이지 타이틀 --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">전체 상품</h1>
            <p class="text-sm text-gray-500 mt-1">개발자 감성 굿즈를 만나보세요</p>
        </div>

        {{-- 카테고리 필터 --}}
        <div class="flex gap-2 mb-8 flex-wrap">
            <a href="{{ route('home') }}"
               class="px-4 py-1.5 rounded-full text-sm border
                      {{ !request('category') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-500' }}">
                전체
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('home', ['category' => $category->id]) }}"
                   class="px-4 py-1.5 rounded-full text-sm border
                          {{ request('category') == $category->id ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-500' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        {{-- 상품 그리드 --}}
        @if ($products->isEmpty())
            <div class="text-center py-20 text-gray-400">
                상품이 없습니다.
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden">

                        {{-- 썸네일 + 정보는 <a>로 감싸서 상세 이동 --}}
                        <a href="{{ route('products.show', $product) }}" class="block">
                            <div class="aspect-square bg-gray-50 flex items-center justify-center">
                                @if ($product->thumbnail)
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <span class="text-4xl">📦</span>
                                @endif
                            </div>

                            <div class="p-4">
                                <p class="text-xs text-gray-400 mb-1">{{ $product->category->name }}</p>
                                <h2 class="text-sm font-semibold text-gray-900 leading-snug mb-2">
                                    {{ $product->name }}
                                </h2>
                                <p class="text-sm font-bold text-gray-900">
                                    {{ number_format($product->price) }}원
                                </p>
                            </div>
                        </a>

                        {{-- 버튼은 <a> 밖에 --}}
                        <div class="px-4 pb-4">
                            <button
                                class="w-full text-sm bg-gray-900 text-white py-2 rounded-lg hover:bg-gray-700 transition"
                                onclick="addToCart({{ $product->id }}, 1)"
                            >
                                장바구니 담기
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>