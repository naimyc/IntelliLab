<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('language');
            $table->string('topic')->nullable();
            $table->boolean('abgeschlossen')->default(false);
            $table->integer('total_tasks')->default(1);
            $table->integer('completed_tasks')->default(0);
            $table->json('exercise_data')->nullable(); // gesamtes Exercise-JSON
            $table->text('ki_feedback')->nullable();   // letztes Prüfen-Feedback
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('projects'); }
};
