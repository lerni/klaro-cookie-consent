<?php

namespace  Kraftausdruck\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Core\Manifest\ModuleResourceLoader;

class KlaroInitExtension extends Extension
{
    public function onBeforeInit()
    {
        $siteConfig = SiteConfig::current_site_config();
        if ($siteConfig->CookieIsActive) {
            Requirements::javascript('/_klaro-config/');
            Requirements::javascript(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:dist/klaro.js'));
        }
    }
}
