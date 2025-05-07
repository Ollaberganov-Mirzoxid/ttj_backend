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
        Schema::create('arizas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('jshshir');
            $table->string('passport');
            $table->date('passport_date');
            $table->string('region');
            $table->string('district');
            $table->string('address');
            $table->string('university');
            $table->boolean('has_sibling');
            $table->string('sibling_relation')->nullable();
            $table->string('sibling_jshshir')->nullable();
            $table->string('privilege')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arizas');
    }
};
