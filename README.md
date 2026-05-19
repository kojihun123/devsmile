# DEVSMILE

개발자 감성 밈/굿즈 쇼핑몰 포트폴리오 프로젝트

> "404 행복을 찾을 수 없습니다" 같은 상품을 파는 유쾌한 개발자 감성 쇼핑몰

## Tech Stack

| 분류 | 기술 |
|------|------|
| Backend | PHP 8.4, Laravel 12 |
| Frontend | Blade, Tailwind CSS, Vite |
| Infra | Docker, Nginx, PHP-FPM |
| DB | MySQL 8.0 |
| Cache/Session | Redis |
| Queue | Laravel Queue (Redis driver) |
| 결제 | 토스페이먼츠 위젯 SDK v2 |
| 이메일 | Gmail SMTP + Laravel Mailable |

## 주요 기능

- 회원가입 / 로그인 (전화번호 포함)
- 상품 목록 / 상세 / 카테고리 필터
- 장바구니 (수량 변경, 재고 방어)
- 주문 생성 → 토스페이먼츠 결제
- 결제 멱등성 (DB Lock) + 재고 0 이하 방어 + 결제 자동 취소
- 회원/비회원 주문 조회
- 배송 시뮬레이션 이메일 (주문확인 → 배송중 → 배달완료)
- 배달완료 이메일에 상품 이미지 첨부
- 관리자 패널: 카테고리/상품/주문 CRUD + 상태 필터
- Redis 캐시 / 세션 / Queue

---

## 로컬 개발 환경

### 요구사항

- Docker & Docker Compose

### 시작하기

```bash
# 저장소 클론
git clone https://github.com/yourname/devsmile.git
cd devsmile

# 환경변수 설정
cp .env.example .env
# .env 편집: APP_KEY, DB_PASSWORD, TOSS_* 키, MAIL_* 설정

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

- Ubuntu 22.04 LTS 이상
- Docker, Docker Compose v2
- 공인 IP + 도메인 연결 (Let's Encrypt SSL 발급 필요)
- 최소 사양: 1 vCPU, 2GB RAM

### 최초 배포 (1회)

```bash
# 1. 서버에서 클론
git clone https://github.com/yourname/devsmile.git
cd devsmile

# 2. 환경변수 설정
cp .env.example .env
vi .env   # 모든 값 채우기 (APP_KEY, DB, TOSS, MAIL, APP_URL)

# 3. APP_KEY 생성
docker run --rm -v $(pwd):/app -w /app php:8.4-cli php artisan key:generate

# 4. 배포 스크립트 실행 (도메인, 이메일 입력)
chmod +x deploy.sh
./deploy.sh example.com admin@example.com

# 5. 최초 시더 (관리자 계정 생성)
docker compose -f docker-compose.prod.yml exec app php artisan db:seed
```

### 업데이트 배포 (코드 변경 시)

```bash
git pull

# Vite 재빌드 (CSS/JS 변경 시)
docker run --rm -v "$(pwd):/app" -w /app node:22-alpine \
    sh -c "npm ci --quiet && npm run build"

# 이미지 재빌드 + 재시작
docker compose -f docker-compose.prod.yml build app queue
docker compose -f docker-compose.prod.yml up -d

# 마이그레이션 (스키마 변경 시)
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# 캐시 갱신
docker compose -f docker-compose.prod.yml exec app php artisan optimize
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

### 볼륨 / 데이터 영속성

| 경로 | 용도 |
|------|------|
| `./storage/` | 업로드 파일, 로그, 캐시 |
| `./public/build/` | Vite 빌드 결과물 |
| `./docker/certbot/` | SSL 인증서 |
| `mysql_data` (named volume) | MySQL 데이터 |

### 유용한 명령어

```bash
COMPOSE="docker compose -f docker-compose.prod.yml"

# 로그 확인
$COMPOSE logs -f app
$COMPOSE logs -f queue

# 서비스 재시작
$COMPOSE restart app

# DB 접속
$COMPOSE exec mysql mysql -u devsmile -p devsmile

# Queue 상태
$COMPOSE exec app php artisan queue:monitor

# 인증서 수동 갱신
$COMPOSE exec certbot certbot renew
```

---

## 환경변수 설명

| 변수 | 설명 |
|------|------|
| `APP_KEY` | Laravel 암호화 키 (`php artisan key:generate`) |
| `APP_URL` | 서비스 URL (`https://example.com`) |
| `TOSS_CLIENT_KEY` | 토스페이먼츠 클라이언트 키 |
| `TOSS_SECRET_KEY` | 토스페이먼츠 시크릿 키 |
| `MAIL_USERNAME` | Gmail 주소 |
| `MAIL_PASSWORD` | Gmail 앱 비밀번호 ([발급 방법](https://support.google.com/accounts/answer/185833)) |
