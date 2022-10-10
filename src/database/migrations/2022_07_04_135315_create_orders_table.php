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

        Schema::create('product_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('categories_name');
            $table->string('categories_description');
            $table->string('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_code');
            $table->string('product_name', 200);
            $table->text('product_description');
            $table->foreignUuid('product_category_id')->references('id')->on('product_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('price_sale',16,2)->unsigned()->default(0);
            $table->decimal('discount',16,2)->unsigned()->default(0);
            $table->Integer('discount_persentage')->unsigned()->default(0);
            $table->SmallInteger('is_available')->unsigned()->default(1);
            $table->json('meta')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('payment_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number');
            $table->string('holder');
            $table->SmallInteger('is_active')->default(1);
            $table->json('meta')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('order_unique_code');
            $table->integer('province_id');
            $table->integer('district_id');
            $table->string('address');
            $table->integer('payment_channel_id')->unsigned();
            $table->integer('amount');
            $table->decimal('ppn')->default(0);
            $table->decimal('ppn_fee')->default(0);
            $table->SmallInteger('is_insurance_fee')->default(0);
            $table->decimal('insurance_fee')->default(0);
            $table->decimal('packing_fee')->default(0);
            $table->integer('total_amount');
            $table->string('notes')->nullable();
            $table->string('status')->default('PENDING');
            $table->json('meta')->nullable();
            $table->integer('verify_by')->default(0)->unsigned(); // user id -> admin
            $table->string('snap_token')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
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
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('payment_channels');
        Schema::dropIfExists('orders');
    }
};
