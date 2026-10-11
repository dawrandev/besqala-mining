<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AboutEditor extends Component
{
    /** @var array<string, string> */
    public array $title = ['uz' => '', 'ru' => '', 'en' => ''];

    /** @var array<string, string> */
    public array $intro = ['uz' => '', 'ru' => '', 'en' => ''];

    /** @var list<array{label: array<string, string>, value: string}> */
    public array $stats = [];

    public function mount(): void
    {
        $about = SiteSetting::get('about') ?? [];

        $this->title = array_merge(['uz' => '', 'ru' => '', 'en' => ''], $about['title'] ?? []);
        $this->intro = array_merge(['uz' => '', 'ru' => '', 'en' => ''], $about['intro'] ?? []);
        $this->stats = $about['stats'] ?? [
            ['label' => ['uz' => '', 'ru' => '', 'en' => ''], 'value' => ''],
        ];
    }

    public function addStat(): void
    {
        $this->stats[] = ['label' => ['uz' => '', 'ru' => '', 'en' => ''], 'value' => ''];
    }

    public function removeStat(int $index): void
    {
        unset($this->stats[$index]);
        $this->stats = array_values($this->stats);
    }

    public function save(): void
    {
        $this->validate([
            'title.uz' => 'required|string|max:255',
            'intro.uz' => 'required|string',
        ]);

        SiteSetting::put('about', [
            'title' => $this->title,
            'intro' => $this->intro,
            'stats' => array_values(array_filter($this->stats, fn (array $s): bool => $s['value'] !== '')),
        ]);

        session()->flash('status', __('Saqlandi.'));
    }

    public function render(): View
    {
        return view('livewire.about-editor');
    }
}
