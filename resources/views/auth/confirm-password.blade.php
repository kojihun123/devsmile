<x-app-layout>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">

            <h1 class="text-2xl font-bold text-gray-900 mb-2 text-center">비밀번호 확인</h1>
            <p class="text-sm text-gray-500 text-center mb-8">계속하려면 비밀번호를 입력해주세요.</p>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">비밀번호</label>
                    <input id="password" type="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                           required autocomplete="current-password">
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                        class="w-full bg-gray-900 text-white text-sm py-2.5 rounded-lg hover:bg-gray-700 transition">
                    확인
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
