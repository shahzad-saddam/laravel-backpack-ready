<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Controller constructor.
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $user = Auth::user();

        // If the app sends a locale use this
        if ($locale = $request->header('X-Locale')) {
            // If the selected app locale doesn't match the user locale update it
            if ($user && $user->locale != $locale) {
                $user->update(['locale' => $locale]);
            }

            App::setLocale($locale);
        } else if ($user && $user->locale) {
            // If the logged in user has a preferred locale use this
            App::setLocale($user->locale);
        }
    }
}