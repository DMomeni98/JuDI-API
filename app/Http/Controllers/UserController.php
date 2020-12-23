<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private static $store_validation_rules = [
        'email' => ['required', 'email', 'unique:users'],
        'user_name' => ['nullable', 'min:3', 'max:20', 'alpha_dash', 'unique:users'],
        'password' => ['required', 'min:8', 'confirmed']];

    private static $signin_validation_rules = [
        'user_name' => ['required'],
        'password' => 'required'
    ];
   
    private static $avatars_path = "/storage/uploads/avatars/";
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['store','signin', 'ranking']]);
    }
   
    public function store(Request $request){
        $valid_data = $request->validate( self::$store_validation_rules);
        
        $user_name = $request->has("user_name") && !is_null($valid_data['user_name'])?
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
            ];
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
        $credentials = request(['user_name', 'password']);

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
            $user = json_decode($this->me()->original, true);
            $avatar = public_path() . self::$avatars_path . "user_id_" . $this->me()->original->id;

            if (file_exists($avatar.".jpeg")){
                $user += ["avatar" => asset(self::$avatars_path. "user_id_".$this->me()->original->id.".jpeg")];
            }
             elseif (file_exists($avatar.".png")){
                $user += ["avatar" => asset(self::$avatars_path. "user_id_".$this->me()->original->id.".png")];
            } else {
                $user += ["avatar" => null];
            }
            $response = [
            'msg' => "operation successful", 
            'user' => $user
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
    public function signout($msg = "Successfully logged out")
    {
        auth()->logout();
        return response()->json(['msg' => $msg], 200);
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

    public function update_profile(Request $request, $user_name){
        $response = [];
        $response_code = 0;
        
        Validator::make(['user_name' => $user_name], [
            'user_name' => 'required|exists:users',
        ])->validate();

        if ($this->match_request_with_user($user_name)){

            $request->validate(['user_name' => ['min:3', 'max:20', 'alpha_dash', 'nullable'],
                'full_name' => ['nullable', 'max:70', 'regex:/^[\pL\s\-]+$/u', 'nullable']
            ]);
            
            $curr_user = User::where("user_name", $user_name)->first();
            //get new information from request
            $new_full_name = $request->input('full_name');
            $new_user_name = $request->input('user_name');
            $new_email = $request->input('email');
            //$new_password = $request->input('password');
            
            // search databese for new_email and new_user_name
            $token_user_name = User::where("user_name", $new_user_name)->first();
            $token_email = User::where("email", $new_email)->first();

            // taken username
            if(is_null($token_user_name)){
                if(!is_null($new_user_name))
                    $curr_user->user_name = $new_user_name;
            } elseif(!is_null($token_user_name) && $token_user_name->user_name != $user_name){
                $response['error'] = 'sorry, this username is taken.';
                return response()->json($response, 401);
            }

            // taken email
            if(is_null($token_email)){
                if(! is_null($new_email))
                    $curr_user->email = $new_email;
            } elseif(!is_null($token_email) && $token_email->user_name != $user_name){
                $response['error'] = 'sorry, this email is taken.';
                return response()->json($response, 401);
            }

            if(! is_null($new_full_name))
                $curr_user->full_name = $new_full_name;
            //if(! is_null($new_password))
                //$curr_user->password = bcrypt($new_password);
            $curr_user->save();

            $response['msg'] = 'user updated';
            $response['user'] = $curr_user;
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


    public function change_password(Request $request, $user_name)
    {
        if($this->match_request_with_user($user_name)){
            $validated = $request->validate(
                ['new_password' => ['required', 'min:8', 'string', 'confirmed']
                ]);
            $user = User::where('user_name', $user_name)->first();
            
            // check old password
            if(! password_verify($request->input('old_password'), $user['password'])){
                $response = [
                    'msg' => "old password is not correct"
                    ];
                    $response_code = 404;
                    return response()->json($response, $response_code);
            }
            $user->password = bcrypt($validated['new_password']);
            $user->save();
            return $this->signout("password changed successfuly, please sign in again");
        }
        else{
            $response = [
            'msg' => "users didn't match",
            ];
            $response_code = 404;
            return response()->json($response, $response_code);
        }
    }


    public function upload_avatar(Request $request ,$user_name){
        if(!$this->match_request_with_user($user_name)){
            return response()->json(["msg" => "invalid user name"], 404);
        }

        if(!$request->hasFile('avatar')) {
            return response()->json(["msg" => "no file"], 400);
        }
        if(!$request->file('avatar')->isValid()) {
            return response()->json(['invalid_file_upload'], 400);
        }
        $request->validate(['avatar' => 'mimes:jpeg,png|max:1024']);
        
        $path = public_path() . self::$avatars_path;
        $file_name = "user_id_" . $this->me()->original->id . "." .$request->file('avatar')->extension();
        $request->file('avatar')->move($path, $file_name);
        return response()->json(["url" =>  asset(self::$avatars_path.$file_name)], 200);
        // return response()->json(["url" =>  public_path()], 200);
    }

    public function delete_avatar(Request $request, $user_name){

        if(!$this->match_request_with_user($user_name)){
            return response()->json(["msg" => "invalid user name"], 404);
        } else {
            $user = json_decode($this->me()->original, true);
            $avatar = "public/uploads/avatars/user_id_" . $this->me()->original->id;
            $response = [
                'msg' => "operation successful", 
                ];
            $response_code = 200;
            if (Storage::exists($avatar.".jpeg")){
                Storage::delete($avatar .".jpeg");
            }
            elseif (file_exists($avatar.".png")){
                Storage::delete($avatar .".png");
            } else {
                $response = [
                    'msg' => "operation failed", 
                    ];
                $response_code = 400;
            }
        }
        return response()->json([$response], $response_code);

    }
    

    public function ranking(){
        $users = User::orderBy('xp', 'Desc')->take(100)->get();
        $rank = 1;
        foreach ($users as $user) {
            $avatar = public_path() . self::$avatars_path . "user_id_" .$user->id;
            if (file_exists($avatar.".jpeg")){
                $user["avatar"] = "1";
                $user['avatar'] = asset(self::$avatars_path. "user_id_".$user->id.".jpeg");
            } elseif (file_exists($avatar.".png")){
                $user['avatar'] = asset(self::$avatars_path. "user_id_".$user->id.".png");
            } else {
                $user['avatar'] = null;
            }
            $user['rank'] = $rank;
            
            $rank++;
        }
        return response()->json([$users], 200);
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
