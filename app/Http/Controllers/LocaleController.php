<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    private const SUPPORTED = ['uz', 'ru', 'en'];

    /**
     * Persist the chosen UI locale and store it on the user when logged in.
     */
    public function update(Request $request, string $locale): RedirectResponse
    {
        if (in_array($locale, self::SUPPORTED, true)) {
            $request->session()->put('locale', $locale);
            $request->user()?->update(['locale' => $locale]);
        }

        return back();
    }
}
