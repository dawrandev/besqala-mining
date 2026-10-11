# Besqala Mining Valley — asl tizim tahlili (reverse-engineering)

Manba: `http://109.205.176.124:4500/` (React SPA + Express JSON API). Bu hujjat
qayta qurilayotgan tizimning funksional spetsifikatsiyasi sifatida ishlatiladi.
Dizayn qayta ishlanadi, funksiya va ma'lumot modeli saqlanadi.

## Umumiy

- **Nomi:** «Besqala Mining Valley» — maxsus mayning zonasi (Qoraqalpog'iston).
- **Huquqiy asos:** 2026-yil 17-apreldagi PQ–143-son qarori.
- **Egasi:** O'zbekiston Respublikasi Istiqbolli loyihalar milliy agentligi.
- **Tillar:** uz / ru / en (barcha kontent uch tilda).
- **Maqsadi:** yuridik shaxslarni rezident sifatida ro'yxatga olish, ruxsatnoma
  berish, faoliyatni nazorat qilish, hujjatlarni QR orqali tekshirish.

## Rollar (5 ta) — parol demo: `demo1234`

| Rol kaliti | Login | Ism | Panel sarlavhasi |
|---|---|---|---|
| `applicant` (Talabgor/Rezident) | aral@besqala.uz | Azamat Qalbaev | Rezident kabineti |
| `direksiya` (Direksiya) | direksiya@besqala.uz | Dilnoza Yusupova | Direksiya boshqaruv paneli |
| `expert` (Ekspert) | expert@besqala.uz | Rustam Nurmuxamedov | Ekspert komissiyasi |
| `authority` (Vakolatli organ) | authority@besqala.uz | Dmitriy Li | Vakolatli organ |
| `admin` (Kontent administratori) | admin@besqala.uz | — | Kontent boshqaruvi |

## Ariza workflow (status oqimi)

```
draft (Qoralama)
  → submitted (Topshirilgan)
  → completeness_check (To'liqlik tekshiruvi — Direksiya)
      → deficiency (Kamchilik xabarnomasi) → qayta submitted
  → expert_review (Ekspert komissiyasi xulosasi: ijobiy/salbiy)
  → authority_review (Vakolatli organ — ruxsatnoma qarori)
  → permit_issued (Ruxsatnoma berildi) + registered (Reestrga kiritildi) + guvohnoma
  → rejected (Rad etilgan)
```
Ariza turlari: `registration` (yangi), `amendment` (o'zgartirish arizasi).
Rezident holati: `active` (Faol), `suspended` (To'xtatib turilgan), `terminated`.

## Har rol paneli (sidebar + dashboard)

### applicant — Rezident kabineti
Sidebar: Boshqaruv paneli · Mening arizalarim (`/app/applications`) · Yangi ariza
(`/app/new`) · Shaxsiy kabinet (`/app/cabinet`) · [Ommaviy] Ochiq reestr · Hujjatni tekshirish.
Dashboard stat: Faol rezidentlik · Jarayonda · Harakat talab qilinadi · Jami qazib olindi (BTC).
Bloklar: Mening arizalarim ro'yxati · Shaxsiy kabinet (guvohnoma) · Tezkor havolalar.

### direksiya — Direksiya boshqaruv paneli
Sidebar: Boshqaruv paneli · Arizalar navbati (`/app/queue`) · Rezidentlar
(`/app/residents`) · Hisobotlar (`/app/reports`) · Ochiq reestr · Hujjatni tekshirish.
Dashboard stat: Yangi arizalar (to'liqlik tekshiruvi) · Ekspertda · Ro'yxatga olish · Faol rezidentlar.
Bloklar: To'liqlik tekshiruvi navbati · So'nggi harakatlar (timeline).

### expert — Ekspert komissiyasi
Sidebar: Boshqaruv paneli · Ko'rib chiqish (`/app/review`) · Ochiq reestr · Hujjatni tekshirish.
Dashboard stat: Ko'rib chiqish kutilmoqda (15 kun muddat) · Ijobiy xulosalar · Salbiy xulosalar.
Blok: Ko'rib chiqish navbati. Komissiya a'zolari: R. Nurmuxamedov (rais), D. Li, S. Allambergenova.

### authority — Vakolatli organ
Sidebar: Boshqaruv paneli · Ruxsatnomalar (`/app/permits`) · Nazorat (`/app/oversight`)
· Ochiq reestr · Hujjatni tekshirish.
Dashboard stat: Ruxsatnoma kutilmoqda (8 kun) · Faol ruxsatnomalar · To'xtatib turilgan · Aniqlangan buzilishlar.
Bloklar: Ruxsatnoma navbati · So'nggi dalolatnomalar (nazorat).

### admin — Kontent boshqaruvi
Sidebar: Boshqaruv paneli · Yangiliklar (`/app/admin/news`) · Rahbariyat
(`/app/admin/leadership`) · Biz haqimizda (`/app/admin/about`) · Ochiq reestr · Hujjatni tekshirish.
Dashboard stat: Yangiliklar soni · Rahbariyat a'zolari · Biz haqimizda holati.

## Public (ochiq) sahifalar

- `/` Home · `/about` Biz haqimizda · `/news` + `/news/:slug` Yangiliklar
- `/leadership` Rahbariyat · `/registry` Ochiq reestr · `/verify` Hujjat tekshirish · `/login`

## Public API (asl) — javob shakllari

- `GET /api/public/about` → {title, intro, stats} (uz/ru/en)
- `GET /api/public/leadership` → [{fullName, position, department, bio}]
- `GET /api/public/news` / `/public/news/:slug` → [{title, excerpt, body, ...}]
- `GET /api/public/registry` → [{id, companyName, stir, region, legalForm,
  operational, address, email, phone, founders:[{name,share}], certificate:{number,issuedAt,expiresAt,status}}]
- `GET /api/public/verify/:number` → {found, kind, status, document:{number,issuedAt,expiresAt,...}, resident:{...}}

## Hujjat turlari

- `certificate` — rezidentlik guvohnomasi (G-2026-xxxx)
- `permit` — ruxsatnoma
- `expert_conclusion` — ekspert xulosasi (ijobiy/salbiy)
- `deficiency_notice` — kamchilik xabarnomasi
- `inspection_act` — dalolatnoma (nazorat)

## Hududlar (Qoraqalpog'iston)

Nukus shahri, Nukus tumani, Qo'ng'irot tumani, Qorao'zak tumani, Chimboy tumani,
Ellikqal'a tumani, Beruniy tumani, Amudaryo tumani, Kegeyli tumani, Shumanay tumani,
Taxiatosh tumani, Mo'ynoq tumani, Taxtako'pir tumani, To'rtko'l tumani, Qanliko'l tumani.

## Reestrdagi demo rezidentlar (asl)

1. «Aral Digital Mining» MChJ — STIR 305412876, Qorao'zak tumani, active, G-2026-0001.
2. «Kungrad DataFarm» MChJ — STIR 306788221, Qo'ng'irot tumani, suspended, G-2026-0002.

## Texnik qarorlar (qayta qurish)

- Stack: Laravel 13 + Blade + Livewire 4; Tailwind v4 + Alpine.js + ApexCharts.
- Public sayt: maxsus zamonaviy dizayn (frontend-design). Admin/rol panellari: TailAdmin (HTML/Tailwind).
- Ko'p tilli kontent: JSON ustunlar (uz/ru/en) + cast, qo'shimcha paketsiz.
