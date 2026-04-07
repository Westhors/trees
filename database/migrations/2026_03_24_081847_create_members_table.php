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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('password')->nullable();
            $table->string('national_id')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_death')->nullable();
            $table->string('city')->nullable();
            $table->json('personal_relationships')->nullable();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained();
            $table->enum('gender',['male','female'])->nullable();
            $table->foreignId('father_id')
                ->nullable()
                ->constrained('members')
                ->nullOnDelete();
            $table->string('mother_name')->nullable();
            $table->string('wife_name')->nullable();
            $table->string('application_number')->unique()->nullable();
            $table->boolean('dead')->default(0);
            $table->boolean('active')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
