<?php

namespace App\Helpers;

use Conversio\Adapter\LanguageCode;
use Conversio\Adapter\Options\LanguageCodeOptions;
use Conversio\Conversion;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Countries\Facade as Country;
use PragmaRX\Countries\Support\CountriesRepository;

class CountryHelper
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getCountries()
    {
        /** @var CountriesRepository $countryRepository */
        $countryRepository = Country::getRepository();

        $countries = $countryRepository->all();

        $locale = App::getLocale();
        if (($pos = strpos($locale, '_')) !== false) {
            $locale = substr($locale, 0, $pos - 1);
        }

        if (($pos = strpos($locale, '-')) !== false) {
            $locale = substr($locale, 0, $pos - 1);
        }

        $locale = Cache::rememberForever("iso639_3.{$locale}", function () use ($locale) {
            $options = new LanguageCodeOptions();
            $options->setOutput('iso639-3');

            $localeConverter = new Conversion([
                'adapter' => new LanguageCode(),
                'options' => $options
            ]);

            $convertedLocale = $localeConverter->filter($locale);

            return $convertedLocale;
        });

        /** @var \PragmaRX\Countries\Support\Collection $countries */
        $countries = Cache::rememberForever("countries.{$locale}", function () use ($countryRepository, $countries, $locale) {
            $collator = new \Collator('en_US');

            return $countries
                ->filter(function ($country) {
                    return
                        is_object($country) &&
                        property_exists($country, 'name') &&
                        property_exists($country->name, 'common');
                })
                ->map(function ($country) use ($countryRepository, $locale) {
                    if (
                        $locale &&
                        property_exists($country, 'translations') &&
                        property_exists($country->translations, $locale) &&
                        property_exists($country->translations->{$locale}, 'common')
                    ) {
                        $name = $country->name->native->{$locale}->common;
                    } else {
                        $name = $country->name->common;
                    }

                    $flag = null;

                    return [
                        'id' => $country->cca2,
                        'name' => $name,
                        'flag' => $countryRepository->getFlagSvg($country->cca3),
                    ];
                })
                ->toBase()
                ->sort(function ($lhs, $rhs) use ($collator) {
                    return $collator->compare($lhs['name'], $rhs['name']);
                })
                ->values();
        });

        return $countries;
    }

    /**
     * @param string $code
     * @return array|null
     */
    public static function getCountry($code)
    {
        return static::getCountries()->where('id', $code)->first();
    }
}