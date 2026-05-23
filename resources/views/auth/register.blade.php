<x-app-layout>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">

            <h1 class="text-2xl font-bold text-gray-900 mb-8 text-center">회원가입</h1>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">이름</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                           required autofocus autocomplete="name">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">이메일</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                           required autocomplete="username">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">전화번호</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                           required autocomplete="tel" placeholder="010-0000-0000">
                    @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">비밀번호</label>
                    <input id="password" type="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                           required autocomplete="new-password">
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">비밀번호 확인</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                           required autocomplete="new-password">
                    @error('password_confirmation')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                        class="w-full bg-gray-900 text-white text-sm py-2.5 rounded-lg hover:bg-gray-700 transition mt-2">
                    가입하기
                </button>

                <p class="text-center text-sm text-gray-500">
                    이미 계정이 있으신가요?
                    <a href="{{ route('login') }}" class="text-gray-900 font-medium hover:underline">로그인</a>
                </p>
            </form>

        </div>
    </div>
</x-app-layout>
