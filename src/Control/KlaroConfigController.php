<?php

namespace Kraftausdruck\Controller;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Director;
use SilverStripe\SiteConfig\SiteConfig;


class KlaroConfigController extends Controller
{

    public function index(HTTPRequest $request)
    {
        $siteConfig = SiteConfig::current_site_config();

        $this->getResponse()->addHeader("Content-Type", "text/javascript; charset=utf-8");


        if ($siteConfig->CookieIsActive) {
            return $this->owner->customise(['SiteConfig' => $siteConfig])->renderWith('Kraftausdruck/Controller/KlaroConfigController');
        } else {
            return $this->httpError(404);
        }
    }
}
