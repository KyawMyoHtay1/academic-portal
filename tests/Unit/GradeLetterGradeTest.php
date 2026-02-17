<?php

namespace Tests\Unit;

use App\Models\Grade;
use PHPUnit\Framework\TestCase;

class GradeLetterGradeTest extends TestCase
{
    public function test_zero_score_maps_to_f(): void
    {
        $grade = new Grade();
        $grade->score = 0;

        $this->assertSame('F', $grade->letter_grade);
    }
}

