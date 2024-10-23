<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            // Change available_at column type
            $table->dropColumn('available_at');
            $table->dateTime('available_at');
            // Change created_at column type
            $table->dropColumn('created_at');
            $table->dateTime('created_at');
            // Change reserved_at column type
            $table->dropColumn('reserved_at');
            $table->dateTime('reserved_at')->nullable();
            // Add columns updated_at and scrapped
            $table->dateTime('updated_at')->nullable();
            $table->longText('scrapped')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            // Remove columns updated_at and scrapped
            $table->dropColumn('updated_at');
            $table->dropColumn('scrapped');
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
};
