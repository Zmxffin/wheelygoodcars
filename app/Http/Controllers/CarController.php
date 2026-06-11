<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Tag;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CarController extends Controller
{
    /**
     * A2 — Overview of the logged-in offerer's own cars.
     */
    public function index(): View
    {
        $cars = Auth::user()->cars()
            ->with('tags')
            ->latest()
            ->get();

        return view('cars.index', compact('cars'));
    }

    /**
     * A1 / B1 / B7 / F6 / F10 — Multistep form to offer a car.
     */
    public function create(): View
    {
        $tags = Tag::orderBy('name')->get();

        return view('cars.create', compact('tags'));
    }

    /**
     * A1 — Store a newly offered car.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCar($request);

        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('cars', 'public');
        }

        $car = Car::create($data);
        $car->tags()->sync($request->input('tags', []));

        return redirect()->route('cars.index')
            ->with('status', "Auto {$car->brand} {$car->model} is toegevoegd aan je aanbod.");
    }

    /**
     * F1 / F11 — Edit price, status and tags of an existing offer.
     */
    public function edit(Car $car): View
    {
        $this->authorizeOwner($car);

        $tags = Tag::orderBy('name')->get();
        $car->load('tags');

        return view('cars.edit', compact('car', 'tags'));
    }

    /**
     * F1 / F11 — Update price, status and tags.
     */
    public function update(Request $request, Car $car): RedirectResponse
    {
        $this->authorizeOwner($car);

        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'sold' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        $car->price = $validated['price'];
        $car->sold_at = $request->boolean('sold') ? ($car->sold_at ?? now()) : null;
        $car->save();

        $car->tags()->sync($request->input('tags', []));

        return redirect()->route('cars.index')
            ->with('status', 'Aanbod bijgewerkt.');
    }

    /**
     * F1 — Toggle sold/for-sale with a single click, without a page reload (AJAX).
     */
    public function toggleStatus(Car $car)
    {
        $this->authorizeOwner($car);

        $car->sold_at = $car->sold_at ? null : now();
        $car->save();

        return response()->json([
            'sold' => $car->isSold(),
            'sold_at' => $car->sold_at?->format('d-m-Y'),
        ]);
    }

    /**
     * A3 — Delete an offer.
     */
    public function destroy(Car $car): RedirectResponse
    {
        $this->authorizeOwner($car);

        $car->delete();

        return redirect()->route('cars.index')
            ->with('status', 'Aanbod verwijderd.');
    }

    /**
     * B3 — Generate a printable PDF with the car's data.
     */
    public function pdf(Car $car)
    {
        $this->authorizeOwner($car);

        $car->load('tags', 'user');

        $pdf = Pdf::loadView('cars.pdf', compact('car'));

        return $pdf->download("auto-{$car->license_plate}.pdf");
    }

    /**
     * Shared validation rules for storing a car.
     */
    private function validateCar(Request $request): array
    {
        return $request->validate([
            'license_plate' => ['required', 'string', 'max:15'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'mileage' => ['required', 'integer', 'min:0'],
            'seats' => ['nullable', 'integer', 'min:1'],
            'doors' => ['nullable', 'integer', 'min:1'],
            'production_year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'weight' => ['nullable', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    /**
     * Make sure the current user owns the car.
     */
    private function authorizeOwner(Car $car): void
    {
        abort_unless($car->user_id === Auth::id(), 403);
    }
}
