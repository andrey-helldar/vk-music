<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnknownMethodTest extends TestCase
{
    public function testUnknownMethod()
    {
        $this->json('POST', '/api/other')->seeJson([
            'error_code' => 1,
            'error'      => 'Unknown method.',
        ]);

        $this->json('GET', '/api/other')->seeJson([
            'error_code' => 1,
            'error'      => 'Unknown method.',
        ]);
    }
}
