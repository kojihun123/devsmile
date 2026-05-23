<section>
    <p class="text-sm text-gray-500 mb-4">탈퇴 시 모든 정보가 영구 삭제되며 복구할 수 없습니다.</p>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="text-sm text-red-600 border border-red-300 px-4 py-2 rounded-lg hover:bg-red-50 transition">
        회원 탈퇴
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-base font-semibold text-gray-900 mb-2">정말 탈퇴하시겠습니까?</h2>
            <p class="text-sm text-gray-500 mb-5">비밀번호를 입력하면 계정이 영구 삭제됩니다.</p>

            <input id="password" name="password" type="password"
                   placeholder="비밀번호"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 mb-2">
            @error('password', 'userDeletion')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror

            <div class="flex justify-end gap-3 mt-5">
                <button type="button" x-on:click="$dispatch('close')"
                        class="text-sm text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                    취소
                </button>
                <button type="submit"
                        class="text-sm text-white bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    탈퇴
                </button>
            </div>
        </form>
    </x-modal>
</section>
