<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600 mb-4 inline-block">← 대시보드</a>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">회원 관리</h1>

        @if (session('success'))
            <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" class="flex gap-2 mb-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="이름 또는 이메일 검색"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 w-64">
            <button type="submit"
                    class="bg-gray-900 text-white text-sm px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                검색
            </button>
            @if (request('search'))
                <a href="{{ route('admin.users.index') }}"
                   class="px-3 py-2 text-sm text-gray-400 hover:text-gray-600">초기화</a>
            @endif
        </form>

        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">이름</th>
                        <th class="px-4 py-3 text-left">이메일</th>
                        <th class="px-4 py-3 text-left">전화번호</th>
                        <th class="px-4 py-3 text-left">가입일</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('{{ $user->name }} 회원을 삭제하시겠습니까?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">삭제</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">회원이 없습니다.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>

    </div>
</x-app-layout>
