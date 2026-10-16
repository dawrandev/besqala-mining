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
            $candidate = Document::query()
                ->where('number', $number)
                ->with(['resident', 'replacedBy', 'statusEvents'])
                ->first();

            // Only officially issued, publicly verifiable documents are revealed.
            // Drafts stay internal and are reported as "not found".
            if ($candidate !== null
                && $candidate->kind->isPubliclyVerifiable()
                && $candidate->status->isOfficiallyIssued()) {
                $document = $candidate;
            }
        }

        return view('public.verify', [
            'number' => $number,
            'searched' => $number !== '',
            'document' => $document,
            'record' => $document !== null ? $this->record($document) : null,
        ]);
    }

    /**
     * The open registry record (Regulation point 21) — current resident data,
     * falling back to the immutable snapshot when the resident is unavailable.
     * Only the fields the regulation makes public are included.
     *
     * @return array<string, mixed>
     */
    private function record(Document $document): array
    {
        $resident = $document->resident;
        $snapshot = $document->data_snapshot ?? [];

        return [
            'company_name' => $resident->company_name ?? ($snapshot['company_name'] ?? null),
            'legal_form' => $resident->legal_form ?? ($snapshot['legal_form'] ?? null),
            'address' => $resident->address ?? ($snapshot['address'] ?? null),
            'location_address' => $resident->location_address ?? ($snapshot['location_address'] ?? null),
            'email' => $resident->email ?? ($snapshot['email'] ?? null),
            'stir' => $resident->stir ?? ($snapshot['stir'] ?? null),
            'phone' => $resident->phone ?? ($snapshot['phone'] ?? null),
            'founders' => $resident->founders ?? ($snapshot['founders'] ?? []),
        ];
    }
}
