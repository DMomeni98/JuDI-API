<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CardTest extends TestCase
{
    private static $headers =  [
        'Content-Type' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest'
    ];

    
    // create card
    public function teststore()
    {
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);

        $response = $this->withHeaders(self::$headers)->
        json('POST', '/api/users/homa/cards',
            ['title' => 'sport',
             "description" => "12345678",
             "due" => "2020-11-17T16:40",
             "with_star" => true,
             "category_id" =>"5",
             "is_done" => true
            ]);
             
        $response
            ->assertStatus(201)
            ->assertJson([
                'msg' => 'Card Created'
            ]);
    }

    // show cards
    public function testShow(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);
        
        $response = $this->withHeaders(self::$headers)
        ->json('GET', 'api/users/homa/cards/get');

        $response
            ->assertStatus(201);
    }

    //show cards of one due date
    public function testShowDue(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);
        
        $response = $this->withHeaders(self::$headers)
        ->json('GET', 'api/users/homa/cards/get/2020-11-17 16:40:00');

        $response
            ->assertStatus(201);
    }


    //update a card
    public function testUpdate(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345',
             'user_name' => 'homa'
            ]);

        $response = $this->withHeaders(self::$headers)->
        json('PUT', 'api/users/homa/cards/update/17',
            ['title' => 'sport',                 
             "description" => "12345678",
             "due" => "2020-11-17T16:40",
             "with_star" => true,
             "category_id" =>"5",
             "is_done" => true
            ]);

        $response
        ->assertJson([
            'msg' => 'card updated'
        ])
        ->assertStatus(200);
        
        
    }

    //delete a card
    public function testDestroy(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345',
             'user_name' => 'homa'
            ]);
        
        $response = $this->withHeaders(self::$headers)
        ->json('GET', 'api/users/homa/cards/remove/16');
    
        $response
        ->assertStatus(200);
        
    }
}
