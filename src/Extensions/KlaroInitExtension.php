<?php

namespace  Kraftausdruck\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Manifest\ModuleResourceLoader;

class KlaroInitExtension extends Extension
{
    public function onBeforeInit()
    {
        Requirements::javascript('/_klaro-config/');
        Requirements::javascript(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:dist/klaro.js'));
    }
}
