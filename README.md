# DEVSMILE

개발자 감성 밈/굿즈 쇼핑몰 포트폴리오 프로젝트

> "404 행복을 찾을 수 없습니다" 같은 상품을 파는 유쾌한 개발자 감성 쇼핑몰

## Tech Stack

| 분류 | 기술 |
|------|------|
| Backend | PHP 8.4, Laravel 12 |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Infra | Docker, Nginx, PHP-FPM |
| DB | MySQL 8.0 |
| Cache/Session | Redis |
| 결제 | 토스페이먼츠 |

## 주요 기능

- 회원가입 / 로그인
- 상품 목록 / 상세 / 카테고리
- 장바구니
- 주문 생성 / 주문 내역
- 토스페이먼츠 결제 연동
- 관리자 상품 CRUD / 주문 관리
- Redis 캐시 / 세션
- 이미지 업로드

## 실행 방법

### 요구사항

- Docker
- Docker Compose

### 시작하기

```bash
# 저장소 클론
git clone https://github.com/kojihun123/devsmile.git
cd devsmile

# 환경변수 설정
cp .env.example .env

# 컨테이너 빌드 및 실행
docker compose up --build

# 마이그레이션
docker compose exec app php artisan migrate --seed
```

브라우저에서 http://localhost 접속

## 컨테이너 구조

```
nginx      - 웹서버 (80)
app        - PHP-FPM (9000)
node       - Vite 개발서버 (5173)
mysql      - 데이터베이스 (3306)
redis      - 캐시 / 세션 (6379)
```
