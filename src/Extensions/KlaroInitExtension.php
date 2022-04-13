<?php

namespace  Kraftausdruck\Extensions;

use SilverStripe\i18n\i18n;
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

        if ($siteConfig->CookieIsActive && $preconnect === 'true' && $this->owner->response) {
            $additionalLinkHeaders = [
                '</_klaro-config/>; rel=preload; as=script',
                sprintf(
                    '<%s>; rel=preload; as=style',
                    ModuleResourceLoader::resourceURL('/_resources/vendor/lerni/klaro-cookie-consent/node_modules/klaro/dist/klaro.min.css')
                ),
                sprintf(
                    '<%s>; rel=preload; as=script',
                    ModuleResourceLoader::resourceURL('/_resources/vendor/lerni/klaro-cookie-consent/node_modules/klaro/dist/klaro-no-css.js')
                )
            ];
            $headers = $this->owner->response->getHeaders();
            if (array_key_exists('link', $headers)) {
                $linkHeaders = explode(',', $headers['link']);
                $linkHeaders = array_merge($linkHeaders, $additionalLinkHeaders);
            } else {
                $linkHeaders = $additionalLinkHeaders;
            }
            $this->owner->response->addHeader('link', implode(',', $linkHeaders));
        }
        $hash = substr(md5($siteConfig->LastEdited . i18n::get_locale()), 0, 12);
        Requirements::css(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:node_modules/klaro/dist/klaro.min.css'));
        Requirements::javascript('/_klaro-config/?m=' . $hash);
        Requirements::javascript(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:node_modules/klaro/dist/klaro-no-css.js'));
    }
}
