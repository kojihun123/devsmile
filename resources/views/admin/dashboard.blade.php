<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-2xl font-bold text-gray-900 mb-8">관리자 대시보드</h1>

        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm text-center">
                <p class="text-3xl font-bold text-gray-900">{{ $productCount }}</p>
                <p class="text-sm text-gray-400 mt-1">전체 상품</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm text-center">
                <p class="text-3xl font-bold text-gray-900">{{ $orderCount }}</p>
                <p class="text-sm text-gray-400 mt-1">전체 주문</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm text-center">
                <p class="text-3xl font-bold text-gray-900">{{ $paidCount }}</p>
                <p class="text-sm text-gray-400 mt-1">결제 완료</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.categories.index') }}"
               class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                카테고리 관리
            </a>
            <a href="{{ route('admin.products.index') }}"
               class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                상품 관리
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                주문 관리
            </a>
        </div>

    </div>
</x-app-layout>
