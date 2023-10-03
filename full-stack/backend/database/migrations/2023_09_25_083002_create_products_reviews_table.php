<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('average_rating');
            $table->string('title')->nullable();
            $table->string('author');
            $table->string('email')->nullable();
            $table->text('body_text');
            $table->text('body_url')->nullable();
            $table->string('country_code')->nullable();
            $table->string('status')->default('enable');
            $table->string('feature')->default('0');
            $table->timestamps();

            $table->foreign('slug')->references('slug')->on('products');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
