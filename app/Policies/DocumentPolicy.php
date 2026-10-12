<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Staff may download any document; an applicant may download the
     * documents of a resident company they own.
     */
    public function download(User $user, Document $document): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $document->resident !== null
            && $document->resident->owner_id === $user->id;
    }
}
