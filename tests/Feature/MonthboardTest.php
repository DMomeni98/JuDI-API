<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MonthboardTest extends TestCase
{
    use DatabaseTransactions;
    private static $headers =  [
        'Content-Type' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest'
    ];

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    //update
    public function testStore(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);

        $response = $this->withHeaders(self::$headers)->
        json('PUT', 'api/users/homa/monthboard/update',
            ['note' => 'hellooooooo'
            ]);

        $response
        ->assertStatus(201);
    }

    //show monthboard
    public function testShow(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);

        $response = $this->withHeaders(self::$headers)->
        json('GET', 'api/users/homa/monthboard');
    
        $response
        ->assertStatus(200);
    }

    //delete monthboard
    public function testDelete(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);
        $response = $this->withHeaders(self::$headers)->
        json('DELETE', 'api/users/homa/monthboard/delete');
        
        $response
        ->assertStatus(200);  
    }
}
