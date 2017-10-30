<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;



class Web_Waypoints extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function ShowDummyRoute_Vehicle1Route1 ()
    {
        $response = $this->get('/vehicles/1/routes/1/');

        $response->assertStatus(404);
    }
}

