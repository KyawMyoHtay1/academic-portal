<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Keep auth tests deterministic regardless of local .env reCAPTCHA keys.
        config()->set('recaptcha.site_key', '');
        config()->set('recaptcha.secret_key', '');
    }
}
