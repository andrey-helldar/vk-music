<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AudiosControllerTest extends TestCase
{
    public function testStoreAudios()
    {
        $this->json('POST', '/api/audios.user');
        //        $this->seeJson([
        //            'error' => [],
        //        ]);
    }
}
