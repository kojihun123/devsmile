<x-mail::message>
# 배송이 시작되었습니다! 🚚

안녕하세요, **{{ $order->guest_name }}**님.
주문하신 상품이 배송을 시작했습니다.

---

## 주문 정보

| | |
|---|---|
| 주문번호 | #{{ $order->id }} |
| 수령인 | {{ $order->guest_name }} |

## 배송 상품

@foreach ($order->items as $item)
- **{{ $item->product_name }}** × {{ $item->quantity }}개
@endforeach

---

곧 도착할 예정입니다. 조금만 기다려 주세요!

감사합니다,
{{ config('app.name') }}
</x-mail::message>
