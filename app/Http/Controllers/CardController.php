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
        'due' => ['required', 'nullable'],
        'with_star' => ['required', 'nullable', 'boolean'],
        'category_id' =>['required', 'integer']
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $user_name)
    {
        $valid_data = $request->validate(self::$store_validation_rules);
        
        if(!$this->match_request_with_user($user_name)){
            $response = [
                'msg' => "users didn't match",
                'user' => null
            ];
            $response_code = 401;
            return response()->json($response, $response_code);
        }
        
        $card = new Card([
            'title' => $valid_data['title'],
            'description' => $valid_data['description'],
            'due' => $valid_data['due'],
            'with_star' => $valid_data['with_star'],
            'category_id' => $valid_data['category_id'],
            'user_id' => $user_name
        ]);

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

    
      /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function destroy($id)
    {
        //
    }
}
