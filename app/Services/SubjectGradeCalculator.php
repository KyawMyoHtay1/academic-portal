<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Subject;
use App\Models\Student;

class SubjectGradeCalculator
{
    /**
     * Calculate suggested subject grade based on graded assignments.
     * 
     * Returns:
     * - computed_grade: Average percentage from all graded assignments (0-100)
     * - breakdown: Array of assignment details with scores
     * - total_assignments: Total number of assignments for this subject
     * - graded_assignments: Number of assignments that have been graded
     * - ungraded_assignments: Number of assignments not yet graded
     */
    public function calculateSuggestedGrade(int $subjectId, int $studentId): array
    {
        $subject = Subject::findOrFail($subjectId);
        $student = Student::findOrFail($studentId);

        // Get all published assignments for this subject
        $assignments = Assignment::where('subject_id', $subjectId)
            ->where('status', Assignment::STATUS_PUBLISHED)
            ->orderBy('due_date', 'asc')
            ->get();

        $breakdown = [];
        $gradedCount = 0;
        $totalPercentage = 0;

        foreach ($assignments as $assignment) {
            // Get submission for this student
            $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->where('student_id', $studentId)
                ->first();

            $assignmentData = [
                'assignment_id' => $assignment->id,
                'title' => $assignment->title,
                'max_score' => $assignment->max_score,
                'due_date' => $assignment->due_date->format('Y-m-d'),
                'submitted' => $submission !== null,
                'score' => null,
                'percentage' => null,
                'graded' => false,
            ];

            // If submission exists and is graded
            if ($submission && $submission->isGraded() && $submission->score !== null) {
                $percentage = $submission->percentage ?? round(($submission->score / $assignment->max_score) * 100, 2);
                
                $assignmentData['score'] = (float) $submission->score;
                $assignmentData['percentage'] = $percentage;
                $assignmentData['graded'] = true;
                $assignmentData['graded_at'] = $submission->graded_at?->format('Y-m-d H:i');
                $assignmentData['feedback'] = $submission->feedback;

                $totalPercentage += $percentage;
                $gradedCount++;
            }

            $breakdown[] = $assignmentData;
        }

        // Calculate average (only from graded assignments)
        $computedGrade = $gradedCount > 0 ? round($totalPercentage / $gradedCount, 2) : null;

        return [
            'computed_grade' => $computedGrade,
            'breakdown' => $breakdown,
            'total_assignments' => $assignments->count(),
            'graded_assignments' => $gradedCount,
            'ungraded_assignments' => $assignments->count() - $gradedCount,
            'has_assignments' => $assignments->count() > 0,
        ];
    }

    /**
     * Get assignment breakdown for a student in a subject.
     */
    public function getAssignmentBreakdown(int $subjectId, int $studentId): array
    {
        return $this->calculateSuggestedGrade($subjectId, $studentId);
    }
}
