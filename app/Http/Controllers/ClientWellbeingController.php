<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientWellbeingController extends Controller
{
    /** Client panel (authenticated). */
    public function index(Request $request)
    {
        return view('client.wellbeing.index');
    }

    /** Public website (no auth). */
    public function publicIndex(Request $request)
    {
        return view('wellbeing.public');
    }
}
