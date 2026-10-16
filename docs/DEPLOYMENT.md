# Deploy yo‘riqnomasi — Besqala Mining Valley

Ushbu hujjat loyihani **ishlab chiqarish (production)** serveriga joylashtirish
bo‘yicha to‘liq qadamlarni beradi. Namuna muhit: **Ubuntu 22.04 + Nginx +
PHP‑FPM 8.3 + MySQL 8**. (Boshqa muhitlar — Laravel Forge, Ploi, Docker,
cPanel — uchun ham shu tamoyillar amal qiladi.)

---

## 1. Server talablari

| Komponent | Versiya / izoh |
|-----------|----------------|
| PHP | **8.3+**, kengaytmalar: `bcmath, ctype, curl, dom, fileinfo, gd, mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, zip` |
| Composer | 2.x |
| Node.js | **18+** (faqat `npm run build` uchun; build’ni lokalda qilib, natijani deploy qilsa ham bo‘ladi) |
| Ma’lumotlar bazasi | MySQL 8 yoki MariaDB 10.6+ |
| Web-server | Nginx (yoki Apache) |

> **GD** kengaytmasi QR-kod generatsiyasi uchun majburiy.

PHP kengaytmalarini o‘rnatish (Ubuntu):

```bash
sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-gd \
  php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath \
  nginx mysql-server git unzip
```

---

## 2. Ma’lumotlar bazasi

```sql
CREATE DATABASE besqala CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'besqala'@'localhost' IDENTIFIED BY 'KUCHLI_PAROL';
GRANT ALL PRIVILEGES ON besqala.* TO 'besqala'@'localhost';
FLUSH PRIVILEGES;
```

---

## 3. Kodni joylashtirish

```bash
cd /var/www
sudo git clone https://github.com/dawrandev/besqala-mining.git
sudo chown -R $USER:www-data besqala-mining
cd besqala-mining

composer install --no-dev --optimize-autoloader
```

Frontend assetlari (serverda Node bor bo‘lsa):

```bash
npm ci
npm run build
```

> Serverda Node yo‘q bo‘lsa: `npm run build` ni **lokalda** bajarib, hosil
> bo‘lgan `public/build/` papkasini serverga yuklang (git’ga kiritmaydi).

---

## 4. Muhit (.env)

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

Muhim sozlamalar:

```dotenv
APP_NAME="Besqala Mining Valley"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://besqala.uz

APP_LOCALE=uz
APP_FALLBACK_LOCALE=uz

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=besqala
DB_USERNAME=besqala
DB_PASSWORD=KUCHLI_PAROL

SESSION_DRIVER=database   # yoki redis
CACHE_STORE=database      # yoki redis
QUEUE_CONNECTION=database # fon vazifalari bo‘lsa
```

> `APP_DEBUG=false` va kuchli `APP_KEY` — xavfsizlik uchun majburiy.

> **`APP_URL` to‘g‘ri va HTTPS bo‘lishi juda muhim:** guvohnoma PDF’idagi QR-kod
> va ommaviy verify havolalari aynan shu manzilga asoslanadi. Noto‘g‘ri bo‘lsa
> QR ishlamaydi.

> **Email (mailto):** ilova pochtani o‘zi yubormaydi, shuning uchun **SMTP
> sozlash shart emas**. «Rezidentga yuborish» tugmasi xodimning o‘z pochta
> dasturini `mailto:` orqali ochadi. `MAIL_*` sozlamalarini standart holicha
> qoldirsangiz bo‘ladi.

---

## 5. Migratsiya va fayllar

```bash
php artisan migrate --force          # production’da --force kerak

# Demo ma’lumotlar KERAK BO‘LSAGINA (odatda production’da keraksiz):
# php artisan migrate:fresh --seed --force

php artisan storage:link             # agar foydalanuvchi fayllari bo‘lsa
```

> Rasmiy berilgan guvohnoma PDF’lari **privat diskka** — `storage/app/documents`
> ga muzlatiladi (SHA-256 bilan). Papka avtomatik yaratiladi; u faqat
> himoyalangan controller orqali beriladi (ommaviy URL yo‘q). Yagona talab —
> `storage` ga yozish ruxsati (7-bo‘limga qarang). Bu fayllarni **zaxira
> nusxalashga** (backup) kiriting — ular hujjatlarning o‘zgarmas asl nusxasi.

---

## 6. Keshlash (production optimizatsiyasi)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

> Har deploydan keyin shu buyruqlarni qayta ishga tushiring (quyidagi
> yangilash skriptiga kiritilgan).

---

## 7. Fayl ruxsatlari

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

> Bu `storage/app/documents` (muzlatilgan guvohnoma PDF’lari) uchun ham yozish
> ruxsatini beradi — alohida qadam shart emas.

---

## 8. Nginx konfiguratsiyasi

`/etc/nginx/sites-available/besqala`:

