<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Add the supplier_id column first
            $table->foreignId('supplier_id')->nullable()->after('status')->constrained('suppliers')->onDelete('set null');
            
            // Remove the old supplier column
            $table->dropColumn('supplier');
        });
    }

    public function down()
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Remove the foreign key constraint first
            $table->dropForeign(['supplier_id']);
            
            // Add back the old supplier column
            $table->string('supplier')->nullable()->after('status');
            
            // Remove the supplier_id column
            $table->dropColumn('supplier_id');
        });
    }
};