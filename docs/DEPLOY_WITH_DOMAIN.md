# 서버 배포 가이드 (도메인 + HTTPS)

## 사전 준비

- **AWS Lightsail** Ubuntu 24.04 인스턴스 (2GB RAM / $10 플랜 이상)
- Lightsail 방화벽에서 **80, 443 포트** 오픈
- **도메인 구입 완료** (AWS Route 53 권장)
- DNS A 레코드가 서버 IP를 가리키는 상태 (`nslookup {도메인}` 으로 확인)
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

## Route 53 도메인 DNS 연결

1. Route 53 콘솔 → **Hosted zones** → 도메인 클릭
2. **Create record**
   - Record type: **A**
   - Value: `{서버IP}`
   - TTL: 300
3. DNS 전파 확인 (5~30분 소요):

```bash
nslookup {도메인}
# 서버 IP가 나오면 완료
```

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

## 4. .env 설정

```bash
cp .env.example .env
nano .env
```

| 항목 | 값 |
|------|-----|
| `APP_URL` | `https://{도메인}` |
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

## 7. Docker 이미지 빌드 + public/build 동기화

```bash
docker compose -f docker-compose.prod.yml build
docker compose -f docker-compose.prod.yml up -d app mysql redis
docker cp devsmile-app-1:/var/www/html/public/build ./public/
docker compose -f docker-compose.prod.yml down
```

> Vite 빌드 결과물 해시값 불일치 방지를 위해 이미지에서 호스트로 복사

---

## 8. SSL 인증서 발급 (최초 1회)

### 임시 nginx 시작 (app과 같은 네트워크에 연결)

```bash
docker compose -f docker-compose.prod.yml up -d app mysql redis
```

```bash
docker run --rm -d --name nginx_init -p 80:80 --network devsmile_devsmile -v "$(pwd)/docker/nginx/init.conf:/etc/nginx/conf.d/default.conf" -v "$(pwd)/docker/certbot/www:/var/www/certbot" -v "$(pwd)/public:/var/www/html/public:ro" nginx:alpine
```

### 인증서 발급

```bash
docker run --rm -v "$(pwd)/docker/certbot/www:/var/www/certbot" -v "$(pwd)/docker/certbot/conf:/etc/letsencrypt" certbot/certbot certonly --webroot --webroot-path=/var/www/certbot --email {이메일} --agree-tos --no-eff-email -d {도메인}
```

### 임시 nginx 종료 + 전체 서비스 종료

```bash
docker stop nginx_init
docker compose -f docker-compose.prod.yml down
```

---

## 9. prod.conf 도메인 설정

```bash
sed -i 's/YOUR_DOMAIN/{도메인}/g' docker/nginx/prod.conf
```

---

## 10. 전체 서비스 시작

```bash
docker compose -f docker-compose.prod.yml up -d
```

컨테이너 확인:

```bash
docker compose -f docker-compose.prod.yml ps
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

브라우저에서 `https://{도메인}` 접속!

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

# 인증서 수동 갱신
docker compose -f docker-compose.prod.yml exec certbot certbot renew
```
