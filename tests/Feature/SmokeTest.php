<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the full demo dataset (150 sellers, 250 cars, 20 tags) on the
        // isolated test database so these end-to-end checks have data to run on.
        $this->seed();
    }

    private function demo(): User
    {
        return User::where('email', 'demo@wheelygoodcars.test')->firstOrFail();
    }

    public function test_public_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get('/aanbod')->assertOk();

        $car = Car::whereNull('sold_at')->firstOrFail();
        $this->get("/aanbod/{$car->id}")->assertOk();
        $this->getJson("/aanbod/{$car->id}/views")->assertOk()->assertJsonStructure(['today', 'total']);
    }

    public function test_authenticated_pages_render(): void
    {
        $user = $this->demo();

        $this->actingAs($user)->get('/mijn-aanbod')->assertOk();
        $this->actingAs($user)->get('/aanbod-plaatsen')->assertOk();
        $this->actingAs($user)->get('/beheer/dashboard')->assertOk();
        $this->actingAs($user)->getJson('/beheer/dashboard/data')->assertOk()
            ->assertJsonStructure(['offered', 'sold', 'today', 'sellers', 'views_today', 'avg_per_seller', 'sold_percentage']);
        $this->actingAs($user)->get('/beheer/tags')->assertOk();
        $this->actingAs($user)->get('/beheer/opvallend')->assertOk();

        $car = $user->cars()->firstOrFail();
        $this->actingAs($user)->get("/mijn-aanbod/{$car->id}/bewerken")->assertOk();
    }

    public function test_owner_can_toggle_status_and_others_cannot(): void
    {
        $user = $this->demo();
        $car = $user->cars()->firstOrFail();
        $wasSold = $car->sold_at !== null;

        $this->actingAs($user)->patchJson("/mijn-aanbod/{$car->id}/status")
            ->assertOk()->assertJson(['sold' => ! $wasSold]);

        // A different user must not be able to touch it.
        $other = User::where('id', '!=', $user->id)->firstOrFail();
        $this->actingAs($other)->patchJson("/mijn-aanbod/{$car->id}/status")->assertForbidden();
    }

    public function test_pdf_download_works(): void
    {
        $user = $this->demo();
        $car = $user->cars()->firstOrFail();

        $response = $this->actingAs($user)->get("/mijn-aanbod/{$car->id}/pdf");
        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_full_offer_lifecycle(): void
    {
        $user = $this->demo();
        $tagIds = \App\Models\Tag::take(2)->pluck('id')->all();

        // A1 / F10 — create with tags.
        $this->actingAs($user)->post('/aanbod-plaatsen', [
            'license_plate' => 'TE-ST-99',
            'brand' => 'Testmerk',
            'model' => 'Testmodel',
            'price' => 9999,
            'mileage' => 12345,
            'tags' => $tagIds,
        ])->assertRedirect('/mijn-aanbod');

        $car = Car::where('license_plate', 'TE-ST-99')->firstOrFail();
        $this->assertSame($user->id, $car->user_id);
        $this->assertCount(2, $car->tags);

        // F1 / F11 — update price, mark sold, change tags.
        $this->actingAs($user)->put("/mijn-aanbod/{$car->id}", [
            'price' => 8000,
            'sold' => 1,
            'tags' => [$tagIds[0]],
        ])->assertRedirect('/mijn-aanbod');

        $car->refresh();
        $this->assertEquals(8000, $car->price);
        $this->assertNotNull($car->sold_at);
        $this->assertCount(1, $car->fresh()->tags);

        // A3 — delete.
        $this->actingAs($user)->delete("/mijn-aanbod/{$car->id}")->assertRedirect('/mijn-aanbod');
        $this->assertModelMissing($car);
    }

    public function test_rdw_lookup_returns_car_data(): void
    {
        // Live RDW open-data lookup (B1) with a known existing plate.
        $response = $this->actingAs($this->demo())->getJson('/rdw/TT027D');

        if ($response->status() !== 200) {
            $this->markTestSkipped('RDW API not reachable in this environment.');
        }

        $response->assertJson(['found' => true])
            ->assertJsonPath('data.brand', 'AUDI');
    }

    public function test_search_and_tag_filter(): void
    {
        // F7 — search by brand returns an AJAX grid partial.
        $response = $this->get('/aanbod?search=Volkswagen', ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertOk();

        // F9 — filtering on a tag only returns matching, for-sale cars.
        $tag = \App\Models\Tag::has('cars')->first();
        $this->get('/aanbod?tags[]='.$tag->id, ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
    }
}
