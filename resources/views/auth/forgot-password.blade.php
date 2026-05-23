<x-app-layout>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">

            <h1 class="text-2xl font-bold text-gray-900 mb-2 text-center">비밀번호 찾기</h1>
            <p class="text-sm text-gray-500 text-center mb-8">가입한 이메일로 재설정 링크를 보내드립니다.</p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">이메일</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                           required autofocus>
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                        class="w-full bg-gray-900 text-white text-sm py-2.5 rounded-lg hover:bg-gray-700 transition">
                    재설정 링크 보내기
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
