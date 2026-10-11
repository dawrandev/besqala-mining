<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Application\CreateApplication;
use App\Enums\Region;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ApplicationForm extends Component
{
    #[Validate('required|string|max:255')]
    public string $company_name = '';

    #[Validate('required|string|size:9')]
    public string $stir = '';

    #[Validate('required|string')]
    public string $region = '';

    #[Validate('required|string|max:255')]
    public string $address = '';

    #[Validate('nullable|string|max:255')]
    public string $location_address = '';

    #[Validate('nullable|email')]
    public string $email = '';

    #[Validate('nullable|string|max:30')]
    public string $phone = '';

    #[Validate('nullable|numeric|min:0')]
    public ?float $power_mw = null;

    #[Validate('nullable|string|max:60')]
    public string $energy_source = '';

    #[Validate('nullable|string|max:60')]
    public string $bank_account = '';

    /** @var list<array{name: string, share: int|string}> */
    public array $founders = [['name' => '', 'share' => 100]];

    public function addFounder(): void
    {
        $this->founders[] = ['name' => '', 'share' => 0];
    }

    public function removeFounder(int $index): void
    {
        unset($this->founders[$index]);
        $this->founders = array_values($this->founders);
    }

    public function save(bool $submit, CreateApplication $createApplication): mixed
    {
        $validated = $this->validate();

        $application = $createApplication->handle([
            'company_name' => $validated['company_name'],
            'stir' => $validated['stir'],
            'region' => $validated['region'],
            'legal_form' => 'MChJ (Mas’uliyati cheklangan jamiyat)',
            'address' => $validated['address'],
            'location_address' => $this->location_address ?: null,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'founders' => array_values(array_filter($this->founders, fn (array $f): bool => $f['name'] !== '')),
            'power_mw' => $this->power_mw,
            'energy_source' => $this->energy_source ?: null,
            'bank_account' => $this->bank_account ?: null,
        ], auth()->user(), $submit);

        session()->flash('status', $submit ? __('Ariza topshirildi.') : __('Qoralama saqlandi.'));

        return $this->redirectRoute('app.applications.show', $application, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.application-form', [
            'regions' => Region::options(),
        ]);
    }
}
