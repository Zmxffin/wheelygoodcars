<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Realistic make => models map.
     */
    private const MODELS = [
        'Volkswagen' => ['Golf', 'Polo', 'Passat', 'Tiguan', 'Up!'],
        'Opel' => ['Corsa', 'Astra', 'Insignia', 'Mokka'],
        'Renault' => ['Clio', 'Megane', 'Captur', 'Twingo'],
        'Peugeot' => ['208', '308', '2008', '3008'],
        'Toyota' => ['Yaris', 'Corolla', 'Aygo', 'RAV4'],
        'Ford' => ['Fiesta', 'Focus', 'Kuga', 'Puma'],
        'BMW' => ['1-serie', '3-serie', 'X1', '5-serie'],
        'Audi' => ['A1', 'A3', 'A4', 'Q3'],
        'Mercedes-Benz' => ['A-Klasse', 'C-Klasse', 'E-Klasse', 'GLA'],
        'Kia' => ['Picanto', 'Rio', 'Ceed', 'Sportage'],
    ];

    private const COLORS = ['Zwart', 'Wit', 'Grijs', 'Blauw', 'Rood', 'Zilver', 'Groen'];

    public function definition(): array
    {
        $brand = fake()->randomElement(array_keys(self::MODELS));
        $model = fake()->randomElement(self::MODELS[$brand]);
        $year = fake()->numberBetween(2005, 2024);

        return [
            'user_id' => User::factory(),
            'license_plate' => $this->licensePlate(),
            'brand' => $brand,
            'model' => $model,
            'price' => fake()->numberBetween(1500, 45000),
            'mileage' => fake()->numberBetween(5000, 280000),
            'seats' => fake()->randomElement([2, 4, 5, 5, 5, 7]),
            'doors' => fake()->randomElement([2, 3, 4, 5, 5]),
            'production_year' => $year,
            'weight' => fake()->numberBetween(900, 2200),
            'color' => fake()->randomElement(self::COLORS),
            'image' => null,
            'sold_at' => fake()->boolean(30) ? fake()->dateTimeBetween('-1 year', 'now') : null,
            'views' => fake()->numberBetween(0, 500),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Generate a plausible Dutch license plate (sidecode-ish).
     */
    private function licensePlate(): string
    {
        $l = fn ($n) => strtoupper(fake()->lexify(str_repeat('?', $n)));
        $d = fn ($n) => fake()->numerify(str_repeat('#', $n));

        return $l(2).'-'.$d(2).'-'.$l(2);
    }

    /**
     * State: car is sold.
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'sold_at' => fake()->dateTimeBetween($attributes['created_at'] ?? '-1 year', 'now'),
        ]);
    }

    /**
     * State: car is still for sale.
     */
    public function forSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'sold_at' => null,
        ]);
    }
}
