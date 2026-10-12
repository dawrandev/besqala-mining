# Besqala Mining Valley

> Maxsus mayning zonasi rezidentlarini ro‘yxatdan o‘tkazish, ruxsatnomalar
> berish va faoliyatni nazorat qilishning yagona raqamli platformasi.
>
> A single digital platform for registering residents of a special crypto-mining
> zone, issuing permits, and overseeing their activity — built for the
> Karakalpakstan special mining zone (Resolution PQ‑143, 17.04.2026).

Laravel 13 · Livewire 4 · Tailwind CSS v4 · Alpine.js · ApexCharts

---

## Mundarija / Contents

- [Imkoniyatlar / Features](#imkoniyatlar--features)
- [Texnologiyalar / Tech stack](#texnologiyalar--tech-stack)
- [Rollar va demo hisoblar / Roles & demo accounts](#rollar-va-demo-hisoblar--roles--demo-accounts)
- [Ariza workflow / Application workflow](#ariza-workflow--application-workflow)
- [Lokal o‘rnatish / Local setup](#lokal-ornatish--local-setup)
- [Test va sifat / Testing & quality](#test-va-sifat--testing--quality)
- [Loyiha tuzilishi / Project structure](#loyiha-tuzilishi--project-structure)
- [Ko‘p tillilik / Localization](#kop-tillilik--localization)
- [Deploy](#deploy)
- [Hujjatlar / Docs](#hujjatlar--docs)

---

## Imkoniyatlar / Features

**Ochiq (public) sayt** — zamonaviy emerald/teal dizayn:
- Bosh sahifa (hero, xizmat ko‘rsatkichlari, ruxsat etilgan energiya turlari,
  rezident majburiyatlari / taqiqlar, jarayon bosqichlari, yangiliklar, CTA)
- Biz haqimizda (missiya, strategik vazifa, qadriyatlar, tarix, bog‘lanish)
- Rahbariyat · Yangiliklar (kategoriyalar bilan) · **Ochiq reestr** (qidiruv + filtr)
- **Hujjatni tekshirish** — guvohnoma/ruxsatnoma raqami yoki QR orqali

**Boshqaruv panellari (TailAdmin dizayni)** — 5 rol:
- Rol asosidagi sidebar, qidiruv, dark-mode, bildirishnoma va profil menyulari
- Dashboardlarda **ApexCharts** grafiklar (oylik arizalar, status taqsimoti)
- Ariza detali: **progress stepper**, tabli bo‘limlar (Ma’lumotlar / Rahbariyat /
  Uskunalar / Hujjatlar), **QR-kodli guvohnoma preview** kartasi
- To‘liq ariza workflow’i (quyida)

**Hujjatlar**
- Guvohnoma, ruxsatnoma, ekspert xulosasi, kamchilik xabarnomasi, dalolatnoma
- **QR-kodli PDF** (dompdf + endroid/qr-code), ommaviy va egaga yuklab olish
- Har bir hujjat raqam + QR orqali ochiq tekshiriladi

**Boshqa**
- Trilingual interfeys (uz/ru/en) + trilingual kontent (JSON ustunlar)
- Karakalpakstanning 15 tumani, demo rezidentlar va arizalar

---

## Texnologiyalar / Tech stack

| Qatlam | Texnologiya |
|--------|-------------|
| Backend | PHP 8.3, Laravel 13 |
| Interaktivlik | Livewire 4 + Alpine.js |
| Frontend | Blade, Tailwind CSS v4 (Vite), Outfit font |
| Grafiklar | ApexCharts |
| PDF / QR | barryvdh/laravel-dompdf, endroid/qr-code (GD) |
| Ma’lumotlar bazasi | SQLite (dev) · MySQL 8 / MariaDB (prod) |
| Test / sifat | Pest 4, Larastan (PHPStan lvl 5), Laravel Pint |

Admin panel dizayni [TailAdmin](https://tailadmin.com) (Free, MIT) dizayn
tizimidan port qilingan; ochiq sayt bespoke.

---

## Rollar va demo hisoblar / Roles & demo accounts

Parol (hammasi uchun): **`demo1234`**

| Rol | Email | Vazifasi |
|-----|-------|----------|
| Talabgor / Rezident | `aral@besqala.uz` | Ariza topshirish, shaxsiy kabinet, hujjatlar |
| Direksiya | `direksiya@besqala.uz` | To‘liqlik tekshiruvi, rezidentlar, hisobotlar |
| Ekspert | `expert@besqala.uz` | Arizalarni ko‘rib chiqish, xulosa berish |
| Vakolatli organ | `authority@besqala.uz` | Ruxsatnoma berish, nazorat |
| Admin (kontent) | `admin@besqala.uz` | Yangiliklar, rahbariyat, biz haqimizda |

Kirish sahifasida har bir rol uchun bir bosishli demo tugmalari mavjud.

---

## Ariza workflow / Application workflow

```
qoralama → topshirildi → to‘liqlik tekshiruvi → ekspert xulosasi
        → vakolatli organ → ruxsatnoma berildi → ro‘yxatga olingan
                                              ↘ rad etilgan
            (kamchilik xabarnomasi istalgan bosqichda qaytarishi mumkin)
```

Har bosqich tegishli rolning amallari bilan bajariladi (biznes-logika
`app/Actions/Application/` dagi Action’larda, `ApplicationPolicy` bilan
himoyalangan). Ruxsatnoma berilganda rezident, guvohnoma va ruxsatnoma
avtomatik yaratiladi.

---

## Lokal o‘rnatish / Local setup

**Talablar:** PHP 8.3+, Composer, Node.js 18+, (GD kengaytmasi QR uchun).

```bash
git clone https://github.com/dawrandev/besqala-mining.git
cd besqala-mining

composer install
npm install

cp .env.example .env
php artisan key:generate

# SQLite (eng oson): bo‘sh fayl yaratib, .env da DB_CONNECTION=sqlite qoldiring
# Linux/macOS:  touch database/database.sqlite
# Windows:      type nul > database\database.sqlite

php artisan migrate --seed      # jadvallar + demo ma’lumotlar
npm run build                   # yoki ishlab chiqishda: npm run dev

php artisan serve               # http://127.0.0.1:8000
```

> MySQL ishlatish uchun `.env` da `DB_CONNECTION=mysql` va ulanish
> ma’lumotlarini kiriting, so‘ng `php artisan migrate --seed`.

Laragon’da: loyihani `www/` ga qo‘yib, `besqala-mining.test` vhost orqali
oching (Apache/Nginx + hosts avtomatik sozlanadi).

---

## Test va sifat / Testing & quality

```bash
php artisan test               # yoki ./vendor/bin/pest  (62 ta test)
./vendor/bin/phpstan analyse   # statik tahlil, level 5
./vendor/bin/pint              # kod formatlash (Laravel preset)
```

---

## Loyiha tuzilishi / Project structure

```
app/
  Actions/Application/   biznes-logika (Create, Transition, IssueResidentPermit, ...)
  Actions/Document/      RenderDocumentPdf (QR-kodli PDF)
  Enums/                 UserRole, ApplicationStatus, DocumentKind, NewsCategory, Region, ...
  Http/Controllers/      Public/ (ochiq sayt), App/ (panellar), Admin/ (kontent)
  Livewire/              ApplicationForm, ApplicationActions, NewsManager, ...
  Models/                Application, Resident, Document, NewsArticle, Leader, ...
  Policies/              ApplicationPolicy, DocumentPolicy
  Support/               Qr, Concerns/HasTranslations
resources/
  css/app.css            TailAdmin dizayn tizimi + accent (public) palette
  views/public/          ochiq sahifalar
  views/app/             rol panellari
  views/components/      layouts, ui/*, admin/*
lang/                    uz · ru · en (guruh fayllari + JSON)
database/                migrations, seeders/DemoSeeder, factories
docs/                    RESEARCH.md, decisions.md, DEPLOYMENT.md
```

---

## Ko‘p tillilik / Localization

- Interfeys: `lang/{uz,ru,en}.json` (tekis kalitlar) + `lang/{uz,ru,en}/*.php`
  (enum guruhlari). Sana/vaqt Carbon locale bilan sinxron (uz → `uz_Latn`).
- Kontent (yangilik, rahbariyat, biz haqimizda): JSON ustunlar, `HasTranslations`.
- Til almashtirgich har sahifada; tanlov sessiya + foydalanuvchida saqlanadi.

---

## Deploy

To‘liq ishlab chiqarish (production) yo‘riqnomasi: **[docs/DEPLOYMENT.md](docs/DEPLOYMENT.md)**.

Qisqa qadamlar:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

`APP_ENV=production`, `APP_DEBUG=false`, kuchli `APP_KEY`, MySQL, HTTPS.

---

## Hujjatlar / Docs

- [`docs/RESEARCH.md`](docs/RESEARCH.md) — asl tizim tahlili (reverse-engineering)
- [`docs/decisions.md`](docs/decisions.md) — arxitektura qarorlari
- [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) — production deploy yo‘riqnomasi

---

## Litsenziya / License

Loyiha kodi — mualliflik huquqi egasiga tegishli. Dizayn tizimi TailAdmin
(Free, MIT) asosida. Laravel — MIT.
