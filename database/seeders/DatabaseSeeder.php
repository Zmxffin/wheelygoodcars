<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    private array $tagIds = [];

    /**
     * Seed at least 150 sellers, 250 cars and 20 tags.
     * A handful of sellers are deliberately crafted to trigger the
     * "notable seller" rules from user story B5.
     */
    public function run(): void
    {
        $this->call(TagSeeder::class);
        $this->tagIds = Tag::pluck('id')->all();

        // Known demo account to log in with.
        $demo = User::factory()->create([
            'name' => 'Demo Verkoper',
            'email' => 'demo@wheelygoodcars.test',
            'phone_number' => '0612345678',
        ]);
        $this->makeCars($demo, 5);

        $this->seedNotableSellers();

        // Fill up to 150 sellers and 250 cars with normal data.
        $usersToGo = 150 - User::count();
        $normalUsers = User::factory()->count(max($usersToGo, 1))->create();

        $carsToGo = 250 - Car::count();
        for ($i = 0; $i < $carsToGo; $i++) {
            $this->makeCars($normalUsers->random(), 1);
        }
    }

    /**
     * Create cars for a user and attach 0-4 random tags.
     */
    private function makeCars(User $user, int $count, array $attributes = [], bool $withTags = true): void
    {
        Car::factory()->count($count)->for($user)->create($attributes)
            ->each(function (Car $car) use ($withTags) {
                if ($withTags && ! empty($this->tagIds)) {
                    $amount = fake()->numberBetween(0, 4);
                    if ($amount > 0) {
                        $car->tags()->attach(fake()->randomElements($this->tagIds, $amount));
                    }
                }
            });
    }

    /**
     * Craft sellers that each trigger one of the B5 "notable" rules.
     */
    private function seedNotableSellers(): void
    {
        // 1) No phone number.
        User::factory()->withoutPhone()->count(3)->create()
            ->each(fn (User $u) => $this->makeCars($u, 2));

        // 2) High age but low mileage (odometer fraud).
        User::factory()->count(2)->create()->each(function (User $u) {
            $this->makeCars($u, 1, [
                'production_year' => 2006,
                'mileage' => 7500,
            ]);
        });

        // 3) More than 3 cars added & sold the same day, asking price > 10.000 (money laundering).
        User::factory()->count(2)->create()->each(function (User $u) {
            $this->makeCars($u, 4, [
                'created_at' => Carbon::today()->addHours(9),
                'sold_at' => Carbon::today()->addHours(15),
                'price' => fake()->numberBetween(12000, 30000),
            ]);
        });

        // 4) Only cars priced under 1000 (too good to be true).
        User::factory()->count(2)->create()->each(function (User $u) {
            $this->makeCars($u, 3, [
                'price' => fake()->numberBetween(300, 950),
            ]);
        });

        // 5) No tags used at all.
        User::factory()->count(2)->create()->each(function (User $u) {
            $this->makeCars($u, 2, [], withTags: false);
        });

        // 6) No new cars offered in over a year.
        User::factory()->count(2)->create()->each(function (User $u) {
            $this->makeCars($u, 2, [
                'created_at' => Carbon::now()->subMonths(15),
            ]);
        });
    }
}
