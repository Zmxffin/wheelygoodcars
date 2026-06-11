<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * 20 realistic car-feature tags with badge colours.
     */
    private const TAGS = [
        ['Trekhaak', '#0d6efd'],
        ['Navigatie', '#6610f2'],
        ['Airco', '#6f42c1'],
        ['Cruise control', '#d63384'],
        ['Lederen bekleding', '#dc3545'],
        ['Panoramadak', '#fd7e14'],
        ['Stoelverwarming', '#ffc107'],
        ['Parkeersensoren', '#198754'],
        ['Achteruitrijcamera', '#20c997'],
        ['Apple CarPlay', '#0dcaf0'],
        ['Android Auto', '#0d6efd'],
        ['Automaat', '#212529'],
        ['Lichtmetalen velgen', '#6c757d'],
        ['Elektrische ramen', '#198754'],
        ['Bluetooth', '#0dcaf0'],
        ['Climate control', '#6610f2'],
        ['Adaptieve cruise', '#d63384'],
        ['Dodehoekdetectie', '#dc3545'],
        ['Keyless entry', '#fd7e14'],
        ['Sportstoelen', '#198754'],
    ];

    public function run(): void
    {
        foreach (self::TAGS as [$name, $color]) {
            Tag::firstOrCreate(['name' => $name], ['color' => $color]);
        }
    }
}