```nginx
server {
    listen 80;
    server_name besqala.uz www.besqala.uz;
    root /var/www/besqala-mining/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }

    client_max_body_size 20M;
}
```

Yoqish:

```bash
sudo ln -s /etc/nginx/sites-available/besqala /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

## 9. HTTPS (Let’s Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d besqala.uz -d www.besqala.uz
```

Certbot avtomatik yangilanishni (renew) o‘rnatadi.

---

## 10. Scheduler va Queue (ixtiyoriy)

Rejalashtirilgan vazifalar uchun cron (har daqiqa):

```bash
* * * * * cd /var/www/besqala-mining && php artisan schedule:run >> /dev/null 2>&1
```

Navbat (queue) ishchisi — `QUEUE_CONNECTION=database` bo‘lsa, systemd xizmati:

`/etc/systemd/system/besqala-worker.service`:

```ini
[Unit]
Description=Besqala queue worker
After=network.target

[Service]
User=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/besqala-mining/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable --now besqala-worker
```

> Hozirgi versiyada og‘ir fon vazifalari yo‘q; PDF to‘g‘ridan-to‘g‘ri
> generatsiya qilinadi. Queue keyinchalik (masalan, email xabarnomalar)
> uchun foydali bo‘ladi.

---

## 11. Yangilash (keyingi deploylar)

```bash
cd /var/www/besqala-mining
php artisan down                       # (ixtiyoriy) saytni vaqtincha o‘chirish

git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build                # yoki tayyor build’ni yuklash

php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache

# agar queue ishchisi bo‘lsa, yangi kodni olishi uchun:
sudo systemctl restart besqala-worker  # yoki: php artisan queue:restart

php artisan up
```

---

## 12. Tekshiruv (deploydan keyin)

- [ ] Bosh sahifa ochiladi, UZ/RU/EN til almashadi
- [ ] Kirish ishlaydi (demo o‘chirilgan bo‘lsa — haqiqiy hisob bilan)
- [ ] Ariza topshirish → workflow bosqichlari ishlaydi (AJAX, sahifa qayta yuklanmaydi)
- [ ] Vakolatli organ ruxsatnoma beradi → guvohnoma **DRAFT** yaratiladi
- [ ] Direksiya **«Rasmiy berish»** bosadi → guvohnoma ACTIVE, PDF muzlatiladi
- [ ] Guvohnoma PDF yuklab olinadi (3 til), QR ommaviy reestr yozuvi sahifasiga olib boradi
- [ ] Reestr sahifasi 21-band ma’lumotlari + status tarixini ko‘rsatadi
- [ ] Hayotiy sikl: to‘xtatib turish / tiklash / qayta rasmiylashtirish ishlaydi
- [ ] «Rezidentga yuborish (email)» xodimning pochta dasturini ochadi (mailto)
- [ ] `storage/app/documents` da PDF fayllar saqlanmoqda
- [ ] `storage/logs/laravel.log` da xatolar yo‘q
- [ ] HTTPS sertifikati amal qiladi

---

## 13. Muammolarni bartaraf etish / Troubleshooting

| Alomat | Sabab / yechim |
|--------|----------------|
| 500 xato, bo‘sh sahifa | `storage` ruxsatlari; `php artisan config:clear`; `.env` `APP_KEY` |
| CSS/JS yuklanmaydi | `npm run build` bajarilmagan yoki `public/build/` yo‘q; `APP_URL` noto‘g‘ri |
| QR-kod chiqmaydi | PHP **gd** kengaytmasi o‘rnatilmagan |
| PDF xato beradi | `dompdf` uchun yozish ruxsati; xotira limiti (`memory_limit`) |
| Guvohnoma PDF saqlanmaydi / 500 | `storage/app/documents` ga yozish ruxsati (`chown`/`chmod`) |
| QR noto‘g‘ri manzilga olib boradi | `APP_URL` xato — QR shunga asoslanadi; tuzatib `config:cache` |
| «Rezidentga yuborish» hech narsa qilmaydi | bu `mailto:` — foydalanuvchining pochta dasturi o‘rnatilgan bo‘lishi kerak |
| AJAX amallari ishlamaydi (419/CSRF) | `<meta name="csrf-token">` + sessiya; `APP_URL`/cookie domeni; `config:cache` |
| Sana/oy noto‘g‘ri tilda | `APP_LOCALE`/`APP_FALLBACK_LOCALE` tekshiring (uz → Carbon `uz_Latn`) |
| 419 Page Expired | `SESSION_DRIVER`/`APP_URL`/cookie domeni; `php artisan config:cache` |

---

**Minimal deploy (bitta blok):**

```bash
git clone https://github.com/dawrandev/besqala-mining.git && cd besqala-mining
composer install --no-dev --optimize-autoloader
npm ci && npm run build
cp .env.example .env && php artisan key:generate
# .env ni tahrirlang (DB, APP_URL, APP_ENV=production, APP_DEBUG=false)
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache
```
