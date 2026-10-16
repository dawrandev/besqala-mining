# Besqala Mining Valley

> Maxsus mayning zonasi rezidentlarini ro‘yxatdan o‘tkazish, ruxsatnomalar
> berish va faoliyatni nazorat qilishning yagona raqamli platformasi.
>
> A single digital platform for registering residents of a special crypto-mining
> zone, issuing permits, and overseeing their activity — built for the
> Karakalpakstan special mining zone (Resolution PQ‑143, 17.04.2026).

Laravel 13 · Blade + Alpine.js (AJAX) · Tailwind CSS v4 · ApexCharts

---

## Mundarija / Contents

- [Imkoniyatlar / Features](#imkoniyatlar--features)
- [Guvohnoma hayotiy sikli / Certificate lifecycle](#guvohnoma-hayotiy-sikli--certificate-lifecycle)
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
- **Ommaviy reestr yozuvi** — guvohnoma raqami yoki QR orqali: to‘liq ochiq
  ma’lumot (Nizom 21-band), joriy status va status o‘zgarishlari tarixi

**Boshqaruv panellari (TailAdmin dizayni)** — 5 rol:
- Rol asosidagi sidebar, qidiruv, dark-mode, bildirishnoma va profil menyulari
- Dashboardlarda **ApexCharts** grafiklar (oylik arizalar, status taqsimoti)
- Ariza detali: **progress stepper**, tabli bo‘limlar (Ma’lumotlar / Rahbariyat /
  Uskunalar / Hujjatlar), **QR-kodli guvohnoma preview** kartasi,
  **guvohnoma boshqaruvi** (rasmiy berish, hayotiy sikl, emailga yuborish)
- To‘liq ariza workflow’i (quyida)

**Guvohnoma va hujjatlar** — [quyida batafsil](#guvohnoma-hayotiy-sikli--certificate-lifecycle):
- O‘zgarmaydigan (immutable) berilgan guvohnoma: muzlatilgan PDF + SHA-256 + snapshot
- Olti bosqichli hayotiy sikl (DRAFT/ACTIVE/SUSPENDED/REISSUED/TERMINATED/ANNULLED)
- **3 tilli QR-kodli PDF** (uz/ru/en), `BMV-R-YYYY-NNNNNN` raqam
- Rezidentga **emailga yuborish** (mailto — SMTP sozlamassiz) + yuborish jurnali

**Boshqa**
- Trilingual interfeys (uz/ru/en) + trilingual kontent (JSON ustunlar)
- Karakalpakstanning 15 tumani, demo rezidentlar va arizalar
- Barcha formalar, CRUD va workflow amallari **AJAX** (fetch) orqali, sahifa
  qayta yuklanmasdan (Livewire’siz — oddiy Blade + Alpine.js)

---

## Guvohnoma hayotiy sikli / Certificate lifecycle

Guvohnoma (свидетельство) rasmiy hujjat sifatida **o‘zgarmaydigan** tarzda
boshqariladi. To‘liq spetsifikatsiya: **[docs/CERTIFICATE_LIFECYCLE.md](docs/CERTIFICATE_LIFECYCLE.md)**.

**Oqim:**
```
Vakolatli organ ruxsatnoma beradi
   └─▶ rezident ro‘yxatga olinadi, guvohnoma DRAFT holatda yaratiladi
          (tahrir / qayta generatsiya mumkin, reestrda ko‘rinmaydi)

Direksiya ──[Rasmiy berish]──▶ ACTIVE
   • PDF storage’ga muzlatiladi + SHA-256 yaxlitlik xeshi
   • rezident ma’lumotlari snapshoti saqlanadi (keyingi tahrirlar ta’sir qilmaydi)
   • ommaviy reestrda paydo bo‘ladi; rezidentga emailga yuborish mumkin

ACTIVE ──[To‘xtatib turish]──▶ SUSPENDED ──[Tiklash]──▶ ACTIVE
ACTIVE ──[Qayta rasmiylashtirish]──▶ yangi DRAFT + eskisi REISSUED (havola bilan)
ACTIVE ──[Amalni tugatish]──▶ TERMINATED      ACTIVE ──[Bekor qilish]──▶ ANNULLED
```

- **Immutability:** berilgan PDF hech qachon qayta yozilmaydi; download va ommaviy
  verify muzlatilgan faylni beradi. Rezident kartasini tahrirlash avval berilgan
  guvohnomani o‘zgartirmaydi.
- **Status tarixi (audit):** har o‘zgarishda sana, asos (sabab) va xodim saqlanadi.
- **Ommaviy reestr sahifasi:** eski QR amal to‘xtagandan keyin ham ishlaydi; joriy
  status, asos va (qayta rasmiylashtirilgan bo‘lsa) yangi hujjatga havola ko‘rsatiladi.
- **Email:** ilova o‘zi pochta yubormaydi (SMTP shart emas) — xodimning o‘z pochta
  dasturini `mailto:` orqali ochadi (PDF va verify havolalari bilan) va yuborishni
  jurnalga yozadi (Nizom 20-band).

---

## Texnologiyalar / Tech stack

| Qatlam | Texnologiya |
|--------|-------------|
| Backend | PHP 8.3, Laravel 13 |
| Interaktivlik | Alpine.js + AJAX (fetch), `resources/js/ajax.js` |
| Frontend | Blade, Tailwind CSS v4 (Vite), Outfit font |
| Grafiklar | ApexCharts |
| PDF / QR | barryvdh/laravel-dompdf, endroid/qr-code (GD) |
| Ma’lumotlar bazasi | SQLite (dev) · MySQL 8 / MariaDB (prod) |
| Test / sifat | Pest 4, Larastan (PHPStan lvl 5), Laravel Pint |

Admin panel dizayni [TailAdmin](https://tailadmin.com) (Free, MIT) dizayn
tizimidan port qilingan; ochiq sayt bespoke. Loyihada **Livewire ishlatilmaydi** —
interaktiv qismlar oddiy Blade + Alpine.js, server bilan aloqa esa fetch-asosli
AJAX endpointlar orqali.

---

## Rollar va demo hisoblar / Roles & demo accounts

Parol (hammasi uchun): **`demo1234`**

| Rol | Email | Vazifasi |
|-----|-------|----------|
| Talabgor / Rezident | `aral@besqala.uz` | Ariza topshirish, shaxsiy kabinet, hujjatlar |
| Direksiya | `direksiya@besqala.uz` | To‘liqlik tekshiruvi, rezidentlar, **guvohnoma rasmiy berish va boshqaruvi**, hisobotlar |
| Ekspert | `expert@besqala.uz` | Arizalarni ko‘rib chiqish, xulosa berish |
| Vakolatli organ | `authority@besqala.uz` | Ruxsatnoma berish, nazorat |
| Admin (kontent) | `admin@besqala.uz` | Yangiliklar, rahbariyat, biz haqimizda |

Kirish sahifasida har bir rol uchun bir bosishli demo tugmalari mavjud.

---

## Ariza workflow / Application workflow

```
qoralama → topshirildi → to‘liqlik tekshiruvi → ekspert xulosasi
        → vakolatli organ → ro‘yxatga olingan
                                              ↘ rad etilgan
            (kamchilik xabarnomasi istalgan bosqichda qaytarishi mumkin)
```

Har bosqich tegishli rolning amallari bilan bajariladi (biznes-logika
`app/Actions/Application/` dagi Action’larda, `ApplicationPolicy` bilan
himoyalangan). Vakolatli organ ruxsatnomani berganda rezident va ruxsatnoma
yaratiladi, guvohnoma esa **DRAFT** holatida tayyorlanadi; keyin Direksiya uni
rasmiy beradi (yuqoridagi guvohnoma hayotiy sikliga qarang).

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

> Berilgan guvohnoma PDF’lari `storage/app/documents` (privat disk) ga saqlanadi
> — bu papka avtomatik yaratiladi va faqat himoyalangan controller orqali beriladi.
> Email yuborish uchun **SMTP sozlash shart emas** (mailto ishlatiladi).

Laragon’da: loyihani `www/` ga qo‘yib, `besqala-mining.test` vhost orqali
oching (Apache/Nginx + hosts avtomatik sozlanadi).

---

## Test va sifat / Testing & quality

```bash
php artisan test               # yoki ./vendor/bin/pest  (94 ta test)
./vendor/bin/phpstan analyse   # statik tahlil, level 5
./vendor/bin/pint              # kod formatlash (Laravel preset)
```

---

## Loyiha tuzilishi / Project structure

```
app/
  Actions/Application/   biznes-logika (Create, Transition, IssueResidentPermit, ...)
  Actions/Document/      RenderDocumentPdf, IssueCertificate (muzlatish), ChangeDocumentStatus,
                         ReissueCertificate, GenerateDocumentNumber
  Enums/                 UserRole, ApplicationStatus, DocumentKind, DocumentStatus, Region, ...
  Http/Controllers/      Public/ (ochiq sayt), App/ (panellar), Admin/ (kontent), DocumentController
  Http/Requests/         Store*/Update* + DocumentStatusRequest (FormRequest validatsiya)
  Models/                Application, Resident, Document, DocumentStatusEvent, DocumentDelivery, ...
  Policies/              ApplicationPolicy, DocumentPolicy (download/issue/manage/sendEmail)
  Support/               Qr, Concerns/HasTranslations
resources/
  css/app.css            TailAdmin dizayn tizimi + accent (public) palette
  js/app.js, js/ajax.js  Alpine + ApexCharts + AJAX yordamchi (CSRF, toast, 422 inline)
  views/public/          ochiq sahifalar (verify = reestr yozuvi sahifasi)
  views/app/             rol panellari (+ applications/partials, admin/partials)
  views/pdf/             certificate.blade.php (3 tilli guvohnoma), document.blade.php (generic)
  views/components/      layouts, ui/*, admin/*
lang/                    uz · ru · en (guruh fayllari: certificate, document_status, ... + JSON)
database/                migrations, seeders/DemoSeeder, factories
docs/                    RESEARCH.md, decisions.md, DEPLOYMENT.md, CERTIFICATE_LIFECYCLE.md, templates/
```

---

## Ko‘p tillilik / Localization

- Interfeys: `lang/{uz,ru,en}.json` (tekis kalitlar, 399 ta) + `lang/{uz,ru,en}/*.php`
  (enum va guruh fayllari: `certificate`, `document_status`, ...). Sana/vaqt Carbon
  locale bilan sinxron (uz → `uz_Latn`).
- Kontent (yangilik, rahbariyat, biz haqimizda): JSON ustunlar, `HasTranslations`.
- Guvohnoma PDF’i berilgan til (locale)da muzlatiladi (uz/ru/en — DejaVu Serif,
  lotin va kirill).
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
# yozish ruxsati: storage (jumladan storage/app/documents) + bootstrap/cache
```

`APP_ENV=production`, `APP_DEBUG=false`, kuchli `APP_KEY`, MySQL, HTTPS.
Email uchun SMTP shart emas (mailto). `APP_URL` to‘g‘ri bo‘lishi muhim — PDF
ichidagi QR va verify havolalari shunga asoslanadi.

---

## Hujjatlar / Docs

- [`docs/RESEARCH.md`](docs/RESEARCH.md) — asl tizim tahlili (reverse-engineering)
- [`docs/decisions.md`](docs/decisions.md) — arxitektura qarorlari
- [`docs/CERTIFICATE_LIFECYCLE.md`](docs/CERTIFICATE_LIFECYCLE.md) — guvohnoma hayotiy sikli spetsifikatsiyasi
- [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) — production deploy yo‘riqnomasi
- [`docs/templates/`](docs/templates/) — tasdiqlangan A4 guvohnoma shablonlari (uz/ru/en)

---

## Litsenziya / License

Loyiha kodi — mualliflik huquqi egasiga tegishli. Dizayn tizimi TailAdmin
(Free, MIT) asosida. Laravel — MIT.
