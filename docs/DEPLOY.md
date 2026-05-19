# 서버 배포 가이드 (도메인 없음, IP 기반)

## 사전 준비

- **AWS Lightsail** Ubuntu 24.04 인스턴스 (2GB RAM / $10 플랜 이상)
- Lightsail 방화벽에서 **80, 443 포트** 오픈
- SSH 키 파일 (`.pem`) 다운로드 완료

---

## AWS Lightsail 인스턴스 생성

1. Lightsail 콘솔 → **Create instance**
2. Region: **Seoul (ap-northeast-2)**
3. Blueprint: **OS Only → Ubuntu 24.04 LTS**
4. Networking type: **Dual-stack**
5. Plan: **$10 (2GB RAM)**
6. 인스턴스 이름 입력 후 생성

### 방화벽 설정

인스턴스 → **Networking** 탭 → **Firewall** → **Add rule**

| Port | Protocol |
|------|----------|
| 80   | TCP |
| 443  | TCP |

---

## 1. SSH 접속

**Windows CMD:**
```cmd
ssh -i C:\Users\{유저명}\Downloads\{키파일}.pem ubuntu@{서버IP}
```

**WSL / Linux / Mac:**
```bash
chmod 400 {키파일}.pem
ssh -i {키파일}.pem ubuntu@{서버IP}
```

---

## 2. Docker 설치

```bash
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker ubuntu && newgrp docker
docker --version
```

---

## 3. 저장소 클론

```bash
git clone https://github.com/kojihun123/devsmile.git
cd devsmile
```

---

## 4. nginx 설정 (IP 기반 HTTP 전용)

```bash
nano docker/nginx/prod.conf
```

전체 내용을 아래로 교체:

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

| 항목 | 값 |
|------|-----|
| `APP_URL` | `http://{서버IP}` |
| `APP_DEBUG` | `true` (초기 디버깅용) |
| `DB_PASSWORD` | 원하는 비밀번호 |
| `TOSS_CLIENT_KEY` | 토스 클라이언트 키 |
| `TOSS_SECRET_KEY` | 토스 시크릿 키 |
| `MAIL_USERNAME` | Gmail 주소 |
| `MAIL_PASSWORD` | Gmail 앱 비밀번호 |

저장: `Ctrl+X` → `Y` → `Enter`

---

## 6. APP_KEY 생성

```bash
docker run --rm -v $(pwd):/app -w /app composer:2 composer install --no-dev --optimize-autoloader
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

> 5~10분 소요

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

Dockerfile 안에서 Vite 빌드가 돌기 때문에 이미지 안의 빌드 결과물을 호스트로 복사해야 nginx가 올바른 CSS/JS를 서빙할 수 있음:

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

# 전체 종료 + DB 초기화
docker compose -f docker-compose.prod.yml down -v
```
