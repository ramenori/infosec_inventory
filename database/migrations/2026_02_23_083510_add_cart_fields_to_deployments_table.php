<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->foreignId('inventory_id')->nullable()->constrained()->cascadeOnDelete()->after('department');
            $table->string('component')->nullable()->after('inventory_id');
            $table->integer('quantity')->nullable()->after('component');
        });

        Schema::dropIfExists('deployment_carts');
    }

    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropColumn(['inventory_id', 'component', 'quantity']);
        });
    }
};
