<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
// use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

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
        $valid_data = $request->validate([
            'email' => ['required', 'email', 'unique:users'],
            'user_name' => ['max:20', 'alpha_dash', 'nullable', 'unique:users'],
            'password' => ['required', 'min:8']
    ]);

        $user_name = !is_null($valid_data['user_name'])?
            $valid_data['user_name']: self::token_generator();
        $email = $valid_data['email'];
        $password = bcrypt($valid_data['password']);

        $user = new User([
            'user_name' => $user_name,
            'email' => $email,
            'password' => $password
        ]);

        $signin = [
            'href' => parent::$base_route . 'users/sigin',
            'method' => 'POST',
            'params' => ['email', 'password']
        ];


        $response_code = 0;
        if($user->save()){
            $user->signin = $signin;
            $response = [
                'msg' => 'User Created',
                'user' => $user
            ];
            $response_code = 201;
        } else {
            $response = [
                'msg' => 'an error occured while creating user'
            ];
            $response_code = 404;
        }
        return \response()->json($response, $response_code);
    }


    public function signin(Request $request){
        $valid_data = $request->validate([
                'email' => ['required', 'email'],
                'password' => 'required'
        ]);

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

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
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
        ]);
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
