<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function __construct(Request $request)
    {
        Auth::shouldUse('api');

        parent::__construct($request);
    }
}