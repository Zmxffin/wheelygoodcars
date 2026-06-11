<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\View\View;

class TagStatsController extends Controller
{
    /**
     * B4 — Per tag, show how often it is used, split into sold / unsold cars.
     */
    public function index(): View
    {
        $tags = Tag::query()
            ->withCount([
                'cars as sold_count' => fn ($q) => $q->whereNotNull('sold_at'),
                'cars as unsold_count' => fn ($q) => $q->whereNull('sold_at'),
                'cars as total_count',
            ])
            ->orderByDesc('total_count')
            ->get();

        return view('admin.tags', compact('tags'));
    }
}
