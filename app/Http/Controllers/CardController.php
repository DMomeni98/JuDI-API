<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\User;
use App\Models\Label;

class CardController extends Controller
{

    private static $store_validation_rules = [
        'title' => ['required'],
        'description' => ['nullable'],
        'due' => ['nullable'],
        'label_name' => ['exists:labels,name', 'nullable'],
        'with_star' => ['nullable', 'boolean'],
        'category_id' =>['required', 'integer'],
        'is_repetitive' => ['bool', 'nullable'],
        'repeat_days' => ['nullable']
    ];


private static $update_validation_rules = [
        'title' => ['nullable'],
        'description' => ['nullable'],
        'due' => ['nullable'],
        'with_star' => ['nullable', 'boolean'],
        'category_id' => ['nullable', 'integer'],
        'is_done' => ['nullable', 'boolean'],
        'is_repetitive' => ['bool', 'nullable']
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
        
        //check repetitive
        if(! $request->input('is_repetitive') )
            return $this->create($request, $user_name, $request->input('due'), 0);
        else{
            $repeat_days = $request->validate(self::$store_validation_rules)['repeat_days'];
            $cards = [];
            $repetitive_id = Card::max('repetitive_id') + 1;
            foreach($repeat_days as $due)
                array_push($cards, $this->create($request, $user_name, $due, $repetitive_id));

            $response_code = 201;
            $response = $cards;
            return response()->json($response, $response_code);
        } 

    }


    // create an object from Card Model, is used by store() method
    public function create($request, $user_name, $due, $repetitive_id){
        $valid_data = $request->validate(self::$store_validation_rules);
        $label_name = null;
        if(!is_null($valid_data['label_name'])) {
            $label = Label::firstWhere(['name' => $request->input("label_name"),
                    'user_id' => $this->get_user_id($user_name)]);
            if (!is_null($label))
                $label_name = $valid_data['label_name'];
            else
                return response()->json(["msg" => "invalid label"], 404);
        }

        $card = new Card([
            'title' => $valid_data['title'],
            'description' => $valid_data['description'],
            'due' => $due,
            'with_star' => $this->set_default_to_with_star($valid_data['with_star'], $request),
            'category_id' => $valid_data['category_id'],
            'is_done' => $this->set_default_to_is_done($request),
            'user_id' => $this->get_user_id($user_name),
            'label_name' => $label_name,
            'repetitive_id' => $repetitive_id
        ]);
        //$card['repetitive_id'] = $repetitive_id;
        
        $response_code = 0;
        if($card->save()){
            $response = [
                'msg' => 'Card Created',
                'card' => $card
            ];
            $response_code = 201;
        } else {
            $response = ['msg' => 'an error occured while creating card'];
            $response_code = 404;
        }
        return response()->json($response, $response_code);
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
        foreach($cards as $card){
            if ($card['repetitive_id'] == 0)
                $card['is_repetitive'] = false;
            else
                $card['is_repetitive'] = true;
        }
        $response = [
            'msg' => 'Cards found',
            'cards' => $cards
        ];
        $response_code = 200;
        return response()->json($response, $response_code);
    }


    //show cards of one due date
    public function show_one_due($user_name, $due){
        $user_id = $this->get_user_id($user_name);
        $cards = Card::where('user_id', $user_id)->where('due', $due)->get();
        foreach($cards as $card){
            if ($card['repetitive_id'] == 0)
                $card['is_repetitive'] = false;
            else
                $card['is_repetitive'] = true;
        }
        $response = [
            'msg' => 'Cards found',
            'cards' => $cards
        ];
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
        if ($card['repetitive_id'] == 0)
                $card['is_repetitive'] = false;
            else
                $card['is_repetitive'] = true;
        $response = ['card' => $card];
        $response_code = 200;
        return response()->json($response, $response_code);
    }


    public function update_root(Request $request, $user_name, $card_id){
        // check if user is signed in
        if(! $this->check_signin($user_name)['match'])
            return $this->check_signin($user_name)['response'];
        if(! $this->match_request_with_user($user_name))
            return response()->json("user didnt match", 401);
        $card = Card::where('id', $card_id)->first();
        $card['is_done'] = $request->validate(self::$update_validation_rules)['is_done'];
        $card['due'] = $request->validate(self::$update_validation_rules)['due'];
        $card->save();
        if($card['repetitive_id'] == 0){
            $response = [
                'msg'  => "card updated successfully",
                'card' => $this->update($request, $user_name, $card_id)                 
            ];
            return response()->json($response, 200);
        }
        else{
            $repetitive_id = $card['repetitive_id'];
            $all_repeats = Card::where('repetitive_id', $repetitive_id)->get();
            $cards = [];
            foreach($all_repeats as $one_card)
                array_push($cards, $this->update($request, $user_name, $one_card['id']));
            $response = [
                'msg' => 'cards updated successfully',
                'cards' => $cards
            ];
            return response()->json($response, 200);
            
        }

    }
    
    
    public function update($request, $user_name, $card_id)
    {
        $valid_data = $request->validate(self::$update_validation_rules);
        $curr_card = Card::where('id', $card_id)->first();
        
        if(! is_null($valid_data['title']))
            $curr_card->title = $valid_data['title'];
        if(! is_null($valid_data['description']))
            $curr_card->description = $valid_data['description'];
        if(! is_null($valid_data['with_star']))
            $curr_card->with_star = $valid_data['with_star'];
        if(! is_null($valid_data['category_id']))
            $curr_card->category_id = $valid_data['category_id'];
        
        $curr_card->save();
        //$response['msg'] = 'card updated';
        //$response['card'] = $curr_card;
        //$response_code = 200;
        return $curr_card;
}


// show week cards
    public function weekboard(Request $request, $user_name){
        $days = $request->input('days');
        $one_day_cards = [];
        foreach($days as $day){
            array_push($one_day_cards, $this->show_one_due($user_name, $day)->original['cards']);
        }
        $response = [
            'msg' => 'cards returned seuccessfully',
            'cards' => $one_day_cards
        ];
        $response_code = 200;
        return response()->json($response, $response_code);
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

}
