<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Notifications\TaskReminder;

class EmailController extends Controller
{
    public function store(Request $request, $user_name){
        //$order = User::findOrFail($request->order_id);

        // Ship the order...
        $user = User::where('user_name', $user_name);
        //$user->name = 'homa';
        $user->email = 'homasemsarha@yahoo.com';
        //Mail::to($user)->send(new TaskReminder());
        Notification::route('mail' , $user->email) //Sending mail to subscriber
                          ->notify(new TaskReminder()); //With new post
 
        return redirect()->back();
    }
}
