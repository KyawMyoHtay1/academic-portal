<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('file_path'); // Path to uploaded file
            $table->string('original_filename');
            $table->text('comments')->nullable(); // Student's submission comments
            $table->decimal('score', 5, 2)->nullable(); // Score out of max_score
            $table->text('feedback')->nullable(); // Teacher's feedback
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->enum('status', ['submitted', 'graded', 'returned'])->default('submitted');
            $table->timestamps();

            // Prevent duplicate submissions (one submission per student per assignment)
            $table->unique(['assignment_id', 'student_id']);
            $table->index(['assignment_id', 'status']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
