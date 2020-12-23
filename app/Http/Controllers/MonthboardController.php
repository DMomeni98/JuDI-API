<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Monthboard;

use Illuminate\Http\Request;

class MonthboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }   

    // check request
    public function update(Request $request, $user_name){
        $user = User::where('user_name', $user_name)->first();
        $board = Monthboard::where('user_id', $user['id'])->first();

        if(response()->json(auth()->user())->original != $user)
            return response()->json("user didnt match", 401);
        if($board == NULL){
            $monthboard = new Monthboard([
                'note' => $request->input('note'),
                'user_id' => $user['id']
            ]);
            if($monthboard->save())
                return response()->Json($monthboard, 201);
            else
                return response()->json("note not saved", 404);
        }
        else{
            $board['note'] = $request->input('note');
            if($board->save())
                return response()->json($board, 200);
            else
                return response()->json("not saved", 404);
        }

    }

    //show monthboard
    public function show(Request $request, $user_name){
        $user = User::where('user_name', $user_name)->first();
        if(response()->json(auth()->user())->original != $user)
            return response()->json("user didnt match", 401);

        $board = Monthboard::where('user_id', $user['id'])->first();
        if($board == NULL){
            $board = new Monthboard([
                'note' => 'type your month goals here',
                'user_id' => $user['id']
            ]);
        }
        return response()->json($board['note'], 200);
    }

    //delete monthboard
    public function delete(Request $request, $user_name){
        $user = User::where('user_name', $user_name)->first();
        if(response()->json(auth()->user())->original != $user)
            return response()->json("user didnt match", 401);
            Monthboard::where('user_id', $user['id'])->delete();
        return response()->json('deleted', 200);
    }
}
