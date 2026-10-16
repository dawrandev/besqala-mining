<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { font-family: 'DejaVu Serif', serif; }
        body { margin: 0; color: #1f2733; }
        .frame { position: absolute; top: 0; left: 0; width: 794px; height: 1123px; }
        .sheet { position: relative; padding: 70px 74px 60px; }

        .head { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: top; }
        .directorate { font-size: 13px; font-weight: bold; color: #1f2733; line-height: 1.35; width: 62%; }
        .qrcell { text-align: right; width: 38%; }
        .qrcell img { width: 112px; height: 112px; }
        .qrcap { font-size: 8.5px; color: #4b5563; margin-top: 2px; line-height: 1.3; }

        .title { text-align: center; margin: 34px 0 6px; }
        .title .word { font-size: 21px; font-weight: bold; letter-spacing: 1px; color: #0f172a; }
        .title .desc { font-size: 13px; font-weight: bold; color: #0f172a; line-height: 1.4; margin-top: 4px; }

        .intro { font-size: 11.5px; line-height: 1.55; text-align: justify; margin: 22px 4px 8px; }

        table.fields { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.fields td { padding: 11px 6px; border-bottom: 1.4px solid #2f6b5e; vertical-align: top; }
        table.fields td.k { width: 44%; font-size: 10px; font-weight: bold; color: #1f2733; line-height: 1.3; padding-right: 18px; }
        table.fields td.v { font-size: 12.5px; color: #111827; line-height: 1.4; word-wrap: break-word; }
        table.fields td.num { font-family: 'DejaVu Sans Mono', monospace; letter-spacing: .5px; }

        .sign { width: 100%; border-collapse: collapse; margin-top: 56px; }
        .sign td { vertical-align: bottom; font-size: 12px; font-weight: bold; color: #1f2733; }
        .sign .seal { text-align: center; }
        .sign .seal .box { width: 96px; height: 96px; margin: 0 auto; }
    </style>
</head>
<body>
    <img class="frame" src="{{ $frame }}" alt="">
    <div class="sheet">
        <table class="head">
            <tr>
                <td class="directorate">{{ __('certificate.directorate') }}</td>
                <td class="qrcell">
                    <img src="{{ $qr }}" alt="QR">
                    <div class="qrcap">
                        {{ __('certificate.verify_caption') }}<br>registry.bmv.uz
                    </div>
                </td>
            </tr>
        </table>

        <div class="title">
            <div class="word">{{ __('certificate.title_word') }}</div>
            <div class="desc">{{ __('certificate.title_desc') }}</div>
        </div>

        <p class="intro">{{ __('certificate.intro') }}</p>

        <table class="fields">
            <tr>
                <td class="k">{{ __('certificate.label_name') }}</td>
                <td class="v">{{ $data['company_name'] ?: '—' }}</td>
            </tr>
            <tr>
                <td class="k">{{ __('certificate.label_location') }}</td>
                <td class="v">{{ $data['address'] ?: ($data['location_address'] ?: '—') }}</td>
            </tr>
            <tr>
                <td class="k">{{ __('certificate.label_email') }}</td>
                <td class="v">{{ $data['email'] ?: '—' }}</td>
            </tr>
            <tr>
                <td class="k">{{ __('certificate.label_tin') }}</td>
                <td class="v num">{{ $data['stir'] ?: '—' }}</td>
            </tr>
            <tr>
                <td class="k">{{ __('certificate.label_phone') }}</td>
                <td class="v">{{ $data['phone'] ?: '—' }}</td>
            </tr>
            <tr>
                <td class="k">{{ __('certificate.label_number') }}</td>
                <td class="v num">{{ $document->number }}</td>
            </tr>
            <tr>
                <td class="k">{{ __('certificate.label_date') }}</td>
                <td class="v num">{{ optional($document->issued_at)->format('d.m.Y') ?? '—' }}</td>
            </tr>
        </table>

        <table class="sign">
            <tr>
                <td style="width:38%;">{{ __('certificate.signatory_title') }}</td>
                <td class="seal" style="width:28%;"><div class="box"></div></td>
                <td style="width:34%; text-align:right;">{{ __('certificate.signatory_name') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
