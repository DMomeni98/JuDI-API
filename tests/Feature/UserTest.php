<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{

    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *  user sign up works well!
     * @return void
     */
    public function testStore()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest'
        ])->json('POST', '/api/users',
            ['user_name' => 'homalsdkdjhkjndg',
             'password' => '12345678',
             'email' => 'test48kdsdff@test.com'
            ]);
             

        $response
            ->assertStatus(201)
            ->assertJson([
                'msg' => 'User Created',
            ]);
    }


    // doesnt sign up a user with duplicate email
    public function testTakenEmail()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest'
        ])->json('POST', '/api/users',
            ['user_name' => 'hello',
             'password' => '12345678',
             'email' => 'test@test.com'
            ]);
             

        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' =>
                    ['email'=>['The email has already been taken.']]    
                ]);
    }

    // doesnt sign up a user with duplicate user_name
    public function testTakenUserName()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest'
        ])->json('POST', '/api/users',
            ['user_name' => 'hello',
             'password' => '12345678',
             'email' => 'test@test.com'
            ]);
             

        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' =>
                    ['user_name'=>['The user name has already been taken.']]    
                ]);
    }

     // sign up a user with null user_name
     public function testNullUserName()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'application/json',
             'X-Requested-With' => 'XMLHttpRequest'
         ])->json('POST', '/api/users',
             ['user_name' => '',
              'password' => '12345678',
              'email' => 'test101@test.com'
             ]);
              
 
         $response
             ->assertStatus(201)
             ->assertJson([
                'msg' => 'User Created',
             ]);
     }
 

    // doesnt sign up a user with  null email
    public function testNullEmail()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest'
        ])->json('POST', '/api/users',
            ['user_name' => 'homa',
             'password' => '12345678',
             'email' => ''
            ]);
             

        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' =>
                    ['email'=>['The email field is required.']]    
                ]);
    }

    // doesnt sign up a user with  null password
    public function testNullPassword()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest'
        ])->json('POST', '/api/users',
            ['user_name' => 'homa',
             'password' => '',
             'email' => 'test10@test.com'
            ]);
             

        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' =>
                    ['password'=>['The password field is required.']]    
                ]);
    }


    //test sign in
    public function signinTest()
    {
        $responce = $this->get('api/users');
    }
}
