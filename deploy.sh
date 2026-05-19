#!/bin/bash
set -e

DOMAIN=${1:?'사용법: ./deploy.sh <도메인> <이메일>  예) ./deploy.sh example.com admin@example.com'}
EMAIL=${2:?'이메일 주소를 입력하세요.'}
COMPOSE="docker compose -f docker-compose.prod.yml"

echo ""
echo "=============================="
echo " DEVSMILE 배포 시작"
echo " 도메인: $DOMAIN"
echo " 이메일: $EMAIL"
echo "=============================="
echo ""

# ── 1. .env 확인 ─────────────────────────────────────────────────────────────
if [ ! -f .env ]; then
    echo "[1/8] .env 파일이 없습니다. .env.example을 복사합니다."
    cp .env.example .env
    echo "      .env 파일을 편집한 뒤 다시 실행하세요."
    exit 1
fi
echo "[1/8] .env 확인 완료"

# ── 2. Vite 빌드 (public/build 생성) ─────────────────────────────────────────
echo "[2/8] Vite 프론트엔드 빌드 중..."
docker run --rm \
    -v "$(pwd):/app" \
    -w /app \
    node:22-alpine \
    sh -c "npm ci --quiet && npm run build"
echo "      public/build 생성 완료"

# ── 3. prod.conf 도메인 설정 ──────────────────────────────────────────────────
echo "[3/8] nginx prod.conf 도메인 설정..."
sed -i "s/YOUR_DOMAIN/$DOMAIN/g" docker/nginx/prod.conf
echo "      도메인 '$DOMAIN' 적용 완료"

# ── 4. Docker 이미지 빌드 ─────────────────────────────────────────────────────
echo "[4/8] Docker 이미지 빌드 중..."
$COMPOSE build --no-cache app queue
echo "      이미지 빌드 완료"

# ── 5. init.conf 로 nginx 시작 (certbot challenge용) ─────────────────────────
echo "[5/8] 초기 HTTP nginx 시작 (SSL 발급 전)..."
mkdir -p docker/certbot/www docker/certbot/conf

docker run --rm -d \
    --name devsmile_nginx_init \
    -p 80:80 \
    -v "$(pwd)/docker/nginx/init.conf:/etc/nginx/conf.d/default.conf" \
    -v "$(pwd)/docker/certbot/www:/var/www/certbot" \
    -v "$(pwd)/public:/var/www/html/public:ro" \
    nginx:alpine

# ── 6. Let's Encrypt 인증서 발급 ─────────────────────────────────────────────
echo "[6/8] Let's Encrypt 인증서 발급 중..."
docker run --rm \
    -v "$(pwd)/docker/certbot/www:/var/www/certbot" \
    -v "$(pwd)/docker/certbot/conf:/etc/letsencrypt" \
    certbot/certbot certonly \
    --webroot \
    --webroot-path=/var/www/certbot \
    --email "$EMAIL" \
    --agree-tos \
    --no-eff-email \
    -d "$DOMAIN"

# 임시 nginx 종료
docker stop devsmile_nginx_init

echo "      인증서 발급 완료"

# ── 7. 전체 서비스 시작 ───────────────────────────────────────────────────────
echo "[7/8] 전체 서비스 시작..."
$COMPOSE up -d

# DB 준비 대기
echo "      MySQL 준비 대기 중..."
sleep 10

# 마이그레이션
$COMPOSE exec app php artisan migrate --force
echo "      마이그레이션 완료"

# 캐시 최적화
$COMPOSE exec app php artisan config:cache
$COMPOSE exec app php artisan route:cache
$COMPOSE exec app php artisan view:cache
echo "      캐시 최적화 완료"

# ── 8. 완료 ──────────────────────────────────────────────────────────────────
echo ""
echo "=============================="
echo " 배포 완료!"
echo " https://$DOMAIN"
echo "=============================="
echo ""
echo "관리자 계정: admin@devsmile.com / password"
echo "(최초 1회 시더 실행 필요: docker compose -f docker-compose.prod.yml exec app php artisan db:seed)"
echo ""
