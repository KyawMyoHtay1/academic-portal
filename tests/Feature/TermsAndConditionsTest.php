<?php

namespace Tests\Feature;

use Tests\TestCase;

class TermsAndConditionsTest extends TestCase
{
    public function test_terms_and_conditions_page_can_be_rendered()
    {
        $response = $this->get('/terms-and-conditions');

        $response->assertStatus(200);
    }
}
