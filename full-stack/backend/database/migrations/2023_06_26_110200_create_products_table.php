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
            $table->string('slug');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->decimal('price', 8, 2);
            $table->string('SKU')->nullable(); 
            $table->string('status')->default('publish');
            $table->decimal('regular_price', 8, 2)->nullable();
            $table->decimal('sale_price', 8, 2)->nullable();
            $table->integer('inStock')->nullable();
            $table->decimal('average_rating',3,2)->nullable();
            $table->string('weight')->nullable();
            $table->json('specification')->nullable();
            $table->json('dimensions')->nullable();

            $table->boolean('on_sale')->default(false);
            $table->date('date_on_sale_from')->nullable();
            $table->date('date_on_sale_to')->nullable();
            
            $table->text('short_description');
            $table->text('description');
            
            $table->unsignedBigInteger('category_id')->nullable();
            
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
