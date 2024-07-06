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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name', 50);
            $table->string('phone', 13);
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('pricePerItem')->nullable();
            $table->string('size')->nullable();
            $table->integer('payment')->nullable();
            $table->date('deadline')->nullable();
            $table->integer('progres')->nullable();
            $table->date('endDate')->nullable();
            $table->integer('shippingCost')->nullable();
            $table->string('status');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
