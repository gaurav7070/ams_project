<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name', 50);
            $table->string('last_name', 50)->nullable();
            $table->string('login_id', 50)->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->enum('account_type', ['Personal', 'Business']);
            $table->string('created_by', 50);
            $table->string('modified_by', 50)->nullable();
            $table->string('extra1', 50)->nullable();
            $table->rememberToken();
            $table->timestamps(); // This will automatically create created_at and updated_at columns
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
}
