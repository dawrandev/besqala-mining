# Guvohnoma — hayotiy sikl, immutability va ommaviy tekshiruv

> Manba: client (Direksiya) talablari (rus tilidagi texnik topshiriq, 2026-10-16) +
> Nizom 8-moddasi (lex.uz/uz/docs/8316053, 20–21-bandlar) + A4 PDF shablonlar
> (`docs/templates/A4 - {UZ,RU,EN}.pdf`). Ushbu hujjat **guvohnoma (свидетельство о
> регистрации)** ga tegishli. Ruxsatnoma (permit) hozircha o'zgarmaydi; mexanizm
> umumiy quriladi va keyinchalik unga kengaytirilishi mumkin.

## 1. Asosiy tamoyillar

1. **Immutability.** Rasmiy berilgan guvohnoma PDF'i va u yaratilган ma'lumotlar
   tizimда **o'zgармайдиган tarixiy versiya** sifatida saqlanadi. Rezident kartasini
   keyinchalik tahrirlash avval berilgан guvohnoma mazmunini o'zgartirmaydi
   (jonli bog'lanish emas — berilgan paytdagi **snapshot** ishlatiladi).
2. **Versiyalash.** Har bir guvohnoma uchun saqlanadi: PDF fayl, berilish paytidagi
   rezident ma'lumotlari snapshoti, raqam va status, generatsiya sana-vaqti,
   **shablon versiyasi**, PDF'ning **SHA-256** xeshi, generatsiyани tasdiqlagan xodim,
   status o'zgarishlari tarixi, (bo'lsa) o'rнини bosgan hujjatга havola.
3. **Tuzatish = yangi hujjat.** Rasmiy berilgandan keyин xatolик topilsa yoki
   ma'lumot o'zgarsa, eski PDF tahrirlanmaydi/qayta yozilmaydi — **yangi guvohnoma**
   (yangi raqам + internal id) yaratiladi, eskisiga `REISSUED`/`ANNULLED` status
   beriladi va yangisiga havola saqlanadi.
