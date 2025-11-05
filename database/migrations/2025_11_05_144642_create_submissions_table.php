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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique(); // REQ-0001
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->date('request_date'); // Request date
            $table->date('expected_return_date'); // 5 days after
            $table->date('received_at')->nullable(); // Actual date of receipt Admin
            $table->integer('days_elapsed')->nullable();
            $table->enum('status', ['created', 'overdue', 'returned'])->default('created');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index('request_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
