<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CardTest extends TestCase
{
    use DatabaseTransactions;
    private static $headers =  [
        'Content-Type' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest'
    ];

    
    // create card
    public function testStore()
    {
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);

        $response = $this->withHeaders(self::$headers)->
        json('POST', 'api/users/homa/cards',
            ['title' => 'sport',
             "description" => "12345678",
             "due" => "2020-11-17T16:40",
             "with_star" => true,
             "category_id" =>"5",
             "is_done" => true,
             "is_repetetive" => false,
             "label" => "None"
            ]);
        
        //$card = $response->original['card'];  
        //$id = $card['id']; 
            
        $response
            ->assertStatus(201)
            ->assertJson([
                'msg' => 'Card Created'
            ]);
    }

    // test repetitive cards
    public function testStoreRepetitive(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);

        $response = $this->withHeaders(self::$headers)->
        json('POST', '/api/users/homa/cards',
            ['title' => 'sport',
             "description" => "12345678",
             "with_star" => true,
             "category_id" =>"5",
             "is_done" => true,
             "is_repetitive" => true,
             "repeat_days" => ["2020-2-2", "2020-2-4"],
             "label" => "None"
            ]);
             
        $response
            ->assertStatus(201);
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
            ->assertStatus(200);
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
            ->assertStatus(200);
    }


    //update a card
    public function testUpdate(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);

        $response = $this->withHeaders(self::$headers)->
        json('PUT', 'api/users/homa/cards/update/1',
            ['title' => 'sport',                 
             "description" => "12345678",
             "due" => "2020-11-17T16:40",
             "with_star" => true,
             "category_id" =>"5",
             "is_done" => true,
             "is_repetitive" => false
            ]);

        $response
            ->assertStatus(200);
        
        
    }


    //update a repetitive card
    public function testRepetitiveUpdate(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);

        $response = $this->withHeaders(self::$headers)->
        json('PUT', 'api/users/homa/cards/update/2',
            ['title' => 'sport',                 
             "description" => "12345",
             "due" => "2020-11-17T16:40",
             "with_star" => true,
             "category_id" =>"5",
             "is_done" => true,
             "is_repetitive" => true
            ]);

        $response
            ->assertStatus(200);
        
        
    }


    // update with all feilds null 
    public function testUpdateNull(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);

        $response = $this->withHeaders(self::$headers)->
        json('PUT', 'api/users/homa/cards/update/1',
            ['title' => '',                 
             "description" => "",
             "due" => "",
             "with_star" => "",
             "category_id" =>"",
             "is_done" => false,
             "is_repetitive" => ""
            ]);

        $response
            ->assertStatus(200);
    }
    
    
    
    //delete a card
    public function testDestroy(){
        $response = $this->withHeaders(self::$headers)
        ->json('POST', '/api/users/signin',
            ['password' => '12345678',
             'user_name' => 'homa'
            ]);
        
        $response = $this->withHeaders(self::$headers)
        ->json('DELETE', 'api/users/homa/cards/remove/1');
    
        $response
        ->assertStatus(200);
        
    }
}
