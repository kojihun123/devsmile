<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-2xl font-bold text-gray-900 mb-8">내 정보</h1>

        {{-- 프로필 정보 --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-base font-semibold text-gray-900 mb-5">기본 정보</h2>
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- 비밀번호 변경 --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-base font-semibold text-gray-900 mb-5">비밀번호 변경</h2>
            @include('profile.partials.update-password-form')
        </div>

        {{-- 회원 탈퇴 --}}
        <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-red-600 mb-5">회원 탈퇴</h2>
            @include('profile.partials.delete-user-form')
        </div>

    </div>
</x-app-layout>
