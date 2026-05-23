<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">이름</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', $user->name) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                   required autofocus autocomplete="name">
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">이메일</label>
            <input id="email" name="email" type="email"
                   value="{{ old('email', $user->email) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                   required autocomplete="username">
            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="bg-gray-900 text-white text-sm px-5 py-2 rounded-lg hover:bg-gray-700 transition">
                저장
            </button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-500">저장되었습니다.</p>
            @endif
        </div>
    </form>
</section>
