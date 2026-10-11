<?php

declare(strict_types=1);

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Enums\ExpertVerdict;
use App\Models\Application;
use App\Models\ExpertConclusion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class RecordExpertConclusion
{
    public function __construct(private readonly TransitionApplication $transition) {}

    /**
     * Save the expert commission's verdict and route the application onward.
     */
    public function handle(Application $application, User $expert, ExpertVerdict $verdict, ?string $comment = null): Application
    {
        return DB::transaction(function () use ($application, $expert, $verdict, $comment): Application {
            ExpertConclusion::query()->create([
                'application_id' => $application->id,
                'expert_id' => $expert->id,
                'verdict' => $verdict,
                'comment' => $comment,
                'members' => ['R. Nurmuxamedov (rais)', 'D. Li', 'S. Allambergenova'],
                'concluded_at' => now(),
            ]);

            $next = $verdict === ExpertVerdict::Positive
                ? ApplicationStatus::AuthorityReview
                : ApplicationStatus::Rejected;

            return $this->transition->handle(
                $application,
                $next,
                $expert,
                $verdict === ExpertVerdict::Positive ? __('Ijobiy ekspert xulosasi') : __('Salbiy ekspert xulosasi'),
                $comment,
            );
        });
    }
}
