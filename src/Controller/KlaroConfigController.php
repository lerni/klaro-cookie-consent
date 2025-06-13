<?php

namespace Kraftausdruck\Controller;

use SilverStripe\i18n\i18n;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use TractorCow\Fluent\Model\Locale;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\SiteConfig\SiteConfig;
use TractorCow\Fluent\State\FluentState;
use Kraftausdruck\Models\CookieEntry;
use Kraftausdruck\Models\CookieCategory;

class KlaroConfigController extends Controller
{

    public function index(HTTPRequest $request)
    {
        $siteConfig = SiteConfig::current_site_config();

        // response header
        $header = $this->getResponse();
        $header->addHeader('Content-Type', 'text/javascript; charset=utf-8');
        $header->addHeader('X-Robots-Tag', 'noindex');

        if ($siteConfig->CookieIsActive) {
            return $this->owner->customise($this->getLocalisedSiteConfigs())
                ->renderWith('Kraftausdruck/Controller/KlaroConfigController');
        } else {
            return $this->httpError(404);
        }
    }

    protected function getLocalisedSiteConfigs()
    {
        $localeData = ArrayList::create();
        $globalServices = ArrayList::create();

        if (class_exists('TractorCow\Fluent\Extension\FluentExtension')) {
            $locales = Locale::get();
            $originalLocale = FluentState::singleton()->getLocale();

            // First pass: collect all unique services and build translation structure
            $serviceTranslations = [];

            foreach ($locales as $locale) {
                // Switch to this locale temporarily
                FluentState::singleton()->setLocale($locale->Locale);
                $lang = i18n::getData()->langFromLocale($locale->Locale);

                // Get SiteConfig for this locale
                $siteConfig = SiteConfig::current_site_config();
                $siteConfig->KLang = $lang;

                // Fetch localized cookie data while the locale is set
                $siteConfig->LocalizedCookieEntries = CookieEntry::get();
                $siteConfig->LocalizedCookieCategories = CookieCategory::get();

                $localeData->push($siteConfig);

                // Build service translations for this locale
                foreach ($siteConfig->LocalizedCookieEntries as $cookieEntry) {
                    $serviceKey = $cookieEntry->CookieKey;

                    if (!isset($serviceTranslations[$serviceKey])) {
                        $serviceTranslations[$serviceKey] = [
                            'service' => $cookieEntry, // Store the base service object
                            'translations' => []
                        ];
                    }

                    // Add translation for this locale
                    $serviceTranslations[$serviceKey]['translations'][$lang] = [
                        'title' => $cookieEntry->Title,
                        'description' => $cookieEntry->Purpose
                    ];
                }
            }

            // Second pass: create global services with nested translations
            foreach ($serviceTranslations as $serviceKey => $serviceData) {
                $service = $serviceData['service'];
                $service->ServiceTranslations = ArrayList::create();

                // Add translation objects for template
                foreach ($serviceData['translations'] as $lang => $translation) {
                    $translationObj = ArrayData::create([
                        'KLang' => $lang,
                        'Title' => $translation['title'],
                        'Description' => $translation['description']
                    ]);
                    $service->ServiceTranslations->push($translationObj);
                }

                $globalServices->push($service);
            }

            // Restore original locale
            FluentState::singleton()->setLocale($originalLocale);
        } else {
            // Single locale setup
            $locale = i18n::config()->get('default_locale');
            $lang = i18n::getData()->langFromLocale($locale);
            $siteConfig = SiteConfig::current_site_config();
            $siteConfig->KLang = $lang;

            // Fetch cookie data for single locale setup
            $siteConfig->LocalizedCookieEntries = CookieEntry::get();
            $siteConfig->LocalizedCookieCategories = CookieCategory::get();

            $localeData->push($siteConfig);

            // Add services with single language
            foreach ($siteConfig->LocalizedCookieEntries as $cookieEntry) {
                $cookieEntry->ServiceTranslations = ArrayList::create([
                    ArrayData::create([
                        'KLang' => $lang,
                        'Title' => $cookieEntry->Title,
                        'Description' => $cookieEntry->Purpose
                    ])
                ]);
                $globalServices->push($cookieEntry);
            }
        }

        // Return structure that template expects
        return [
            'LocalisedSiteConfigs' => $localeData,
            'GlobalServices' => $globalServices,
            'SiteConfig' => $localeData->first() // For backwards compatibility if needed
        ];
    }
}
