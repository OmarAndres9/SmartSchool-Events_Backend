<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Sanitizar datos no numéricos antes de cambiar el tipo
        DB::statement('UPDATE "_recursos__table" SET "capacidad" = \'0\' WHERE "capacidad" IS NULL OR "capacidad" = \'\' OR "capacidad" !~ \'^\\d+$\'');
        DB::statement('ALTER TABLE "_recursos__table" ALTER COLUMN "capacidad" TYPE integer USING "capacidad"::integer');
        DB::statement('ALTER TABLE "_recursos__table" ALTER COLUMN "capacidad" SET DEFAULT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE "_recursos__table" ALTER COLUMN "capacidad" TYPE varchar(50)');
        DB::statement('ALTER TABLE "_recursos__table" ALTER COLUMN "capacidad" SET DEFAULT \'0\'');
    }
};
