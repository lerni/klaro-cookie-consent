<?php

namespace  Kraftausdruck\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Config\Config;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Core\Manifest\ModuleResourceLoader;

class KlaroInitExtension extends Extension
{
    public function onBeforeInit()
    {
        $siteConfig = SiteConfig::current_site_config();
        $preconnect = Config::inst()->get('Kraftausdruck\Extensions\KlaroInitExtension', 'preconnect');
        if ($siteConfig->CookieIsActive) {
            if ( $preconnect === 'true') {
                if ($this->owner->response) {
                    $this->owner->response->addHeader('Link', implode(',', [
                        sprintf(
                            '<%s>; rel=preload; as=style',
                            ModuleResourceLoader::resourceURL('/_resources/vendor/lerni/klaro-cookie-consent/node_modules/klaro/dist/klaro.min.css')
                        ),
                        // todo: it doesn't resolve
                        // sprintf(
                        //     '<%s>; rel=preload; as=script',
                        //     ModuleResourceLoader::resourceURL('/_klaro-config/')
                        // ),
                        sprintf(
                            '<%s>; rel=preload; as=script',
                            ModuleResourceLoader::resourceURL('/_resources/vendor/lerni/klaro-cookie-consent/node_modules/klaro/dist/klaro-no-css.js')
                        )
                    ]));
                }
            }
            Requirements::css(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:node_modules/klaro/dist/klaro.min.css'));
            Requirements::javascript('/_klaro-config/');
            Requirements::javascript(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:node_modules/klaro/dist/klaro-no-css.js'));
        }
    }
}
