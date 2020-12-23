<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class LabelController extends Controller
{
    private static $store_validation_rules = [
        'name' => 'required',
        ]; 
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth:api');
    }   
    
    public function index(Request $request, $user_name)
    {
        $user = User::where("user_name", $user_name)->first();
        if (!is_null($user)){
            $labels = Label::select('id', 'name')->where("user_id", $user["id"])->get();
            if(count($labels) == 0){
                $last = Label::all()->last();
                DB::insert('insert into labels (id, user_id, name) values (?, ?, ?)', [$last['id']+1, $user['id'], 'None']);
            }
            $labels = Label::select('id', 'name')->where("user_id", $user["id"])->get();
            
            return response()->json($labels, 200);
            
        } else {
         return response()->json(["msg" => "user not found"], 404);
        }
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
        $request->validate(self::$store_validation_rules);

        $user_id = $this->user()->original->id;
        $label = New Label(['name' => $request->input('name'),
         'user_id' => $user_id]);

         $response_code = 0;
         if($label->save()){
             $response = [
                 'msg' => 'Label Created',
                 'label' => $label
             ];
             $response_code = 201;
         } else {
             $response = ['msg' => 'an error occured while creating label'];
             $response_code = 404;
         }
         return response()->json($response, $response_code);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Label  $label
     * @return \Illuminate\Http\Response
     */
    public function show(Label $label)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Label  $label
     * @return \Illuminate\Http\Response
     */
    public function edit(Label $label)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Label  $label
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Label $label)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Label  $label
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $user_name, $id)
    {
        $label = Label::find($id);
        if (!is_null($label)){
            $label->delete();
            return response()->json(["msg" => "deleted"], 200);
        }else {
        return response()->json(["msg" => "label id not found"], 200);
        }
    }

    public function user()
    {
        return response()->json(auth()->user());
    }
}
