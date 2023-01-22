<?php

namespace Kraftausdruck\Controller;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\SiteConfig\SiteConfig;


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
            return $this->owner->customise(['SiteConfig' => $siteConfig])->renderWith('Kraftausdruck/Controller/KlaroConfigController');
        } else {
            return $this->httpError(404);
        }
    }
}
