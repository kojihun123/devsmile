#!/bin/bash
set -e

COMPOSE="docker compose -f docker-compose.prod.yml"

echo ""
echo "=============================="
echo " DEVSMILE 업데이트 배포"
echo "=============================="
echo ""

# ── 1. 최신 코드 pull ─────────────────────────────────────────────────────────
echo "[1/5] 최신 코드 pull..."
git pull
echo "      완료"

# ── 2. 이미지 재빌드 ──────────────────────────────────────────────────────────
echo "[2/5] Docker 이미지 빌드 중..."
$COMPOSE build app queue
echo "      완료"

# ── 3. public/build 동기화 ────────────────────────────────────────────────────
echo "[3/5] CSS/JS 빌드 결과물 동기화 중..."
$COMPOSE up -d app
sleep 5
docker cp devsmile-app-1:/var/www/html/public/build ./public/
chmod -R 755 public/build/
sudo chmod -R 755 docker/certbot/
echo "      완료"

# ── 4. 서비스 재시작 ──────────────────────────────────────────────────────────
echo "[4/5] 서비스 재시작..."
$COMPOSE up -d
echo "      완료"

# ── 5. 마이그레이션 + 캐시 갱신 ──────────────────────────────────────────────
echo "[5/5] 마이그레이션 + 캐시 갱신..."
$COMPOSE exec app php artisan migrate --force
$COMPOSE exec app php artisan optimize
echo "      완료"

echo ""
echo "=============================="
echo " 업데이트 완료!"
echo "=============================="
echo ""
