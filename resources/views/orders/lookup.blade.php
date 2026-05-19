<x-app-layout>
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-16">

        <h1 class="text-2xl font-bold text-gray-900 mb-2">비회원 주문 조회</h1>
        <p class="text-sm text-gray-400 mb-8">주문번호와 이메일을 입력해 주세요.</p>

        <form method="POST" action="{{ route('orders.lookup.post') }}"
              class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-600 mb-1">주문번호</label>
                <input type="number" name="order_id" value="{{ old('order_id') }}"
                       placeholder="예) 3"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">이메일</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="주문 시 입력한 이메일"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
            </div>

            @error('order_id')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror

            <button type="submit"
                    class="w-full bg-gray-900 text-white py-3 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                조회하기
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-gray-600">
                회원이신가요? 로그인하기
            </a>
        </div>

    </div>
</x-app-layout>
