<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            // Remove columns updated_at and scrapped
            $table->dropColumn('updated_at');
            // Restore the available_at column type
            $table->dropColumn('available_at');
            $table->unsignedInteger('available_at');
            // Restore the created_at column type
            $table->dropColumn('created_at');
            $table->unsignedInteger('created_at');
            // Restore the reserved_at column type
            $table->dropColumn('reserved_at');
            $table->unsignedInteger('reserved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            // Change available_at column type
            $table->dropColumn('available_at');
            $table->dateTime('available_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            // Change created_at column type
            $table->dropColumn('created_at');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            // Change reserved_at column type
            $table->dropColumn('reserved_at');
            $table->dateTime('reserved_at')->nullable();
            // Add columns updated_at and scrapped
            $table->dateTime('updated_at')->nullable();
        });
    }
};
