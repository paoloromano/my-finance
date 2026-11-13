<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->date('received_on');
            $table->string('category', 30);
            $table->string('source');
            $table->string('description')->nullable();
            $table->string('document_number', 50)->nullable();
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('withholding_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('received_on');
            $table->index('category');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
