<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'librarian', 'customer') NOT NULL DEFAULT 'librarian'");
    }

    public function down(): void
    {
        if (! in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'librarian') NOT NULL DEFAULT 'librarian'");
    }
};
