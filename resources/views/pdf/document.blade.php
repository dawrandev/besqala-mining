<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        @page { margin: 0; }
        body { margin: 0; color: #1f2733; font-size: 12px; }
        .sheet { padding: 48px 56px; }
        .border { border: 2px solid #0e7472; padding: 28px 32px; }
        .head { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: top; }
        .agency { font-size: 10px; color: #4b5563; text-transform: uppercase; letter-spacing: .5px; }
        .brand { font-size: 20px; font-weight: bold; color: #0e7472; margin-top: 4px; }
        .rule { height: 3px; background: #0e7472; margin: 18px 0 24px; }
        .kind { font-size: 24px; font-weight: bold; color: #0f172a; text-align: center; margin: 4px 0; }
        .number { text-align: center; font-size: 14px; color: #0e7472; letter-spacing: 1px; margin-bottom: 20px; }
        table.meta { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.meta td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        table.meta td.k { color: #6b7280; width: 38%; }
        table.meta td.v { color: #111827; font-weight: bold; }
        .qrbox { text-align: center; }
        .qrbox img { width: 150px; height: 150px; }
        .qrcap { font-size: 9px; color: #6b7280; margin-top: 6px; }
        .status { display: inline-block; padding: 3px 12px; border-radius: 10px; font-size: 11px; font-weight: bold;
                  color: #087443; background: #e7f8ef; }
        .foot { margin-top: 28px; padding-top: 14px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #6b7280; }
        .foot strong { color: #374151; }
        .seal { text-align: right; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="sheet">
    <div class="border">
        <table class="head">
            <tr>
                <td>
                    <div class="agency">{{ __('O‘zbekiston Respublikasi Istiqbolli loyihalar milliy agentligi') }}</div>
                    <div class="brand">{{ __('Besqala Mining Valley — Maxsus mayning zonasi') }}</div>
                </td>
            </tr>
        </table>

        <div class="rule"></div>

        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="vertical-align:top; width:63%; padding-right:24px;">
                    <div class="kind">{{ $document->kind->label() }}</div>
                    <div class="number">№ {{ $document->number }}</div>

                    <table class="meta">
                        <tr>
                            <td class="k">{{ __('Korxona') }}</td>
                            <td class="v">{{ $resident?->company_name ?? $document->payload['companyName'] ?? '—' }}</td>
                        </tr>
                        @if ($resident)
                            <tr><td class="k">{{ __('STIR') }}</td><td class="v">{{ $resident->stir }}</td></tr>
                            <tr><td class="k">{{ __('Hudud') }}</td><td class="v">{{ $resident->region->label() }}</td></tr>
                            <tr><td class="k">{{ __('Tashkiliy shakl') }}</td><td class="v">{{ $resident->legal_form }}</td></tr>
                            @if ($resident->address)
                                <tr><td class="k">{{ __('Manzil') }}</td><td class="v">{{ $resident->address }}</td></tr>
                            @endif
                        @endif
                        @if (! empty($document->payload['scope']))
                            <tr><td class="k">{{ __('Mavzu') }}</td><td class="v">{{ $document->payload['scope'] }}</td></tr>
                        @endif
                        <tr>
                            <td class="k">{{ __('Holati') }}</td>
                            <td class="v"><span class="status">{{ $document->status->label() }}</span></td>
                        </tr>
                        <tr>
                            <td class="k">{{ __('Berilgan sana') }}</td>
                            <td class="v">{{ optional($document->issued_at)->format('d.m.Y') ?? '—' }}</td>
                        </tr>
                        @if ($document->expires_at)
                            <tr>
                                <td class="k">{{ __('Amal qilish muddati') }}</td>
                                <td class="v">{{ $document->expires_at->format('d.m.Y') }}</td>
                            </tr>
                        @endif
                    </table>
                </td>
                <td style="vertical-align:top; width:37%;">
                    <div class="qrbox">
                        <img src="{{ $qr }}" alt="QR">
                        <div class="qrcap">
                            {{ __('Hujjatni tekshirish') }}<br>
                            {{ $verifyUrl }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="foot">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="vertical-align:top;">
                        <strong>{{ __('Huquqiy asos') }}:</strong> {{ __('PQ–143-son qaror asosida') }}<br>
                        {{ __('Har bir guvohnoma va ruxsatnoma raqam hamda QR-kod orqali tekshiriladi.') }}
                    </td>
                    <td class="seal" style="vertical-align:top; width:30%;">
                        Besqala Mining Valley<br>
                        {{ now()->format('d.m.Y H:i') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>
