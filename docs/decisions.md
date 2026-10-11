# Decisions log

One entry per architectural decision. When the code and this file disagree,
this file wins and the code is the bug.

## D-001: Full-stack Blade + Livewire, no public JSON API
**Decision:** The app is server-rendered Blade with Livewire 4 for interactivity.
No REST API layer is built (the original's API is reproduced only as internal reads).
**Why:** The deliverable is a redesigned web platform for humans, not an API product.
Livewire covers the dynamic queues/dashboards without a separate SPA.
**Rejected:** Laravel API + React — two codebases for no added value here.
**Date:** 2026-10-11

## D-002: Five fixed roles via a single `role` enum on users
**Decision:** `users.role` is a backed enum (`applicant`, `direksiya`, `expert`,
`authority`, `admin`). Access is enforced with policies + a role middleware.
**Why:** Roles are fixed and small; per-record permissions are not needed.
**Rejected:** spatie/laravel-permission — overkill for five static roles.
**Date:** 2026-10-11

## D-003: Multilingual content stored as JSON columns
**Decision:** Translatable fields (news, leadership, about) are JSON columns cast
to array with `uz`/`ru`/`en` keys. A helper reads the active locale with uz fallback.
**Why:** Three locales, content-admin edits all at once; avoids an extra package/table.
**Rejected:** spatie/laravel-translatable, per-locale rows.
**Date:** 2026-10-11

## D-004: Applications are never deleted; status drives the workflow
**Decision:** `applications.status` is an enum covering the full lifecycle
(draft → submitted → completeness_check → deficiency → expert_review →
authority_review → permit_issued/registered → rejected). No hard deletes.
**Why:** The registry and audit trail need the full history.
**Date:** 2026-10-11

## D-005: Documents are unified; kind distinguishes them
**Decision:** One `documents` table with a `kind` enum (certificate, permit,
expert_conclusion, deficiency_notice, inspection_act), each verifiable by number.
**Why:** All issued papers share number/status/dates and the same QR verify flow.
**Date:** 2026-10-11

## D-006: SQLite in development, MySQL-ready
**Decision:** Development uses SQLite (zero-config, committed-ignored). Production
on Laragon can switch to MySQL by editing `.env` only.
**Why:** Laragon MySQL was not running; SQLite keeps the build unblocked and portable.
**Date:** 2026-10-11
