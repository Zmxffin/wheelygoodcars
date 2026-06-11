<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarView;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCarController extends Controller
{
    /**
     * Landing page with the licence-plate quick-start.
     */
    public function home(): View
    {
        return view('home');
    }

    /**
     * F2 / F5 / F7 / F8 / F9 / F12 — Public overview of all cars for sale.
     * Supports searching (make/model) and filtering on tags without a page
     * reload: AJAX requests get just the grid partial back.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $selectedTags = array_filter((array) $request->query('tags', []));

        $query = Car::forSale()->with('tags')->withCount('tags');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if (! empty($selectedTags)) {
            // Car must have ALL selected tags.
            foreach ($selectedTags as $tagId) {
                $query->whereHas('tags', fn ($q) => $q->where('tags.id', $tagId));
            }
        }

        $cars = $query->latest()->paginate(12)->withQueryString();

        // F5 — deterministically highlight a few random cars (bigger in the grid).
        $highlighted = $cars->getCollection()
            ->random(min(3, $cars->count()))
            ->pluck('id')
            ->all();

        if ($request->ajax()) {
            return view('public.partials.grid', compact('cars', 'highlighted'));
        }

        $tags = Tag::orderBy('name')->get();

        return view('public.index', compact('cars', 'tags', 'search', 'selectedTags', 'highlighted'));
    }

    /**
     * F3 / B8 — Detail page of a single car. Counts a view.
     */
    public function show(Car $car): View
    {
        abort_if($car->isSold(), 404);

        $car->increment('views');
        CarView::create(['car_id' => $car->id]);
        $car->load('tags', 'user');

        return view('public.show', compact('car'));
    }

    /**
     * F4 — Expose the live view counts for the "X klanten bekeken deze auto
     * vandaag" popup (both today's count and the total).
     */
    public function views(Car $car): JsonResponse
    {
        return response()->json([
            'today' => CarView::where('car_id', $car->id)->whereDate('created_at', today())->count(),
            'total' => $car->views,
        ]);
    }
}
