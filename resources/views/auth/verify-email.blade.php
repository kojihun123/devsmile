<x-app-layout>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm text-center">

            <h1 class="text-2xl font-bold text-gray-900 mb-2">이메일 인증</h1>
            <p class="text-sm text-gray-500 mb-8">가입하신 이메일로 인증 링크를 보내드렸습니다.<br>메일함을 확인해주세요.</p>

            @if (session('status') == 'verification-link-sent')
                <p class="text-sm text-green-600 mb-6">인증 메일을 다시 보냈습니다.</p>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="w-full bg-gray-900 text-white text-sm py-2.5 rounded-lg hover:bg-gray-700 transition mb-4">
                    인증 메일 재발송
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline">
                    로그아웃
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
