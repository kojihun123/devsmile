<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-2xl font-bold text-gray-900 mb-8">관리자 대시보드</h1>

        <div class="grid grid-cols-3 gap-4 mb-8">
            <a href="{{ route('admin.users.index') }}"
               class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm text-center hover:shadow-md transition">
                <p class="text-3xl font-bold text-gray-900">{{ $userCount }}</p>
                <p class="text-sm text-gray-400 mt-1">전체 회원</p>
            </a>
            <a href="{{ route('admin.products.index') }}"
               class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm text-center hover:shadow-md transition">
                <p class="text-3xl font-bold text-gray-900">{{ $productCount }}</p>
                <p class="text-sm text-gray-400 mt-1">전체 상품</p>
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm text-center hover:shadow-md transition">
                <p class="text-3xl font-bold text-gray-900">{{ $orderCount }}</p>
                <p class="text-sm text-gray-400 mt-1">전체 주문</p>
            </a>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('admin.categories.index') }}"
               class="px-4 py-2 text-sm bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-900 hover:text-white hover:border-gray-900 transition">
                카테고리 관리
            </a>
            <a href="{{ route('admin.products.index') }}"
               class="px-4 py-2 text-sm bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-900 hover:text-white hover:border-gray-900 transition">
                상품 관리
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="px-4 py-2 text-sm bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-900 hover:text-white hover:border-gray-900 transition">
                주문 관리
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 text-sm bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-900 hover:text-white hover:border-gray-900 transition">
                회원 관리
            </a>
        </div>

    </div>
</x-app-layout>
