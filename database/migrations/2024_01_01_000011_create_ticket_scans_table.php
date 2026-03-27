<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ticket_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('scanned_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('scanned_at');
            $table->enum('result', ['valid', 'already_used', 'invalid', 'cancelled'])->default('valid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ticket_scans'); }
};
