<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarView;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * B6 — Realtime dashboard, meant to be shown fullscreen on a wall display.
     */
    public function index(): View
    {
        return view('admin.dashboard');
    }

    /**
     * B6 — JSON data the dashboard polls at least every 10 seconds.
     */
    public function data(): JsonResponse
    {
        $offered = Car::count();
        $sold = Car::whereNotNull('sold_at')->count();
        $sellers = User::has('cars')->count();

        return response()->json([
            'offered' => $offered,
            'sold' => $sold,
            'for_sale' => $offered - $sold,
            'today' => Car::whereDate('created_at', today())->count(),
            'sellers' => $sellers,
            'views_today' => CarView::whereDate('created_at', today())->count(),
            'avg_per_seller' => $sellers > 0 ? round($offered / $sellers, 1) : 0,
            'sold_percentage' => $offered > 0 ? round($sold / $offered * 100) : 0,
        ]);
    }
}
