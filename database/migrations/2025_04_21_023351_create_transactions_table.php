<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->string('product_name');
            $table->integer('price');
            $table->integer('quantity');
            $table->integer('sub_total');
            $table->integer('total');
            $table->string('cashier');
            $table->integer('poin_used')->default(0);
            $table->integer('change');
            $table->string('member_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