4. **Davomiylik.** Eski QR va tekshiruv sahifasi amal to'xtaгандан keyин ham ishlaydi;
   joriy statusни, (bo'lsa) to'xtatish sababини va o'rнини bosgan hujjатга havolani
   ko'rsatadi.

## 2. Statuslar (DocumentStatus)

| Status | Ma'nosi | Ommaviy reestrda | Email |
|--------|---------|------------------|-------|
| `DRAFT` | Rasmiy berishdan oldingi dastlabki versiya (ichki) | ❌ Yo'q | ❌ Yo'q |
| `ACTIVE` | Guvohnoma amal qiladi | ✅ Ha | ✅ (berilganda) |
| `SUSPENDED` | Amal vaqtincha to'xtatilgan | ✅ Ha (status bilan) | — |
| `REISSUED` | Qayta rasmiylashtirилган (yangisiga havola bilan) | ✅ Ha (status bilan) | — |
| `TERMINATED` | Amal to'xtaган (muddат tugaши ham shu yerda) | ✅ Ha (status bilan) | — |
| `ANNULLED` | Bekor qilingan | ✅ Ha (status bilan) | — |

- **DRAFT ichki holat:** rasmiy berilган deb hisoblanmaydi, ommaviy reestrda
  ko'rinmaydi, emailга yuborilmaydi. DRAFT holatда xodim ma'lumotни tuzatib,
  **yangi rasmiy hujjat yaratmasдан** qayта generatsiya qila oladi.
- **Qayта tiklash (restore):** tarixда alohида hodisa sifatida qayд etiladi,
  so'ng joriy status yana `ACTIVE` bo'ladi.
- Har bir status o'zgarishi uchun saqlanadi: **sana, sabab, amални bajargан foydalanuvchi**.

## 3. Hayotiy sikl oqimi

```
Ariza → ... → vakolatли organ ruxsatnoma beradi (IssueResidentPermit)
        └─> rezident ro'yxатга olinadi, guvohnoma DRAFT holatда yaratiladi
            (tahrirlанади / qayта generatsiya qilinади, reestrда yo'q)

Direksiya  ──[Rasmiy berish]──▶  ACTIVE
   • PDF muzlatiладi (storage) + SHA-256
   • rezident ma'lumotлари snapshoti saqlanади
   • shablon versiyasi, bergан xodim, sana-vaqt
   • reestrда paydo bo'ladi
   • emailга yuboriladi (PDF + verify havola)

ACTIVE ──[Suspend]──▶ SUSPENDED ──[Restore]──▶ ACTIVE
ACTIVE ──[Reissue]──▶ (yangi DRAFT→ACTIVE) + eskisi REISSUED (havola bilan)
ACTIVE ──[Terminate]──▶ TERMINATED   (muddат tugаши ham)
ACTIVE ──[Annul]──▶ ANNULLED
```

**«Rasmiy berish» amалини DIREKSIYA bajaradi** (shablonда imzolovчи «Direksiya
rahbari»). Rasmiy berishgача guvohnoma DRAFT bo'lib, erkин tahrirlанади.

### Uchта misol holат (TZ dan)
1. **Berishdан oldин tuzatиш** — DRAFT'да erkин tahrir + qayта generatsiya, yangi hujjатsiz.
2. **Berishдан keyин xato** — eski PDF o'zgармайди; yangi tuzatилган guvohnoma, eskiси
   REISSUED/ANNULLED + yangisига havola.
3. **Ma'lumот o'zgариши** — nom/tashkiliy shakl/manzil o'zgарса, yangи hujjат; eskиси
   tarixий versiya sifатида saqlanади.

## 4. Ommaviy tekshiruv / reestr yozuvи sahifаси (Nizom 21-band)

QR-kod **alohида ommaviy sahifага** olиб boradi (`registry.bmv.uz` / verify).
Sahifада (PDF'дан kengрoq) quyидагилар ko'rsatилади:

- to'liq noми va tashkilий-huquqий shakли;
- joylashган yerи va pochta manзили;
- elektron pochта manзили;
- STIR;
- telefон raqами;
- **ta'sисchилар va ularning ustаv fondидагی ulушlари**;
- berilиш sanаси va tartиб raqами;
- **qayта rasmiylaштириш, to'xtатиш, tiklaш, amални to'xtатиш va bekор qилиш
  asoslари va sanаlари** (status tarихи).

PDF'нинг o'zида faqат **asосий rekvizитlar** (shablондаги 7 maydон). **Amал muddатi
va ta'сисchилар — faqат sahифада**, PDF'да emas (client qarорi).

## 5. Email yubориш (Nizom 20-band)

- Rasmiy berилгандан so'ng Direksiya xodими PDF'ни rezидент emailига yuборади
  (xat: PDF + ommaviy verify sahифа havоласи).
- Saqlanади: qabул qiluvчи manзил, yubориш sana-vaqti, yubорган foydalanuvчи,
  natижа (yuboriлди/xato). Xatoда **o'ша hujjатни qayта yuбориш** (yangи guvohnomasиz).
- DRAFT yuбориlmaydi. Faqат rasmiy berилгандан keyин.
- Asосий bildirиshnoma mexanизmи keyин reissue/suspend/restore/terminate/annul
  hodisаlарига kengаytирилади.

## 6. PDF shablон (A4, 3 til)

- Dizayn: `docs/templates/A4 - {UZ,RU,EN}.pdf`. **HTML/CSS** (yoki SVG) dinamик
  maydонlар bilan — rasm ustига matn **EMAS**. Yashил gilyош ramка = dekoratив fon.
- Tepа-chап: Direksiya nomi. Tepа-o'нг: QR + «Hujjатни tekshириш / registry.bmv.uz».
- Maydонlар (7): to'liq noм+tashkiliy shakl · joylashган yer (pochта manзили) ·
  email · STIR · telefон · guvohnома tartiб raqами · berilган sanа.
- Imzо: «Direksiya rahbари / J. Esenbaev» (statик, sozlanади).
- Raqам formати: **`BMV-R-YYYY-NNNNNN`** (masалан `BMV-R-2026-000015`).
- Uzун nomlар, QR va til versиyaлари to'g'ри ishlashи shart.

## 7. Ma'lumотlar modelи (qisqача)

**`documents`** (mavjуд + yangи ustунlар): `internal_uid` (uuid), `number`, `kind`,
`status`, `resident_id`, `application_id`, `issued_by`, `data_snapshot` (json),
`pdf_path`, `template_version`, `sha256`, `issued_at`, `expires_at`,
`replaced_by_document_id` (self FK), timestamps.

**`document_status_events`** (yangі): `document_id`, `from_status`, `to_status`,
`reason`, `actor_id`, `created_at`.

**`document_deliveries`** (yangі, 5-modul): `document_id`, `to_email`, `sent_by`,
`status` (sent/failed), `error`, `sent_at`, timestamps.

## 8. Bosqичлар

1. Poydevor: statuslар (6) + `document_status_events` + model/enum + IssueResidentPermit→DRAFT.
2. Rasmiy berиш: PDF muzlатиш (storage) + SHA-256 + snapshot + shablон versиyaси (Direksiya amali + UI).
3. Hayotiy sikл amallари: reissue / suspend / restore / terminate / annul (UI bilan).
4. Ommaviy verify/reestr sahифаси (21-band).
5. PDF shablон (dizayн, 3 til, BMV-R raqам).
6. Email + asосий bildirиshnoma bazаси.
