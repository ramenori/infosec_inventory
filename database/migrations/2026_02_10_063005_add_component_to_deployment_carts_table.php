<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deployment_carts', function (Blueprint $table) {
            // Add the component column
            $table->string('component')->nullable()->after('inventory_id');
            
            // Optional: Add an index for better performance
            $table->index('component');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deployment_carts', function (Blueprint $table) {
            $table->dropColumn('component');
            $table->dropIndex(['component']);
        });
    }
};