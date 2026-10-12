<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaderRequest;
use App\Models\Leader;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class LeadershipController extends Controller
{
    public function index(): View
    {
        return view('app.admin.leadership', [
            'leaders' => Leader::query()->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * The members grid, rendered as HTML for AJAX refresh.
     */
    public function list(): View
    {
        return view('app.admin.partials.leadership-grid', [
            'leaders' => Leader::query()->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Field values for the edit form (JSON).
     */
    public function edit(Leader $leader): JsonResponse
    {
        return response()->json([
            'id' => $leader->id,
            'full_name' => array_merge(['uz' => '', 'ru' => '', 'en' => ''], $leader->full_name),
            'position' => array_merge(['uz' => '', 'ru' => '', 'en' => ''], $leader->position),
            'department' => array_merge(['uz' => '', 'ru' => '', 'en' => ''], $leader->department ?? []),
            'bio' => array_merge(['uz' => '', 'ru' => '', 'en' => ''], $leader->bio ?? []),
            'sort_order' => $leader->sort_order,
        ]);
    }

    public function store(StoreLeaderRequest $request): JsonResponse
    {
        Leader::query()->create($this->payload($request));

        return response()->json(['ok' => true, 'message' => __('Saqlandi.')]);
    }

    public function update(StoreLeaderRequest $request, Leader $leader): JsonResponse
    {
        $leader->update($this->payload($request));

        return response()->json(['ok' => true, 'message' => __('Saqlandi.')]);
    }

    public function destroy(Leader $leader): JsonResponse
    {
        $leader->delete();

        return response()->json(['ok' => true, 'message' => __('O‘chirildi.')]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(StoreLeaderRequest $request): array
    {
        return [
            'full_name' => $request->input('full_name'),
            'position' => $request->input('position'),
            'department' => $request->input('department', []),
            'bio' => $request->input('bio', []),
            'sort_order' => (int) $request->input('sort_order', 0),
        ];
    }
}
