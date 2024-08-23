<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignUp;

class MailController extends Controller
{
    public function sendEmail()
    {
        // Validate the request data
        // $request->validate([
        //     'email' => 'rami@gmail.com',
        //     'name' => 'rami'
        // ]);

        // $details = [
        //     'name' => $request->input('name')
        // ];

        // Send the email
        Mail::to('ems123project@gmail')->send(new SignUp());

        return response()->json(['message' => 'Email sent successfully'], 200);
    }
}
