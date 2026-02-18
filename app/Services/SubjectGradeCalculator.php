<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Collection;

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
        return $this->calculateForSubjectStudents($subjectId, [$studentId])[$studentId] ?? $this->emptySummary();
    }

    /**
     * Batch calculate suggested grades for many students in one subject.
     *
     * @return array<int, array<string, mixed>> keyed by student_id
     */
    public function calculateForSubjectStudents(int $subjectId, array $studentIds): array
    {
        $studentIds = $this->normalizeIds($studentIds);
        if ($studentIds === []) {
            return [];
        }

        $assignments = Assignment::query()
            ->where('subject_id', $subjectId)
            ->where('status', Assignment::STATUS_PUBLISHED)
            ->orderBy('due_date', 'asc')
            ->get(['id', 'subject_id', 'title', 'max_score', 'due_date']);

        $assignmentIds = $assignments->pluck('id')->all();

        $submissionsByStudent = collect();
        if ($assignmentIds !== []) {
            $submissionsByStudent = AssignmentSubmission::query()
                ->with('grader:id,name')
                ->whereIn('assignment_id', $assignmentIds)
                ->whereIn('student_id', $studentIds)
                ->get([
                    'id',
                    'assignment_id',
                    'student_id',
                    'score',
                    'feedback',
                    'graded_by',
                    'graded_at',
                    'status',
                ])
                ->groupBy('student_id')
                ->map(fn (Collection $rows) => $rows->keyBy('assignment_id'));
        }

        $results = [];
        foreach ($studentIds as $studentId) {
            $results[$studentId] = $this->buildSummary(
                $assignments,
                $submissionsByStudent->get($studentId, collect())
            );
        }

        return $results;
    }

    /**
     * Batch calculate suggested grades for one student across many subjects.
     *
     * @return array<int, array<string, mixed>> keyed by subject_id
     */
    public function calculateForStudentSubjects(array $subjectIds, int $studentId): array
    {
        $subjectIds = $this->normalizeIds($subjectIds);
        if ($subjectIds === []) {
            return [];
        }

        $assignments = Assignment::query()
            ->whereIn('subject_id', $subjectIds)
            ->where('status', Assignment::STATUS_PUBLISHED)
            ->orderBy('due_date', 'asc')
            ->get(['id', 'subject_id', 'title', 'max_score', 'due_date']);

        $assignmentIds = $assignments->pluck('id')->all();

        $submissionsByAssignment = collect();
        if ($assignmentIds !== []) {
            $submissionsByAssignment = AssignmentSubmission::query()
                ->with('grader:id,name')
                ->whereIn('assignment_id', $assignmentIds)
                ->where('student_id', $studentId)
                ->get([
                    'id',
                    'assignment_id',
                    'student_id',
                    'score',
                    'feedback',
                    'graded_by',
                    'graded_at',
                    'status',
                ])
                ->keyBy('assignment_id');
        }

        $assignmentsBySubject = $assignments->groupBy('subject_id');
        $results = [];

        foreach ($subjectIds as $subjectId) {
            $results[$subjectId] = $this->buildSummary(
                $assignmentsBySubject->get($subjectId, collect()),
                $submissionsByAssignment
            );
        }

        return $results;
    }

    /**
     * Get assignment breakdown for a student in a subject.
     */
    public function getAssignmentBreakdown(int $subjectId, int $studentId): array
    {
        return $this->calculateSuggestedGrade($subjectId, $studentId);
    }

    /**
     * @param  Collection<int, \App\Models\Assignment>  $assignments
     * @param  Collection<int, \App\Models\AssignmentSubmission>  $submissionsByAssignment
     * @return array<string, mixed>
     */
    private function buildSummary(Collection $assignments, Collection $submissionsByAssignment): array
    {
        $breakdown = [];
        $gradedCount = 0;
        $totalPercentage = 0.0;

        foreach ($assignments as $assignment) {
            $submission = $submissionsByAssignment->get($assignment->id);

            $assignmentData = [
                'assignment_id' => $assignment->id,
                'title' => $assignment->title,
                'max_score' => $assignment->max_score,
                'due_date' => $assignment->due_date?->format('Y-m-d'),
                'submitted' => $submission !== null,
                'score' => null,
                'percentage' => null,
                'graded' => false,
                'graded_by' => null,
            ];

            if ($submission && $submission->isGraded() && $submission->score !== null) {
                $maxScore = (float) $assignment->max_score;
                $percentage = $maxScore > 0
                    ? round((((float) $submission->score) / $maxScore) * 100, 2)
                    : null;

                $assignmentData['score'] = (float) $submission->score;
                $assignmentData['percentage'] = $percentage;
                $assignmentData['graded'] = true;
                $assignmentData['graded_by'] = $submission->grader?->name;
                $assignmentData['graded_at'] = $submission->graded_at?->format('Y-m-d H:i');
                $assignmentData['feedback'] = $submission->feedback;

                if ($percentage !== null) {
                    $totalPercentage += $percentage;
                    $gradedCount++;
                }
            }

            $breakdown[] = $assignmentData;
        }

        $totalAssignments = $assignments->count();
        $computedGrade = $gradedCount > 0 ? round($totalPercentage / $gradedCount, 2) : null;

        return [
            'computed_grade' => $computedGrade,
            'breakdown' => $breakdown,
            'total_assignments' => $totalAssignments,
            'graded_assignments' => $gradedCount,
            'ungraded_assignments' => $totalAssignments - $gradedCount,
            'has_assignments' => $totalAssignments > 0,
        ];
    }

    /**
     * @param  array<int, int|string>  $ids
     * @return array<int, int>
     */
    private function normalizeIds(array $ids): array
    {
        $normalized = [];

        foreach ($ids as $id) {
            $intId = (int) $id;
            if ($intId > 0) {
                $normalized[$intId] = $intId;
            }
        }

        return array_values($normalized);
    }

    /**
     * @return array<string, mixed>
     */
    private function emptySummary(): array
    {
        return [
            'computed_grade' => null,
            'breakdown' => [],
            'total_assignments' => 0,
            'graded_assignments' => 0,
            'ungraded_assignments' => 0,
            'has_assignments' => false,
        ];
    }
}
