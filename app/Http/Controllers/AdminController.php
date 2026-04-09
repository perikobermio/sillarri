<?php

namespace App\Http\Controllers;

use App\Models\KilterMap;
use App\Models\ShopOrder;
use App\Models\User;
use App\Models\WeatherLocation;
use App\Mail\ShopOrderCancelledNotify;
use App\Mail\ShopOrderCancelledUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $users = User::query()->orderBy('name')->get();
        $maps = KilterMap::query()->orderBy('created_at', 'desc')->get();
        $locations = WeatherLocation::query()->orderBy('name')->get();
        $orders = ShopOrder::query()
            ->with(['items', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        $orders->transform(static function (ShopOrder $order): ShopOrder {
            $order->items_payload = $order->items->map(static function ($item): array {
                return [
                    'name' => $item->name,
                    'color' => $item->color,
                    'size' => $item->size,
                    'qty' => $item->qty,
                    'line_total' => $item->line_total,
                ];
            })->values();
            return $order;
        });
        $perPageSetting = DB::table('app_settings')
            ->where('key', 'kilter_blocks_per_page')
            ->value('value');
        $blockListPageSize = is_numeric($perPageSetting) ? (int) $perPageSetting : 50;
        if ($blockListPageSize <= 0) {
            $blockListPageSize = 50;
        }
        $blockListPageSize = max(2, min(100, $blockListPageSize));

        return view('admin.index', [
            'users' => $users,
            'maps' => $maps,
            'locations' => $locations,
            'orders' => $orders,
            'blockListPageSize' => $blockListPageSize,
        ]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => trim((string) $data['name']),
            'username' => strtolower(trim((string) $data['username'])),
            'email' => strtolower(trim((string) $data['email'])),
            'password' => Hash::make($data['password']),
            'is_admin' => (bool) ($data['is_admin'] ?? false),
        ]);

        return redirect()->route('admin')->with('status', 'Erabiltzailea sortuta.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $user->name = trim((string) $data['name']);
        $user->username = strtolower(trim((string) $data['username']));
        $user->email = strtolower(trim((string) $data['email']));
        $user->is_admin = (bool) ($data['is_admin'] ?? false);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin')->with('status', 'Erabiltzailea eguneratuta.');
    }

    public function deleteUser(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin')->with('status', 'Erabiltzailea ezabatuta.');
    }

    public function deleteMap(KilterMap $map): RedirectResponse
    {
        if ($map->image) {
            Storage::disk('public')->delete($map->image);
        }
        $map->delete();

        return redirect()->route('admin')->with('status', 'Mapa ezabatuta.');
    }

    public function storeLocation(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:weather_locations,name'],
            'label' => ['required', 'string', 'max:120', 'unique:weather_locations,label'],
        ]);

        $geoResponse = Http::get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $data['name'],
            'count' => 1,
            'language' => 'es',
            'format' => 'json',
        ]);

        $geoJson = $geoResponse->json();
        $first = is_array($geoJson) ? ($geoJson['results'][0] ?? null) : null;
        if (! $first || ! isset($first['latitude'], $first['longitude'])) {
            return redirect()->route('admin')->with('status', 'Ez da kokapena aurkitu. Saiatu izenarekin.');
        }

        WeatherLocation::create([
            'name' => trim((string) $data['name']),
            'label' => trim((string) $data['label']),
            'lat' => (float) $first['latitude'],
            'lon' => (float) $first['longitude'],
        ]);

        return redirect()->route('admin')->with('status', 'Herria gehituta.');
    }

    public function deleteLocation(WeatherLocation $location): RedirectResponse
    {
        $location->delete();

        return redirect()->route('admin')->with('status', 'Herria ezabatuta.');
    }

    public function deleteOrder(ShopOrder $order): RedirectResponse
    {
        $order->loadMissing(['items', 'user']);
        $items = $order->items->map(static function ($item): array {
            return [
                'name' => $item->name,
                'color' => $item->color,
                'size' => $item->size,
                'qty' => $item->qty,
                'line_total' => $item->line_total,
            ];
        })->all();
        $total = (float) $order->total;
        $shopEmail = config('mail.shop_notify', 'erikbasanez@gmail.com');

        \Illuminate\Support\Facades\Mail::to($order->email)->send(new ShopOrderCancelledUser($order, $items, $total));
        \Illuminate\Support\Facades\Mail::to($shopEmail)->send(new ShopOrderCancelledNotify($order, $items, $total));

        $order->delete();

        return redirect()->route('admin')->with('status', 'Eskaria ezabatuta.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kilter_blocks_per_page' => ['required', 'integer', 'min:2', 'max:100'],
        ]);

        DB::table('app_settings')->updateOrInsert(
            ['key' => 'kilter_blocks_per_page'],
            ['value' => (string) $data['kilter_blocks_per_page'], 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->route('admin')->with('status', 'Ezarpenak ondo gorde dira.');
    }
}
