<?php

use App\Models\ScrappedItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add scrapped_products table
        Schema::create(ScrappedItem::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string('attribute');
            $table->string('selector');
            $table->string('url');
            $table->string('value');
            $table->timestamps();
        });

        // Remove scrapped column from jobs table
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('scrapped');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove scrapped_products table
        Schema::dropIfExists(ScrappedItem::TABLE);

        // Restore scrapped column in jobs table
        Schema::table('jobs', function (Blueprint $table) {
            $table->longText('scrapped');
        });
    }
};
