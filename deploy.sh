#!/bin/bash
set -e

DOMAIN=${1:?'사용법: ./deploy.sh <도메인> <이메일>  예) ./deploy.sh devsmile.org admin@devsmile.org'}
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
    echo "[1/9] .env 파일이 없습니다. .env.example을 복사합니다."
    cp .env.example .env
    echo "      .env 파일을 편집한 뒤 다시 실행하세요."
    exit 1
fi
echo "[1/9] .env 확인 완료"

# ── 2. 필요한 디렉터리 생성 ───────────────────────────────────────────────────
echo "[2/9] 디렉터리 생성..."
mkdir -p public/storage storage/app/public docker/certbot/www docker/certbot/conf
echo "      완료"

# ── 3. Docker 이미지 빌드 ─────────────────────────────────────────────────────
echo "[3/9] Docker 이미지 빌드 중..."
$COMPOSE build
echo "      완료"

# ── 4. public/build 동기화 ────────────────────────────────────────────────────
echo "[4/9] CSS/JS 빌드 결과물 동기화 중..."
$COMPOSE up -d app mysql redis
sleep 5
docker cp devsmile-app-1:/var/www/html/public/build ./public/
$COMPOSE down
echo "      완료"

# ── 5. prod.conf 도메인 설정 ──────────────────────────────────────────────────
echo "[5/9] nginx 도메인 설정..."
sed -i "s/YOUR_DOMAIN/$DOMAIN/g" docker/nginx/prod.conf
echo "      완료"

# ── 6. 임시 nginx 시작 + SSL 인증서 발급 ──────────────────────────────────────
echo "[6/9] SSL 인증서 발급 중..."

# app 먼저 시작 (nginx가 upstream app을 찾아야 함)
$COMPOSE up -d app mysql redis

# 임시 nginx 시작 (같은 Docker 네트워크에 연결)
docker run --rm -d \
    --name nginx_init \
    -p 80:80 \
    --network devsmile_devsmile \
    -v "$(pwd)/docker/nginx/init.conf:/etc/nginx/conf.d/default.conf" \
    -v "$(pwd)/docker/certbot/www:/var/www/certbot" \
    -v "$(pwd)/public:/var/www/html/public:ro" \
    nginx:alpine

# Let's Encrypt 인증서 발급
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

# 임시 nginx + 기존 서비스 종료
docker stop nginx_init
$COMPOSE down
echo "      인증서 발급 완료"

# ── 7. 전체 서비스 시작 ───────────────────────────────────────────────────────
echo "[7/9] 전체 서비스 시작..."
$COMPOSE up -d

echo "      MySQL 준비 대기 중..."
sleep 10

# ── 8. 마이그레이션 + 권한 설정 ──────────────────────────────────────────────
echo "[8/9] 마이그레이션 + 권한 설정..."
$COMPOSE exec app php artisan migrate --seed --force
$COMPOSE exec app chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 public/build/
echo "      완료"

# ── 9. 캐시 최적화 ────────────────────────────────────────────────────────────
echo "[9/9] 캐시 최적화..."
$COMPOSE exec app php artisan config:cache
$COMPOSE exec app php artisan route:cache
$COMPOSE exec app php artisan view:cache
echo "      완료"

# ── 완료 ──────────────────────────────────────────────────────────────────────
echo ""
echo "=============================="
echo " 배포 완료!"
echo " https://$DOMAIN"
echo "=============================="
echo ""
echo "관리자 계정: admin@email.com / 123123"
echo ""
