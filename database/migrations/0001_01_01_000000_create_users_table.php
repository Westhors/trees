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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('phone')->nullable();

            // role Details
            $table->enum('role', ['user', 'engineer', 'seller', 'merchant', 'company'])->default('user');
            // role Details

            //seller
            $table->string('nature_of_work')->nullable();
            $table->string('workshop_name')->nullable();
            //seller

            //engineer
            $table->enum('created_from', ['admin', 'user'])->nullable(); // المهندسين الذين تم اضافتهم من الادمن
            //engineer

            //merchant
            $table->string('facebook_link')->nullable();
            $table->string('whatsapp_number')->nullable();
            //merchant

            // company
            $table->string('company_name')->nullable();
            $table->text('company_description')->nullable();
            $table->string('company_address')->nullable();
            // company


            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('active')->default(1);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
