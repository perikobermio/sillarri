<?php

namespace App\Http\Controllers;

use App\Mail\ShopOrderConfirmation;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    public function index()
    {
        return view('shop.index');
    }

    public function checkout(Request $request): JsonResponse
    {
        $user = $request->user();

        $catalog = $this->catalog();
        $colors = $this->colors();

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', Rule::in(array_keys($catalog))],
            'items.*.color' => ['required', Rule::in($colors)],
            'items.*.size' => ['required', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $items = [];
        $total = 0;

        foreach ($validated['items'] as $item) {
            $product = $catalog[$item['id']];
            $allowedSizes = $product['sizes'];

            if (!in_array($item['size'], $allowedSizes, true)) {
                return response()->json(['message' => 'Talla ez da zuzena.'], 422);
            }

            $lineTotal = $product['price'] * $item['qty'];
            $total += $lineTotal;
            $items[] = [
                'name' => $product['name'],
                'product_id' => $item['id'],
                'color' => $item['color'],
                'size' => $item['size'],
                'qty' => $item['qty'],
                'unit_price' => $product['price'],
                'line_total' => $lineTotal,
            ];
        }

        $order = DB::transaction(function () use ($user, $items, $total) {
            $order = ShopOrder::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'total' => $total,
                'status' => 'confirmed',
            ]);

            foreach ($items as $item) {
                ShopOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'color' => $item['color'],
                    'size' => $item['size'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                ]);
            }

            return $order;
        });

        Mail::to($user->email)->send(new ShopOrderConfirmation($user, $items, $total));

        return response()->json([
            'message' => 'Erosketa baieztatuta.',
            'order_id' => $order->id,
            'total' => $total,
        ]);
    }

    private function catalog(): array
    {
        return [
            'biserak' => [
                'name' => 'Biserak',
                'price' => 22,
                'sizes' => ['UNI'],
            ],
            'kamiseta-kalekue' => [
                'name' => 'Kamiseta kalekue',
                'price' => 22,
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            ],
            'kamiseta-teknikue' => [
                'name' => 'Kamiseta teknikue',
                'price' => 22,
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            ],
            'kamiseta-tirantedune' => [
                'name' => 'Kamiseta tirantedune',
                'price' => 22,
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            ],
            'sudaderie' => [
                'name' => 'Sudaderie',
                'price' => 32,
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            ],
        ];
    }

    private function colors(): array
    {
        return [
            'BK', 'WH', 'RD', 'AZ', 'RB', 'BR', 'SY', 'AS', 'SB', 'SA', 'PV', 'LI', 'AQ',
        ];
    }
}
