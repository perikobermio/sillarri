<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ShopCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_accepts_any_sweatshirt_color(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'phone' => '600000000',
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('shop.checkout'), [
                'items' => [
                    [
                        'id' => 'sudaderie',
                        'variant' => 'adult',
                        'color' => 'NY',
                        'size' => 'M',
                        'qty' => 1,
                    ],
                ],
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('total', 25);

        $this->assertDatabaseHas('shop_order_items', [
            'product_id' => 'sudaderie',
            'color' => 'NY',
            'size' => 'M',
        ]);
    }

    public function test_checkout_accepts_children_sizes_for_technical_tank_and_sweatshirt(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'phone' => '600000000',
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('shop.checkout'), [
                'items' => [
                    [
                        'id' => 'kamiseta-teknikue',
                        'variant' => 'adult',
                        'color' => 'WH',
                        'size' => '3-4',
                        'qty' => 1,
                    ],
                    [
                        'id' => 'kamiseta-tirantedune',
                        'variant' => 'adult',
                        'color' => 'BK',
                        'size' => '5-6',
                        'qty' => 1,
                    ],
                    [
                        'id' => 'sudaderie',
                        'variant' => 'adult',
                        'color' => 'NY',
                        'size' => '12-14',
                        'qty' => 1,
                    ],
                ],
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('total', 65);

        $this->assertDatabaseHas('shop_order_items', [
            'product_id' => 'kamiseta-teknikue',
            'size' => '3-4',
        ]);
        $this->assertDatabaseHas('shop_order_items', [
            'product_id' => 'kamiseta-tirantedune',
            'size' => '5-6',
        ]);
        $this->assertDatabaseHas('shop_order_items', [
            'product_id' => 'sudaderie',
            'size' => '12-14',
        ]);
    }
}
