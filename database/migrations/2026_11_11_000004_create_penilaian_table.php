<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternatif_id')->constrained('alternatif')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriteria')->cascadeOnDelete();
            $table->foreignId('sub_kriteria_id')->nullable()->constrained('sub_kriteria')->nullOnDelete();
            $table->decimal('nilai', 12, 2);
            $table->timestamps();
            $table->unique(['alternatif_id', 'kriteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
