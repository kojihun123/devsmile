# 서버 배포 가이드 (도메인 없음, IP 기반)

## 사전 준비

- Lightsail Ubuntu 24.04 인스턴스 (2GB RAM 이상)
- Lightsail 방화벽에서 **80, 443 포트** 오픈
- SSH 접속 가능한 상태

---

## 1. SSH 접속

```cmd
ssh -i C:\Users\{유저명}\Downloads\{키파일}.pem ubuntu@{서버IP}
```

---

## 2. Docker 설치

```bash
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker ubuntu && newgrp docker
docker --version  # 버전 확인
```

---

## 3. 저장소 클론

```bash
git clone https://github.com/kojihun123/devsmile.git
cd devsmile
```

---

## 4. nginx 설정에 IP 주입

```bash
sed -i 's/YOUR_DOMAIN/{서버IP}/g' docker/nginx/prod.conf
```

그리고 `prod.conf` 에서 **HTTPS 블록 전체 삭제** (SSL 인증서 없으니까)

```bash
nano docker/nginx/prod.conf
```

아래처럼 HTTP 블록만 남기기:

```nginx
server {
    listen 80;
    server_name {서버IP};
    root /var/www/html/public;
    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

저장: `Ctrl+X` → `Y` → `Enter`

---

## 5. .env 설정

```bash
cp .env.example .env
nano .env
```

수정할 항목:

| 항목 | 값 |
|------|-----|
| `APP_URL` | `http://{서버IP}` |
| `APP_DEBUG` | `true` (초기 디버깅용, 나중에 false로) |
| `DB_PASSWORD` | 원하는 비밀번호 |
| `TOSS_CLIENT_KEY` | 토스 클라이언트 키 |
| `TOSS_SECRET_KEY` | 토스 시크릿 키 |
| `MAIL_USERNAME` | Gmail 주소 |
| `MAIL_PASSWORD` | Gmail 앱 비밀번호 |

저장: `Ctrl+X` → `Y` → `Enter`

---

## 6. APP_KEY 생성

```bash
# composer 의존성 설치
docker run --rm -v $(pwd):/app -w /app composer:2 composer install --no-dev --optimize-autoloader

# APP_KEY 생성 (.env에 자동으로 채워짐)
docker run --rm -v $(pwd):/app -w /app php:8.4-cli php artisan key:generate
```

---

## 7. 필요한 디렉터리 생성

```bash
mkdir -p public/storage storage/app/public docker/certbot/www docker/certbot/conf
```

---

## 8. Docker 이미지 빌드

```bash
docker compose -f docker-compose.prod.yml build
```

> 시간이 좀 걸립니다 (5~10분)

---

## 9. 서비스 시작

```bash
docker compose -f docker-compose.prod.yml up -d
```

컨테이너 확인:

```bash
docker compose -f docker-compose.prod.yml ps
```

---

## 10. public/build 동기화 (CSS/JS)

이미지 안에서 빌드된 assets을 호스트로 복사:

```bash
docker cp devsmile-app-1:/var/www/html/public/build ./public/
```

---

## 11. 마이그레이션 + 시더

```bash
docker compose -f docker-compose.prod.yml exec app php artisan migrate --seed
```

---

## 12. 권한 설정

```bash
docker compose -f docker-compose.prod.yml exec app chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 public/build/
```

---

## 13. 캐시 최적화

```bash
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache
```

---

## 완료

브라우저에서 `http://{서버IP}` 접속!

관리자 계정: `admin@email.com` / `123123`

---

## 업데이트 배포 (코드 변경 시)

```bash
git pull
docker compose -f docker-compose.prod.yml build app queue
docker cp devsmile-app-1:/var/www/html/public/build ./public/
docker compose -f docker-compose.prod.yml up -d
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan optimize
```

---

## 유용한 명령어

```bash
# 로그 확인
docker compose -f docker-compose.prod.yml logs -f app
docker compose -f docker-compose.prod.yml logs -f nginx
docker compose -f docker-compose.prod.yml logs -f queue

# 서비스 재시작
docker compose -f docker-compose.prod.yml restart app

# 전체 종료
docker compose -f docker-compose.prod.yml down

# 전체 종료 + 볼륨 삭제 (DB 초기화)
docker compose -f docker-compose.prod.yml down -v
```
