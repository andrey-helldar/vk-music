<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AppControllerTest extends TestCase
{
    public function testGetTopmenu()
    {
        $this->json('GET', '/api/topmenu')->seeJsonStructure([
            'response' => [
                '*' => [
                    'url',
                    'title',
                    'is_active',
                ],
            ],
        ]);
    }

    public function testGetParams()
    {
        $this->json('GET', '/api/vk.params')->seeJsonStructure([
            'response' => [
                'client_id',
                'redirect_uri',
                'display',
                'scope',
                'response_type',
                'v',
            ],
        ]);
    }
}
