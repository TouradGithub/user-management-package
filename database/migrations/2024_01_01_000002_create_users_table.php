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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('language')->default('en');
            $table->foreignId('user_type_id')->nullable()->constrained('user_types')->onDelete('set null');
            $table->json('profile_data')->nullable();
            $table->json('settings')->nullable();
            $table->json('preferences')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->boolean('two_factor_confirmed_at')->default(false);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['is_active', 'created_at']);
            $table->index(['user_type_id', 'is_active']);
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};