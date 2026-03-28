<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Crea el schema "app" en PostgreSQL/Supabase antes de ejecutar el resto
 * de las migraciones. En SQLite esta migración no hace nada.
 *
 * Este archivo debe mantenerse con el timestamp más antiguo posible
 * (0000_00_00_000000) para garantizar que siempre se ejecute primero.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE SCHEMA IF NOT EXISTS app');
        }
    }

    public function down(): void
    {
        // No eliminamos el schema en down() para evitar pérdida accidental de datos.
        // Si necesitás eliminarlo manualmente, ejecutá:
        //   DROP SCHEMA app CASCADE;
    }
};
