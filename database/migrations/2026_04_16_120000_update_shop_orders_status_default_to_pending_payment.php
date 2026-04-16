<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE shop_orders MODIFY status VARCHAR(255) NOT NULL DEFAULT 'pending_payment'");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE shop_orders ALTER COLUMN status SET DEFAULT 'pending_payment'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE shop_orders MODIFY status VARCHAR(255) NOT NULL DEFAULT 'confirmed'");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE shop_orders ALTER COLUMN status SET DEFAULT 'confirmed'");
        }
    }
};
