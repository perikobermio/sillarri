<?php

namespace App\Http\Controllers;

use App\Mail\ShopOrderConfirmation;
use App\Mail\ShopOrderAdminAlert;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\User;
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

        if (! $user || ! filled($user->phone)) {
            return response()->json([
                'message' => 'Telefonoa beharrezkoa da eskaria egiteko.',
                'code' => 'missing_phone',
                'redirect' => route('settings'),
            ], 422);
        }

        $catalog = $this->catalog();
        $colors = $this->colors();

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', Rule::in(array_keys($catalog))],
            'items.*.color' => ['required', Rule::in($colors)],
            'items.*.size' => ['required', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
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

        $order = DB::transaction(function () use ($user, $items, $total, $validated) {
            $order = ShopOrder::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'total' => $total,
                'status' => ShopOrder::STATUS_PENDING_PAYMENT,
                'notes' => $validated['notes'] ?? null,
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

        try {
            Mail::to($user->email)->send(new ShopOrderConfirmation($user, $items, $total, $order->id));

            $adminEmails = User::query()
                ->where('is_admin', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($adminEmails)) {
                $adminEmails = [config('mail.shop_notify', 'erikbasanez@gmail.com')];
            }

            Mail::to($adminEmails)->send(new ShopOrderAdminAlert($order->loadMissing('user'), $items, $total));
        } catch (\Throwable $e) {
            $order->delete();
            return response()->json([
                'message' => 'Ezin izan da erosketa baieztatu. Saiatu berriro.',
            ], 500);
        }

        return response()->json([
            'message' => 'Eskaria jasota. Transferentzia egin eta baieztapen emaila begiratu.',
            'order_id' => $order->id,
            'total' => $total,
        ]);
    }

    private function catalog(): array
    {
        return [
            'biserak' => [
                'name' => 'Biserak',
                'price' => 15,
                'sizes' => ['UNI'],
            ],
            'kamiseta-kalekue' => [
                'name' => 'Kamiseta kalekue',
                'price' => 20,
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            ],
            'kamiseta-teknikue' => [
                'name' => 'Kamiseta teknikue',
                'price' => 20,
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            ],
            'kamiseta-tirantedune' => [
                'name' => 'Kamiseta tirantedune',
                'price' => 20,
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            ],
            'sudaderie' => [
                'name' => 'Sudaderie',
                'price' => 25,
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
