# Database Schema

## 테이블 목록

| 테이블 | 설명 |
|--------|------|
| `users` | 회원 (Laravel Breeze 기본 제공) |
| `categories` | 상품 카테고리 |
| `products` | 상품 |
| `carts` | 장바구니 |
| `cart_items` | 장바구니 상품 |
| `orders` | 주문 |
| `order_items` | 주문 상품 |
| `payments` | 결제 정보 (토스페이먼츠) |

## 상세 스키마

### users
> Laravel Breeze 기본 제공

| 컬럼 | 타입 | 설명 |
|------|------|------|
| id | bigint | PK |
| name | varchar | 이름 |
| email | varchar | 이메일 |
| password | varchar | 비밀번호 |
| created_at, updated_at | timestamp | |

---

### categories

| 컬럼 | 타입 | 설명 |
|------|------|------|
| id | bigint | PK |
| name | varchar(50) | 카테고리명 |
| created_at, updated_at | timestamp | |

---

### products

| 컬럼 | 타입 | 설명 |
|------|------|------|
| id | bigint | PK |
| category_id | bigint | FK → categories |
| name | varchar(100) | 상품명 |
| description | text | 상품 설명 |
| price | int | 가격 (원 단위) |
| stock | int | 재고 |
| status | enum | active / inactive |
| thumbnail | varchar | 대표 이미지 경로 |
| created_at, updated_at | timestamp | |

---

### carts

| 컬럼 | 타입 | 설명 |
|------|------|------|
| id | bigint | PK |
| user_id | bigint nullable | FK → users (로그인 유저) |
| session_id | varchar nullable | 비회원 식별용 세션 ID |
| created_at, updated_at | timestamp | |

> user_id / session_id 둘 중 하나만 값이 들어온다.

---

### cart_items

| 컬럼 | 타입 | 설명 |
|------|------|------|
| id | bigint | PK |
| cart_id | bigint | FK → carts |
| product_id | bigint | FK → products |
| quantity | int | 수량 |
| created_at, updated_at | timestamp | |

---

### orders

| 컬럼 | 타입 | 설명 |
|------|------|------|
| id | bigint | PK |
| user_id | bigint nullable | FK → users (로그인 유저) |
| guest_name | varchar nullable | 비회원 이름 |
| guest_email | varchar nullable | 비회원 이메일 |
| guest_phone | varchar nullable | 비회원 연락처 |
| total_amount | int | 총 결제 금액 |
| status | enum | pending / paid / shipping / delivered / cancelled |
| created_at, updated_at | timestamp | |

> user_id가 null이면 비회원 주문, guest_* 컬럼으로 식별한다.

---

### order_items

| 컬럼 | 타입 | 설명 |
|------|------|------|
| id | bigint | PK |
| order_id | bigint | FK → orders |
| product_id | bigint | FK → products |
| product_name | varchar | 주문 당시 상품명 스냅샷 |
| price | int | 주문 당시 가격 스냅샷 |
| quantity | int | 수량 |
| created_at, updated_at | timestamp | |

> product_name / price는 스냅샷으로 저장한다.
> 상품 정보가 이후 변경되어도 주문 당시 데이터를 보존하기 위함이다.

---

### payments

| 컬럼 | 타입 | 설명 |
|------|------|------|
| id | bigint | PK |
| order_id | bigint | FK → orders |
| payment_key | varchar | 토스페이먼츠 결제 키 |
| amount | int | 결제 금액 |
| status | enum | pending / done / failed / cancelled |
| paid_at | timestamp nullable | 결제 완료 시각 |
| created_at, updated_at | timestamp | |

## 관계 정리

```
users ──< orders
users ──< carts
categories ──< products
products ──< cart_items >── carts
products ──< order_items >── orders
orders ──< payments
```
