<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported UI locales.
     *
     * @var list<string>
     */
    private const SUPPORTED = ['uz', 'ru', 'en'];

    /**
     * Resolve the active locale from the session, falling back to the
     * authenticated user's preference and then the app default.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $locale = $request->session()->get('locale')
            ?? ($user !== null ? $user->locale : null)
            ?? config('app.locale');

        if (in_array($locale, self::SUPPORTED, true)) {
            App::setLocale($locale);
            // Keep Carbon's translated dates (translatedFormat) in sync with the UI
            // locale. Carbon's "uz" locale is Cyrillic, so map it to Latin Uzbek.
            Carbon::setLocale($locale === 'uz' ? 'uz_Latn' : $locale);
        }

        return $next($request);
    }
}
