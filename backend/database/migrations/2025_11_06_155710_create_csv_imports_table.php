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
        Schema::create('csv_imports', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->integer('total_chunks')->default(0);
            $table->integer('processed_chunks')->default(0);
            $table->integer('total_rows')->default(0);
            $table->integer('imported')->default(0);
            $table->integer('duplicates')->default(0);
            $table->integer('errors')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_imports');
    }
};
