<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->integer('restaurant_id');
            $table->integer('customer_id')->default(0);
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->decimal('sub_total')->default(0);
            $table->decimal('tax')->default(0);
            $table->decimal('delivery_charges')->default(0);
            $table->decimal('discount')->default(0);
            $table->decimal('total')->default(0);
            $table->string('voucher_code')->nullable();
            $table->integer('voucher_id')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->text('notes')->nullable();
            $table->integer('order_placed_by')->nullable();
            $table->integer('status_updated_by')->nullable();
            $table->enum('status', ['Pending', 'Processing', 'Delivered', 'Shipped', 'Completed', 'Cancelled'])->default('Pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
