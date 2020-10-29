<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private static $store_validation_rules = [
        'email' => ['required', 'email', 'unique:users'],
        'user_name' => ['min:3', 'max:20', 'alpha_dash', 'nullable', 'unique:users'],
        'password' => ['required', 'min:8', 'confirmed']];

    private static $signin_validation_rules = [
        'email' => ['required', 'email'],
        'password' => 'required'
    ];
   
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['store','signin']]);
    }
   
    public function store(Request $request){
        $valid_data = $request->validate( self::$store_validation_rules);
        
        $user_name = !is_null($valid_data['user_name'])?
            $valid_data['user_name']: self::token_generator();
        $email = $valid_data['email'];
        $password = bcrypt($valid_data['password']);

        $user = new User([
            'user_name' => $user_name,
            'email' => $email,
            'password' => $password
        ]);
       
        $response_code = 0;
        if($user->save()){
            $user->signin = [
                'href' => parent::$base_route . 'users/sigin',
                'method' => 'POST',
                'params' => ['email', 'password']
            ];;
            $response = [
                'msg' => 'User Created',
                'user' => $user
            ];
            $response_code = 201;
        } else {
            $response = ['msg' => 'an error occured while creating user'];
            $response_code = 404;
        }
        return response()->json($response, $response_code);
    }


    public function signin(Request $request){
        $valid_data = $request->validate(self::$signin_validation_rules);
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
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


    public function index(Request $request, $user_name){
        $response = [];
        $response_code = 0;
        Validator::make(['user_name' => $user_name], [
            'user_name' => 'required|exists:users',
        ])->validate();
        if ($this->match_request_with_user($user_name)){
            $response = [
            'msg' => "operation successful", 
            'user' => $this->me()
            ];
            $response_code = 200;
        }
        else{
            $response = [
            'msg' => "users didn't match",
            'user' => null
            ];
            $response_code = 401;
        }
        return response()->json($response, $response_code);   
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signout()
    {
        auth()->logout();
        return response()->json(['msg' => 'Successfully logged out'], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function match_request_with_user($user_name){
        $result = false;
        $user = User::where('user_name', $user_name)->first();
        if ($this->me()->original == $user){
            $result = true;
        }
        return $result;
    }

    public function update_profile(Request $request){
        $response = [];
        $response_code = 0;
        
        Validator::make(['user_name' => $user_name], [
            'user_name' => 'required|exists:users',
        ])->validate();

        if ($this->match_request_with_user($user_name)){
            $request->validate(['new_user_name' => ['min:3', 'max:20', 'alpha_dash', 'unique:users'],
                'full_name' => ['nullable', 'max:70', 'alpha'],
                'new_password' =>  ['min:8', 'nullable'],
                'password_confirmation' => 'same:new_password'
            ]);
            $full_name = $request->input('full_name');
            $new_user_name = $request->input('new_user_name');
            $new_password = bcrypt($request->input('new_password'));
            $user = $this->me()->original;
            $user->full_name = $full_name;
            $user->password = $new_password;
            $user->save();
        }
        else{
            $response = [
            'msg' => "users didn't match",
            'user' => null
            ];
            $response_code = 401;
        }
        return response()->json($response, $response_code);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }

    public static function token_generator($len = 7){
        $chars = "ABCDEFGHIGKLMNOPQRSUVWXYZ";
        $chars .= "0123456789";
        $chars .= "abcdefghigklmnopqrstuvwxyz";
        $max = \strlen($chars) - 1;
        $token = "";
        for ($i=0; $i < $len; $i++) { 
            $token .= $chars[rand(0, $max)];
        }
        return $token;
    }
}
