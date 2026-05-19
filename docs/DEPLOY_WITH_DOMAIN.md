# 서버 배포 가이드 (도메인 + HTTPS)

## 사전 준비

- Lightsail Ubuntu 24.04 인스턴스 (2GB RAM 이상)
- Lightsail 방화벽에서 **80, 443 포트** 오픈
- 도메인 구입 완료 + DNS A 레코드가 서버 IP를 가리키는 상태
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
docker --version
```

---

## 3. 저장소 클론

```bash
git clone https://github.com/kojihun123/devsmile.git
cd devsmile
```

---

## 4. .env 설정

```bash
cp .env.example .env
nano .env
```

수정할 항목:

| 항목 | 값 |
|------|-----|
| `APP_URL` | `https://devsmile.org` |
| `APP_DEBUG` | `false` |
| `DB_PASSWORD` | 원하는 비밀번호 |
| `TOSS_CLIENT_KEY` | 토스 클라이언트 키 |
| `TOSS_SECRET_KEY` | 토스 시크릿 키 |
| `MAIL_USERNAME` | Gmail 주소 |
| `MAIL_PASSWORD` | Gmail 앱 비밀번호 |

저장: `Ctrl+X` → `Y` → `Enter`

---

## 5. APP_KEY 생성

```bash
docker run --rm -v $(pwd):/app -w /app composer:2 composer install --no-dev --optimize-autoloader
docker run --rm -v $(pwd):/app -w /app php:8.4-cli php artisan key:generate
```

---

## 6. 필요한 디렉터리 생성

```bash
mkdir -p public/storage storage/app/public docker/certbot/www docker/certbot/conf
```

---

## 7. Docker 이미지 빌드

```bash
docker compose -f docker-compose.prod.yml build
```

---

## 8. public/build 동기화 (CSS/JS)

```bash
docker compose -f docker-compose.prod.yml up -d app mysql redis
docker cp devsmile-app-1:/var/www/html/public/build ./public/
docker compose -f docker-compose.prod.yml down
```

---

## 9. SSL 인증서 발급 (최초 1회)

init.conf (HTTP 전용) 로 nginx 임시 시작:

```bash
docker run --rm -d \
    --name nginx_init \
    -p 80:80 \
    -v "$(pwd)/docker/nginx/init.conf:/etc/nginx/conf.d/default.conf" \
    -v "$(pwd)/docker/certbot/www:/var/www/certbot" \
    -v "$(pwd)/public:/var/www/html/public:ro" \
    nginx:alpine
```

인증서 발급:

```bash
docker run --rm \
    -v "$(pwd)/docker/certbot/www:/var/www/certbot" \
    -v "$(pwd)/docker/certbot/conf:/etc/letsencrypt" \
    certbot/certbot certonly \
    --webroot \
    --webroot-path=/var/www/certbot \
    --email {이메일} \
    --agree-tos \
    --no-eff-email \
    -d devsmile.org
```

임시 nginx 종료:

```bash
docker stop nginx_init
```

---

## 10. prod.conf 도메인 설정

```bash
sed -i 's/YOUR_DOMAIN/devsmile.org/g' docker/nginx/prod.conf
```

---

## 11. 전체 서비스 시작

```bash
docker compose -f docker-compose.prod.yml up -d
```

컨테이너 확인:

```bash
docker compose -f docker-compose.prod.yml ps
```

---

## 12. 마이그레이션 + 시더

```bash
docker compose -f docker-compose.prod.yml exec app php artisan migrate --seed
```

---

## 13. 권한 설정

```bash
docker compose -f docker-compose.prod.yml exec app chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 public/build/
```

---

## 14. 캐시 최적화

```bash
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache
```

---

## 완료

브라우저에서 `https://devsmile.org` 접속!

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

# 인증서 수동 갱신
docker compose -f docker-compose.prod.yml exec certbot certbot renew
```
