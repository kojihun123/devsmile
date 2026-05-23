<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600 mb-4 inline-block">← 대시보드</a>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">상품 관리</h1>
            <a href="{{ route('admin.products.create') }}"
               class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                + 상품 등록
            </a>
        </div>

        <form method="GET" class="flex gap-2 mb-4">
            <select name="status" onchange="this.form.submit()"
                    class="min-w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                <option value="">전체 상태</option>
                <option value="active" @selected(request('status') === 'active')>판매중</option>
                <option value="inactive" @selected(request('status') === 'inactive')>판매중단</option>
            </select>
            <select name="category" onchange="this.form.submit()"
                    class="min-w-40 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                <option value="">전체 카테고리</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @if (request('status') || request('category'))
                <a href="{{ route('admin.products.index') }}"
                   class="px-3 py-2 text-sm text-gray-400 hover:text-gray-600">초기화</a>
            @endif
        </form>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">썸네일</th>
                        <th class="px-4 py-3 text-left">상품명</th>
                        <th class="px-4 py-3 text-left">카테고리</th>
                        <th class="px-4 py-3 text-right">가격</th>
                        <th class="px-4 py-3 text-right">재고</th>
                        <th class="px-4 py-3 text-center">상태</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden">
                                    @if ($product->thumbnail)
                                        <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300 text-xs">없음</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($product->price) }}원</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ $product->stock }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($product->status === 'active')
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">판매중</span>
                                @else
                                    <span class="bg-gray-100 text-gray-500 text-xs px-2 py-1 rounded-full">판매중단</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="text-xs text-gray-500 hover:text-gray-900">수정</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                          onsubmit="return confirm('삭제하시겠습니까?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">삭제</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">등록된 상품이 없습니다.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>

    </div>
</x-app-layout>
