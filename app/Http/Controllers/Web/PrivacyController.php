<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class PrivacyController extends Controller
{
    public function show()
    {
        return view('pages.legal.privacy');
    }
}