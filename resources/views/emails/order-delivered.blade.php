<x-mail::message>
# 배달 완료! 🎉

**{{ $order->guest_name }}**님, 주문하신 상품이 도착했습니다.
첨부파일을 확인해 주세요.

---

@foreach ($order->items as $item)
- **{{ $item->product_name }}** × {{ $item->quantity }}개
@endforeach

---

{{ config('app.name') }}
</x-mail::message>
