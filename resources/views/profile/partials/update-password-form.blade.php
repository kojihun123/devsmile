<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">현재 비밀번호</label>
            <input id="update_password_current_password" name="current_password" type="password"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                   autocomplete="current-password">
            @error('current_password', 'updatePassword')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">새 비밀번호</label>
            <input id="update_password_password" name="password" type="password"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                   autocomplete="new-password">
            @error('password', 'updatePassword')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">새 비밀번호 확인</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                   autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="bg-gray-900 text-white text-sm px-5 py-2 rounded-lg hover:bg-gray-700 transition">
                변경
            </button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-500">변경되었습니다.</p>
            @endif
        </div>
    </form>
</section>
