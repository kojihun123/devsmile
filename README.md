# DEVSMILE

개발자 감성 밈/굿즈 쇼핑몰 포트폴리오 프로젝트

> "404 행복을 찾을 수 없습니다" 같은 상품을 파는 유쾌한 개발자 감성 쇼핑몰

**라이브 데모**: https://devsmile.org

> 토스페이먼츠 테스트 환경으로 실제 결제가 발생하지 않습니다.

---

## Tech Stack

| 분류 | 기술 |
|------|------|
| Backend | PHP 8.4, Laravel 12 |
| Frontend | Blade, Tailwind CSS, Vite |
| Infra | Docker, Nginx, PHP-FPM, AWS Lightsail |
| DB | MySQL 8.0 |
| Cache/Session | Redis |
| Queue | Laravel Queue (Redis driver) |
| 결제 | 토스페이먼츠 위젯 SDK v2 |
| 이메일 | Gmail SMTP + Laravel Mailable |

---

## 주요 기능

- 회원가입 / 로그인 (전화번호 포함)
- 상품 목록 / 상세 / 카테고리 필터
- 장바구니 (수량 변경, 재고 방어)
- 주문 생성 → 토스페이먼츠 결제
- 결제 멱등성 (DB Lock) + 재고 0 이하 방어 + 결제 자동 취소
- 회원/비회원 주문 조회
- 배송 시뮬레이션 이메일 (주문확인 → 배송중 → 배달완료 + 이미지 첨부)
- 관리자 패널: 카테고리/상품/주문 CRUD + 상태 필터
- Redis 캐시 / 세션 / Queue
- Let's Encrypt HTTPS 자동 갱신

---

## 로컬 개발 환경

### 요구사항

- Docker & Docker Compose

### 시작하기

```bash
# 저장소 클론
git clone https://github.com/kojihun123/devsmile.git
cd devsmile

# 환경변수 설정
cp .env.example .env
# .env 편집: DB_PASSWORD, TOSS_* 키, MAIL_* 설정

# APP_KEY 생성
docker run --rm -v $(pwd):/app -w /app php:8.4-cli php artisan key:generate

# 컨테이너 빌드 및 실행
docker compose up --build -d

# 마이그레이션 + 시더 (관리자 계정 생성)
docker compose exec app php artisan migrate --seed

# 스토리지 링크
docker compose exec app php artisan storage:link
```

브라우저에서 http://localhost 접속

### 컨테이너 구조 (개발)

```
nginx    - 웹서버 (80)
app      - PHP-FPM (9000)
node     - Vite 개발서버 HMR (5173)
mysql    - 데이터베이스 (3306)
redis    - 캐시 / 세션 / Queue (6379)
queue    - Laravel Queue Worker
```

---

## 프로덕션 배포

### 서버 요구사항

- AWS Lightsail Ubuntu 24.04 (2GB RAM / $10 플랜 이상)
- Docker & Docker Compose
- 도메인 + Let's Encrypt SSL

자세한 배포 가이드는 [docs/DEPLOY_WITH_DOMAIN.md](docs/DEPLOY_WITH_DOMAIN.md) 참고

### 최초 배포

```bash
git clone https://github.com/kojihun123/devsmile.git
cd devsmile

cp .env.example .env
# .env 편집

chmod +x deploy.sh
./deploy.sh devsmile.org admin@devsmile.org
```

### 업데이트 배포

```bash
chmod +x deploy_update.sh
./deploy_update.sh
```

### 컨테이너 구조 (프로덕션)

```
nginx    - 웹서버 (80 HTTP→HTTPS 리다이렉트, 443 HTTPS)
app      - PHP-FPM
mysql    - 데이터베이스 (외부 미노출)
redis    - 캐시 / 세션 / Queue (외부 미노출)
queue    - Laravel Queue Worker
certbot  - Let's Encrypt 인증서 자동 갱신 (12시간마다)
```

---

## 환경변수

| 변수 | 설명 |
|------|------|
| `APP_KEY` | Laravel 암호화 키 (`php artisan key:generate`) |
| `APP_URL` | 서비스 URL (`https://devsmile.org`) |
| `TOSS_CLIENT_KEY` | 토스페이먼츠 클라이언트 키 |
| `TOSS_SECRET_KEY` | 토스페이먼츠 시크릿 키 |
| `MAIL_USERNAME` | Gmail 주소 |
| `MAIL_PASSWORD` | Gmail 앱 비밀번호 ([발급 방법](https://support.google.com/accounts/answer/185833)) |
