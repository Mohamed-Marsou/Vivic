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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->decimal('regular_price', 8, 2)->nullable();
            $table->decimal('sale_price', 8, 2)->nullable();
            $table->integer('inStock')->nullable();
            $table->string('slug');
            $table->string('status')->nullable();
            $table->decimal('average_rating',3,2)->nullable();;
            $table->json('specification')->nullable();
            $table->string('weight')->nullable();
            $table->json('dimensions')->nullable();
            $table->text('short_description');
            $table->text('description');

            $table->unsignedBigInteger('category_id')->nullable();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
