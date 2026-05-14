<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- 로고 --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight text-gray-900">
                    DEVSMILE
                </a>
            </div>

            {{-- 우측 메뉴 --}}
            <div class="flex items-center gap-4">

                {{-- 장바구니 --}}
                <a href="{{ route('cart.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    장바구니
                </a>

                @auth
                    <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>

                    @if(Auth::user()->is_admin)
                        <a href="#" class="text-sm text-gray-600 hover:text-gray-900">관리자</a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                            로그아웃
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">로그인</a>
                    <a href="{{ route('register') }}" class="text-sm bg-gray-900 text-white px-3 py-1.5 rounded hover:bg-gray-700">회원가입</a>
                @endauth
            </div>

        </div>
    </div>
</nav>
