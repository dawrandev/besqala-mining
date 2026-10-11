<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Leader;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LeadershipManager extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    /** @var array<string, string> */
    public array $full_name = ['uz' => '', 'ru' => '', 'en' => ''];

    /** @var array<string, string> */
    public array $position = ['uz' => '', 'ru' => '', 'en' => ''];

    /** @var array<string, string> */
    public array $department = ['uz' => '', 'ru' => '', 'en' => ''];

    /** @var array<string, string> */
    public array $bio = ['uz' => '', 'ru' => '', 'en' => ''];

    public int $sort_order = 0;

    public function create(): void
    {
        $this->reset(['editingId', 'full_name', 'position', 'department', 'bio', 'sort_order']);
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $leader = Leader::query()->findOrFail($id);
        $this->editingId = $leader->id;
        $this->full_name = array_merge(['uz' => '', 'ru' => '', 'en' => ''], $leader->full_name);
        $this->position = array_merge(['uz' => '', 'ru' => '', 'en' => ''], $leader->position);
        $this->department = array_merge(['uz' => '', 'ru' => '', 'en' => ''], $leader->department ?? []);
        $this->bio = array_merge(['uz' => '', 'ru' => '', 'en' => ''], $leader->bio ?? []);
        $this->sort_order = $leader->sort_order;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'full_name.uz' => 'required|string|max:255',
            'position.uz' => 'required|string|max:255',
        ]);

        $data = [
            'full_name' => $this->full_name,
            'position' => $this->position,
            'department' => $this->department,
            'bio' => $this->bio,
            'sort_order' => $this->sort_order,
        ];

        if ($this->editingId !== null) {
            Leader::query()->findOrFail($this->editingId)->update($data);
        } else {
            Leader::query()->create($data);
        }

        $this->showForm = false;
        session()->flash('status', __('Saqlandi.'));
    }

    public function delete(int $id): void
    {
        Leader::query()->findOrFail($id)->delete();
        session()->flash('status', __('O‘chirildi.'));
    }

    public function render(): View
    {
        return view('livewire.leadership-manager', [
            'leaders' => Leader::query()->orderBy('sort_order')->get(),
        ]);
    }
}
