<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendingEmail;

class EmailController extends Controller
{
    static function send($mailto, $name, $message)
    {

        $data = array(
            'name' =>$name,
            'message' => $message
        );

        Mail::to($mailto)->send(new sendingEmail($data));
        return true;
        // return back()->with('success', 'Thanks for contacting us!');
    }
}