<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminWeatherLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_weather_location_from_geocoding_result(): void
    {
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [
                    [
                        'latitude' => 43.3629,
                        'longitude' => -2.5044,
                    ],
                ],
            ]),
        ]);

        $admin = User::factory()->create([
            'username' => 'admin',
            'is_admin' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.locations.store'), [
                'name' => 'Lekeitio',
                'label' => 'Lekeitio',
            ]);

        $response->assertRedirect(route('admin'));
        $response->assertSessionHas('admin_tab', 'weather');
        $response->assertSessionHas('status', 'Herria gehituta.');
        $this->assertDatabaseHas('weather_locations', [
            'name' => 'Lekeitio',
            'label' => 'Lekeitio',
        ]);
        Http::assertSent(fn ($request): bool => str_contains($request->url(), 'geocoding-api.open-meteo.com/v1/search')
            && $request['name'] === 'Lekeitio');
    }

    public function test_geocoding_api_failure_returns_controlled_error(): void
    {
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response(['error' => true], 503),
        ]);

        $admin = User::factory()->create([
            'username' => 'admin',
            'is_admin' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.locations.store'), [
                'name' => 'Lekeitio',
                'label' => 'Lekeitio',
            ]);

        $response->assertRedirect(route('admin'));
        $response->assertSessionHas('admin_tab', 'weather');
        $response->assertSessionHas('open_location_create', true);
        $response->assertSessionHas('error', 'Ezin izan da kokapena egiaztatu. Saiatu berriro.');
        $this->assertDatabaseMissing('weather_locations', [
            'name' => 'Lekeitio',
            'label' => 'Lekeitio',
        ]);
    }

    public function test_location_validation_errors_use_weather_modal_error_bag(): void
    {
        Http::fake();

        $admin = User::factory()->create([
            'username' => 'admin',
            'is_admin' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.locations.store'), [
                'name' => 'Beste herri bat',
                'label' => 'Gernika',
            ]);

        $response->assertRedirect(route('admin'));
        $response->assertSessionHas('admin_tab', 'weather');
        $response->assertSessionHas('open_location_create', true);
        $response->assertSessionHasErrors(['label'], null, 'locationCreate');
        Http::assertNothingSent();
    }
}
