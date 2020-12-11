<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class labeltest extends TestCase
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
    // public function testExample()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    // store label
    public function testStore()
    {
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);

        $response = $this->withHeaders(self::$headers)->
        json('POST', '/api/users/homa/labels',
            ['name' => 'sport'
            ]);
            
        $response
            ->assertStatus(201)
            ->assertJson([
                'msg' => 'Label Created'
            ]);
    }

    // show labels
    public function testIndex(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);
        
        $response = $this->withHeaders(self::$headers)
        ->json('GET', 'api/users/homa/labels');

        $response
            ->assertStatus(200);
    }

    //delete a label
    public function testDestroy(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);
        
        $response = $this->withHeaders(self::$headers)
        ->json('DELETE', 'api/users/homa/labels/4');
    
        $response
        ->assertStatus(200);
        
    }
}
