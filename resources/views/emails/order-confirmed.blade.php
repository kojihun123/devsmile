<x-mail::message>
# 주문이 확인되었습니다!

안녕하세요, **{{ $order->guest_name }}**님.
주문해 주셔서 감사합니다.

---

## 주문 정보

| | |
|---|---|
| 주문번호 | #{{ $order->id }} |
| 주문자 | {{ $order->guest_name }} |
| 이메일 | {{ $order->guest_email }} |

## 주문 상품

@foreach ($order->items as $item)
- **{{ $item->product_name }}** × {{ $item->quantity }}개 — {{ number_format($item->price * $item->quantity) }}원
@endforeach

---

**총 결제 금액: {{ number_format($order->total_amount) }}원**

감사합니다,
{{ config('app.name') }}
</x-mail::message>
