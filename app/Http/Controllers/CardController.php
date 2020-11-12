<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\User;

class CardController extends Controller
{

    private static $store_validation_rules = [
        'title' => ['required'],
        'description' => ['required', 'nullable'],
        'due' => ['nullable'],
        'with_star' => ['nullable', 'boolean'],
        'category_id' =>['required', 'integer']
    ];

    
    
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $user_name)
    {
        // check if user is signed in
        if(! $this->check_signin($user_name)['match'])
            return $this->check_signin($user_name)['response'];
        
        $card = $this->create($request, $user_name);
        $response_code = 0;
        if($card->save()){
            $response = [
                'msg' => 'Card Created',
                'user' => $card
            ];
            $response_code = 201;
        } else {
            $response = ['msg' => 'an error occured while creating card'];
            $response_code = 404;
        }
        return response()->json($response, $response_code);
    }


    // create an object from Card Model, is used by store() method
    public function create($request, $user_name){
        $valid_data = $request->validate(self::$store_validation_rules);
        $card = new Card([
            'title' => $valid_data['title'],
            'description' => $valid_data['description'],
            'due' => $valid_data['due'],
            'with_star' => $this->set_default_to_with_star($valid_data['with_star'], $request),
            'category_id' => $valid_data['category_id'],
            'is_done' => $this->set_default_to_is_done($request),
            'user_id' => $this->get_user_id($user_name)
        ]);
        return $card;
    }


    //check sign in and auth, is used by store() method
    public function check_signin($user_name){

        if(!$this->match_request_with_user($user_name)){
            $response = [
                'msg' => "users didn't match",
                'user' => null
            ];
            $response_code = 401;
            return ['match' => false, 'response' => response()->json($response, $response_code)];
        }
        else
            return ['match' => true];
    }


    public function match_request_with_user($user_name){
        $result = false;
        $user = User::where('user_name', $user_name)->first();
        if ($this->me()->original == $user){
            $result = true;
        }
        return $result;
    }

    
    /**
     * Get the authenticated User.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }


    //get user_id from $user_name, is used by create() method
    public function get_user_id($user_name){
        //get user_id from $user_name    
        $user = User::where('user_name', $user_name)->first();
        return $user->id;
    }

    //set a default value to with_star, is used by create() method
    public function set_default_to_with_star($feild, Request $request){

        if(! empty($request->input('with_star')) )    
            return $feild;
        else
            return false;

    }
    
     //set a default value to is_done, is used by create() method
     public function set_default_to_is_done(Request $request){

        if(! empty($request->input('is_done')) )    
            return $request->input('is_done');
        else
            return false;

    }
   


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($user_name)
    {
        $user_id = $this->get_user_id($user_name);
        $cards = Card::where('user_id', $user_id)->get();
        //array_push($cards, Card::select('id', 'title', 'description', 'due', 'with_star', 'category_id', 'is_done')->where('user_id', $user_id)->get());
        $response = [
            'msg' => 'Cards found',
            'cards' => $cards
        ];
        $response_code = 201;
        return response()->json($response, $response_code);
    }


    //show cards of one due date
    public function show_one_due($user_name, $due){
        $user_id = $this->get_user_id($user_name);
        $cards = Card::where('user_id', $user_id)->where('due', $due)->get();
        //array_push($cards, Card::select('id', 'title', 'description', 'due', 'with_star', 'category_id', 'is_done')->where('user_id', $user_id)->get());
        if(count($cards) == 0){
            $response = ['msg' => 'no card found!'];
            $response_code = 404;
            return response()->json($response, $response_code);
        }
        $response = [
            'msg' => 'Cards found',
            'cards' => $cards
        ];
        $response_code = 201;
        return response()->json($response, $response_code);
    }

   
   
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_name, $id)
    {
        $card = Card::where('id', (int)$id)->get();
        if(count($card) == 0){
            $response = ['message' => 'no such a card with this id'];
            $response_code = 404;
            return response()->json($response, $response_code);
        }
        Card::where('id', (int)$id)->delete();
        $response = ['message' => 'card removed'];
        $response_code = 200;
        return response()->json($response, $response_code);
    }
    
    //show one card
    public function show_one_card($user_name, $card_id){
        $card = Card::where('id', $card_id)->get();
        if(count($card) == 0){
            $response = ['message' => 'no such a card with this id'];
            $response_code = 404;
            return response()->json($response, $response_code);
        }
        $response = ['card' => $card];
        $response_code = 200;
        return response()->json($response, $response_code);
    }
    
    
}
