<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // создаём GIN индекс на tsvector
        DB::statement("
            CREATE INDEX products_search_idx
            ON products
            USING GIN (
                to_tsvector('simple', coalesce(title,'') || ' ' || coalesce(description,''))
            )
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS products_search_idx");
    }
};
