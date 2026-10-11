<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function index(Request $request): View
    {
        $number = $request->string('no')->trim()->toString();
        $document = null;

        if ($number !== '') {
            $document = Document::query()
                ->where('number', $number)
                ->with('resident')
                ->first();
        }

        return view('public.verify', [
            'number' => $number,
            'document' => $document,
            'searched' => $number !== '',
        ]);
    }
}
