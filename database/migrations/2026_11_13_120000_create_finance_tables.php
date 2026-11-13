<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->date('occurred_on');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->string('type', 10); // income | expense
            $table->string('area', 20); // business | personal | family | tax
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index('occurred_on');
        });

        Schema::create('deadlines', function (Blueprint $table) {
            $table->id();
            $table->date('due_on');
            $table->string('label');
            $table->decimal('amount', 12, 2);
            $table->string('category')->nullable();
            $table->date('paid_on')->nullable();
            $table->timestamps();

            $table->index('due_on');
        });

        Schema::create('family_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('amount', 12, 2)->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_expenses');
        Schema::dropIfExists('deadlines');
        Schema::dropIfExists('movements');
        Schema::dropIfExists('settings');
    }
};
