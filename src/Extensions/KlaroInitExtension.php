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
            Requirements::css(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:node_modules/klaro/dist/klaro.min.css'));
            Requirements::javascript('/_klaro-config/');
            Requirements::javascript(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:node_modules/klaro/dist/klaro-no-css.js'));
        }
    }
}
