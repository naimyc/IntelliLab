<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('analyse_ergebnis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('language');
            $table->text('gelernt')->nullable();
            $table->text('verbessern')->nullable();
            $table->text('noch_lernen')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('analyse_ergebnis'); }
};
