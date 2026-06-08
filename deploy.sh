#!/bin/bash
set -e

DOMAIN=${1:?'사용법: ./deploy.sh <도메인>  예) ./deploy.sh devsmile.org'}
COMPOSE="docker compose -f docker-compose.prod.yml"

echo ""
echo "=============================="
echo " DEVSMILE 배포 시작"
echo " 도메인: $DOMAIN"
echo "=============================="
echo ""

# ── 1. .env 확인 ─────────────────────────────────────────────────────────────
if [ ! -f .env ]; then
    echo "[1/6] .env 파일이 없습니다. .env.example을 복사합니다."
    cp .env.example .env
    echo "      .env 파일을 편집한 뒤 다시 실행하세요."
    exit 1
fi
echo "[1/6] .env 확인 완료"

# ── 2. 필요한 디렉터리 생성 ───────────────────────────────────────────────────
echo "[2/6] 디렉터리 생성..."
mkdir -p public/storage storage/app/public
echo "      완료"

# ── 3. Docker 이미지 빌드 ─────────────────────────────────────────────────────
echo "[3/6] Docker 이미지 빌드 중..."
APP_DOMAIN=$DOMAIN $COMPOSE build
echo "      완료"

# ── 4. public/build 동기화 ────────────────────────────────────────────────────
echo "[4/6] CSS/JS 빌드 결과물 동기화 중..."
APP_DOMAIN=$DOMAIN $COMPOSE up -d app mysql redis
sleep 5
docker cp devsmile-app-1:/var/www/html/public/build ./public/
chmod -R 755 public/build/
APP_DOMAIN=$DOMAIN $COMPOSE down
echo "      완료"

# ── 5. 전체 서비스 시작 ───────────────────────────────────────────────────────
echo "[5/6] 전체 서비스 시작..."
APP_DOMAIN=$DOMAIN $COMPOSE up -d

echo "      MySQL 준비 대기 중..."
sleep 10

# ── 6. 마이그레이션 + 권한 + 캐시 ────────────────────────────────────────────
echo "[6/6] 마이그레이션 + 권한 + 캐시..."
$COMPOSE exec app php artisan migrate --seed --force
$COMPOSE exec app chown -R www-data:www-data storage bootstrap/cache
$COMPOSE exec app php artisan optimize
echo "      완료"

echo ""
echo "=============================="
echo " 배포 완료!"
echo " https://$DOMAIN"
echo "=============================="
echo ""
echo "관리자 계정: admin@email.com / 123123"
echo ""
