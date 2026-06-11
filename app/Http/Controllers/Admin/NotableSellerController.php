<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class NotableSellerController extends Controller
{
    /**
     * B5 — List the most notable sellers, with the reason(s) they're flagged,
     * so a beheerder can review possibly shady accounts.
     */
    public function index(): View
    {
        $sellers = User::has('cars')
            ->with(['cars' => fn ($q) => $q->withCount('tags')])
            ->get();

        $notable = $sellers
            ->map(function (User $seller) {
                $reasons = $this->reasonsFor($seller);

                return $reasons->isEmpty() ? null : (object) [
                    'seller' => $seller,
                    'reasons' => $reasons,
                ];
            })
            ->filter()
            ->sortByDesc(fn ($row) => $row->reasons->count())
            ->values();

        return view('admin.notable', compact('notable'));
    }

    /**
     * Evaluate all six B5 rules for one seller.
     */
    private function reasonsFor(User $seller)
    {
        $cars = $seller->cars;
        $reasons = collect();

        // 1) No phone number.
        if (blank($seller->phone_number)) {
            $reasons->push('Geen telefoonnummer ingevuld.');
        }

        // 2) High age but low mileage (odometer fraud).
        $sjoemel = $cars->first(function ($car) {
            if (! $car->production_year) {
                return false;
            }
            $age = (int) date('Y') - $car->production_year;

            return $age >= 12 && $car->mileage < 50000;
        });
        if ($sjoemel) {
            $reasons->push('Oude auto met verdacht lage kilometerstand (mogelijk sjoemelen).');
        }

        // 3) More than 3 cars added and sold the same day, asking price > 10.000.
        $sameDayExpensive = $cars->filter(function ($car) {
            return $car->sold_at
                && $car->sold_at->isSameDay($car->created_at)
                && $car->price > 10000;
        })->count();
        if ($sameDayExpensive > 3) {
            $reasons->push('Meer dan 3 dure auto\'s (>€10.000) op dezelfde dag toegevoegd én verkocht (mogelijk witwassen).');
        }

        // 4) Only cars priced under 1000.
        if ($cars->isNotEmpty() && $cars->every(fn ($car) => $car->price < 1000)) {
            $reasons->push('Enkel auto\'s met vraagprijs onder €1.000 (te mooi om waar te zijn).');
        }

        // 5) No tags in use across all cars.
        if ($cars->isNotEmpty() && $cars->sum('tags_count') === 0) {
            $reasons->push('Geen enkele tag in gebruik (weinig moeite gedaan).');
        }

        // 6) No new cars offered in over a year.
        $newest = $cars->max('created_at');
        if ($newest && Carbon::parse($newest)->lt(now()->subYear())) {
            $reasons->push('Al meer dan een jaar geen nieuwe auto aangeboden.');
        }

        return $reasons;
    }
}
