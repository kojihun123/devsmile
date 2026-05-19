# 아키텍처 및 ERD

## 시스템 아키텍처

```
                        ┌─────────────────────────────────────────┐
                        │           AWS Lightsail (Ubuntu)         │
                        │                                          │
  사용자 브라우저  ──────▶│  nginx (80/443)                          │
                        │    │                                     │
                        │    ▼                                     │
                        │  app (PHP-FPM 9000)                      │
                        │    │          │                          │
                        │    ▼          ▼                          │
                        │  MySQL      Redis                        │
                        │  (DB)    (Cache/Session/Queue)           │
                        │    │          │                          │
                        │    │          ▼                          │
                        │    │      queue worker                   │
                        │    │      (이메일 발송)                    │
                        │    │                                     │
                        │  certbot (SSL 자동 갱신)                  │
                        └─────────────────────────────────────────┘

  GitHub ──▶ GitHub Actions ──▶ SSH ──▶ deploy_update.sh (자동 배포)
```

---

## 결제 플로우

```
사용자          Laravel            토스페이먼츠         Queue Worker
  │                │                    │                   │
  │── 주문 생성 ──▶│                    │                   │
  │◀── 주문 ID ───│                    │                   │
  │                │                    │                   │
  │── 결제 요청 ──▶│── confirmPayment ─▶│                   │
  │                │◀── 승인 결과 ──────│                   │
  │                │                    │                   │
  │                │── DB Lock ─────────│                   │
  │                │   (멱등성 체크)     │                   │
  │                │   재고 차감         │                   │
  │                │   상태 → paid       │                   │
  │                │                    │                   │
  │                │── dispatch ────────────────────────────▶│
  │                │   (주문확인 메일)    │                   │── 즉시 발송
  │                │   (배송중 메일)      │                   │── 5분 후
  │                │   (배달완료 메일)    │                   │── 10분 후
  │◀── 완료 ───────│                    │                   │
```

---

## ERD

```
users
├── id
├── name
├── email (unique)
├── phone (nullable)
├── password
├── is_admin (boolean)
└── timestamps

categories
├── id
├── name
└── timestamps

products
├── id
├── category_id (FK → categories)
├── name
├── description
├── price
├── stock
├── status (active / inactive)
├── thumbnail (nullable)
├── delivery_image (nullable)
└── timestamps

carts
├── id
├── user_id (FK → users, nullable) ─── 회원
├── session_id (nullable) ──────────── 비회원
└── timestamps

cart_items
├── id
├── cart_id (FK → carts)
├── product_id (FK → products)
├── quantity
└── timestamps

orders
├── id
├── user_id (FK → users, nullable) ─── 회원
├── guest_name (nullable) ──────────── 비회원
├── guest_email (nullable)
├── guest_phone (nullable)
├── total_amount
├── status (pending / paid / shipping / delivered / cancelled)
└── timestamps

order_items
├── id
├── order_id (FK → orders)
├── product_id (FK → products)
├── product_name (주문 시점 상품명 스냅샷)
├── price (주문 시점 가격 스냅샷)
├── quantity
└── timestamps

payments
├── id
├── order_id (FK → orders)
├── payment_key (unique, 토스페이먼츠 키)
├── amount
├── status (pending / done / failed / cancelled)
├── paid_at (nullable)
└── timestamps
```

---

## 주요 설계 포인트

| 항목 | 설명 |
|------|------|
| 결제 멱등성 | `DB::transaction` + `lockForUpdate()` 로 동시 요청 직렬화 |
| 재고 방어 | transaction 내 재고 체크 → 부족 시 RuntimeException → rollback → Toss 취소 API |
| 비회원 지원 | `user_id` nullable + `session_id` 로 회원/비회원 장바구니 분리 |
| 주문 스냅샷 | `order_items.product_name`, `price` 저장으로 상품 변경/삭제 시에도 주문 내역 보존 |
| Queue | Redis driver + Worker 컨테이너 분리, 이메일 delay 처리 |
