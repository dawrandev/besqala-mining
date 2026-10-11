<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Application\IssueResidentPermit;
use App\Actions\Application\RecordExpertConclusion;
use App\Actions\Application\SendDeficiencyNotice;
use App\Actions\Application\TransitionApplication;
use App\Enums\ApplicationStatus;
use App\Enums\ExpertVerdict;
use App\Models\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ApplicationActions extends Component
{
    public Application $application;

    public string $comment = '';

    public string $deficiencyText = '';

    public function mount(Application $application): void
    {
        $this->application = $application;
    }

    public function submitDraft(TransitionApplication $transition): void
    {
        $this->authorizeAction();

        $transition->handle(
            $this->application,
            ApplicationStatus::Submitted,
            auth()->user(),
            __('Ariza topshirildi'),
        );

        $this->finish(__('Ariza topshirildi.'));
    }

    public function sendToExpert(TransitionApplication $transition): void
    {
        $this->authorizeAction();

        $transition->handle(
            $this->application,
            ApplicationStatus::ExpertReview,
            auth()->user(),
            __('Ekspertizaga yuborildi'),
        );

        $this->finish(__('Ariza ekspertizaga yuborildi.'));
    }

    public function sendDeficiency(SendDeficiencyNotice $action): void
    {
        $this->authorizeAction();

        $items = array_filter(array_map('trim', explode("\n", $this->deficiencyText)));

        if ($items === []) {
            throw ValidationException::withMessages([
                'deficiencyText' => __('Kamida bitta kamchilik kiriting.'),
            ]);
        }

        $action->handle($this->application, auth()->user(), array_values($items));

        $this->finish(__('Kamchilik xabarnomasi yuborildi.'));
    }

    public function conclude(string $verdict, RecordExpertConclusion $action): void
    {
        $this->authorizeAction();

        $action->handle(
            $this->application,
            auth()->user(),
            ExpertVerdict::from($verdict),
            $this->comment ?: null,
        );

        $this->finish(__('Ekspert xulosasi saqlandi.'));
    }

    public function issuePermit(IssueResidentPermit $action): void
    {
        $this->authorizeAction();

        $action->handle($this->application, auth()->user());

        $this->finish(__('Ruxsatnoma va guvohnoma berildi.'));
    }

    public function reject(TransitionApplication $transition): void
    {
        $this->authorizeAction();

        $transition->handle(
            $this->application,
            ApplicationStatus::Rejected,
            auth()->user(),
            __('Ariza rad etildi'),
            $this->comment ?: null,
        );

        $this->finish(__('Ariza rad etildi.'));
    }

    public function render(): View
    {
        return view('livewire.application-actions');
    }

    private function authorizeAction(): void
    {
        $this->authorize('act', $this->application);
    }

    private function finish(string $message): void
    {
        session()->flash('status', $message);

        $this->redirectRoute('app.applications.show', $this->application, navigate: true);
    }
}
