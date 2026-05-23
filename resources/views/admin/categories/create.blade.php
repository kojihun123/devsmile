<x-app-layout>
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-400 hover:text-gray-600 mb-4 inline-block">← 카테고리 관리</a>
        <h1 class="text-2xl font-bold text-gray-900 mb-8">카테고리 등록</h1>

        <form method="POST" action="{{ route('admin.categories.store') }}"
              class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm space-y-5">
            @csrf

            <div>
                <label class="block text-sm text-gray-600 mb-1">카테고리명</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.categories.index') }}"
                   class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">취소</a>
                <button type="submit"
                        class="bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    등록
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
