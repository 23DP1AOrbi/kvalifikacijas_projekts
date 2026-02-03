<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rename columns
            $table->renameColumn('lietotajvards', 'name');
            $table->renameColumn('epasts', 'email');
            $table->renameColumn('parole', 'password');

            // Add role enum column
            $table->enum('role', ['user', 'admin'])->default('user')->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'lietotajvards');
            $table->renameColumn('email', 'epasts');
            $table->renameColumn('password', 'parole');

            $table->dropColumn('role');
        });
    }
};

