<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CountryHelper;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class CountryController extends ApiController
{
    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $countries = CountryHelper::getCountries();

        if ($search = $request->input('search')) {
            $search = strtolower($search);

            $countries = $countries->filter(function ($country) use ($search) {
                return strpos(strtolower($country['name']), $search) !== false;
            });
        }

        return $countries;
    }

    /**
     * @param string $code
     * @return array
     */
    public function show($code)
    {
        if (!($country = CountryHelper::getCountry($code))) {
            abort(404);
        }

        return $country;
    }
}