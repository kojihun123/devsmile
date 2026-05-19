<x-app-layout>
    <div class="max-w-md mx-auto px-4 py-20 text-center">
        <div class="text-4xl mb-4">❌</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">결제 실패</h2>
        <p id="code" class="text-sm text-gray-400"></p>
        <p id="message" class="text-sm text-red-500 mt-1"></p>

        <a href="{{ route('home') }}"
           class="inline-block mt-8 bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm hover:bg-gray-700 transition">
            홈으로 돌아가기
        </a>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        document.getElementById("code").textContent    = "에러코드: " + urlParams.get("code");
        document.getElementById("message").textContent = "실패 사유: " + urlParams.get("message");
    </script>
</x-app-layout>
