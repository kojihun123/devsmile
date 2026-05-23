<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-400 hover:text-gray-600 mb-4 inline-block">← 상품 관리</a>
        <h1 class="text-2xl font-bold text-gray-900 mb-8">상품 수정</h1>

        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data"
              class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm text-gray-600 mb-1">카테고리</label>
                <select name="category_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                                @selected(old('category_id', $product->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">상품명</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">설명</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">{{ old('description', $product->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">가격 (원)</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">재고</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">상태</label>
                <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="active" @selected(old('status', $product->status) === 'active')>판매중</option>
                    <option value="inactive" @selected(old('status', $product->status) === 'inactive')>판매중단</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">썸네일 이미지</label>
                @if ($product->thumbnail)
                    <img src="{{ asset('storage/' . $product->thumbnail) }}"
                         class="w-20 h-20 object-cover rounded-lg mb-2">
                    <label class="flex items-center gap-2 text-xs text-red-500 mb-2 cursor-pointer">
                        <input type="checkbox" name="delete_thumbnail" value="1">
                        현재 이미지 삭제
                    </label>
                @endif
                <input type="file" name="thumbnail" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                <p class="text-xs text-gray-400 mt-1">변경 시에만 업로드</p>
                @error('thumbnail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">배달 이미지 (이메일 첨부용)</label>
                @if ($product->delivery_image)
                    <img src="{{ asset('storage/' . $product->delivery_image) }}"
                         class="w-20 h-20 object-cover rounded-lg mb-2">
                    <label class="flex items-center gap-2 text-xs text-red-500 mb-2 cursor-pointer">
                        <input type="checkbox" name="delete_delivery_image" value="1">
                        현재 이미지 삭제
                    </label>
                @endif
                <input type="file" name="delivery_image" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                <p class="text-xs text-gray-400 mt-1">변경 시에만 업로드</p>
                @error('delivery_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.products.index') }}"
                   class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">취소</a>
                <button type="submit"
                        class="bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    저장
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
