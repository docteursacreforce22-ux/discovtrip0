<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class CancellationController extends Controller
{
    public function show()
    {
        return view('pages.legal.cancellation');
    }
}