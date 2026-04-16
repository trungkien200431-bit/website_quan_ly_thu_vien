<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('readers', 'user_id')) {
            Schema::table('readers', function (Blueprint $table): void {
                $table->foreignId('user_id')
                    ->nullable()
                    ->unique()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        $columnsToDrop = array_values(array_filter([
            Schema::hasColumn('readers', 'password') ? 'password' : null,
            Schema::hasColumn('readers', 'remember_token') ? 'remember_token' : null,
        ]));

        if ($columnsToDrop !== []) {
            Schema::table('readers', function (Blueprint $table) use ($columnsToDrop): void {
                $table->dropColumn($columnsToDrop);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('readers', 'user_id')) {
            Schema::table('readers', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('user_id');
            });
        }

        if (! Schema::hasColumn('readers', 'password') || ! Schema::hasColumn('readers', 'remember_token')) {
            Schema::table('readers', function (Blueprint $table): void {
                if (! Schema::hasColumn('readers', 'password')) {
                    $table->string('password')->nullable()->after('notes');
                }

                if (! Schema::hasColumn('readers', 'remember_token')) {
                    $table->rememberToken();
                }
            });
        }
    }
};
