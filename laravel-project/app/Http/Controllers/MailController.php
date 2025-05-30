<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        Mail::raw($data['message'], function ($message) use ($data) {
            $message->to($data['email'])
                    ->subject('University Consortium notice');
        });
        
        return response()->json(['message' => 'The email has been successfully sent!']);
    }
}
