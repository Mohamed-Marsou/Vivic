<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaleColumnsToProducts extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('on_sale')->default(false);
            $table->date('date_on_sale_from')->nullable();
            $table->date('date_on_sale_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('on_sale');
            $table->dropColumn('date_on_sale_from');
            $table->dropColumn('date_on_sale_to');
        });
    }
}
