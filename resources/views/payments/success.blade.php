<x-app-layout>
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">

        {{-- 로딩 --}}
        <div id="loading">
            <div class="text-4xl mb-4 animate-spin inline-block">⏳</div>
            <p class="text-sm text-gray-400">결제를 처리하고 있습니다...</p>
        </div>

        {{-- 성공 (confirm 완료 후 표시) --}}
        <div id="success" class="hidden">
            <div class="text-5xl mb-4">🎉</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">결제가 완료됐습니다</h1>
            <p class="text-sm text-gray-400 mb-8">주문해 주셔서 감사합니다!</p>

            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm text-left mb-6 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">주문번호</span>
                    <span id="orderId" class="font-medium text-gray-900"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">결제 금액</span>
                    <span id="amount" class="font-bold text-gray-900"></span>
                </div>
            </div>

            <a href="{{ route('home') }}"
               class="block w-full bg-gray-900 text-white py-3 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                쇼핑 계속하기
            </a>
        </div>

    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const paymentKey = urlParams.get("paymentKey");
        const orderId    = urlParams.get("orderId");
        const amount     = urlParams.get("amount");

        async function confirm() {
            const response = await fetch("/confirm", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ paymentKey, orderId, amount }),
            });

            const json = await response.json();

            if (!response.ok) {
                window.location.href = `/payment/fail?message=${encodeURIComponent(json.message)}&code=${json.code ?? ''}`;
                return;
            }

            document.getElementById("orderId").textContent = '#' + orderId.split('-')[1];
            document.getElementById("amount").textContent  = Number(amount).toLocaleString('ko-KR') + "원";

            document.getElementById("loading").classList.add("hidden");
            document.getElementById("success").classList.remove("hidden");
        }

        confirm();
    </script>
</x-app-layout>
