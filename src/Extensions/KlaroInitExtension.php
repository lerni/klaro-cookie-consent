<?php

namespace  Kraftausdruck\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Config\Config;
use Kraftausdruck\Models\CookieEntry;
use SilverStripe\SiteConfig\SiteConfig;
use Kraftausdruck\Models\CookieCategory;
use SilverStripe\Core\Manifest\ModuleResourceLoader;

class KlaroInitExtension extends Extension
{
    public function onBeforeInit()
    {
        $siteConfig = SiteConfig::current_site_config();
        $preconnect = Config::inst()->get('Kraftausdruck\Extensions\KlaroInitExtension', 'preconnect');

        if ($siteConfig->CookieIsActive && $this->owner->response) {
            // cachebooster similar to template caching
            $hashComponents = [
                $siteConfig->LastEdited,
                CookieCategory::get()->max('LastEdited'),
                CookieCategory::get()->count(),
                CookieEntry::get()->max('LastEdited'),
                CookieEntry::get()->count()
            ];

            $hash = substr(md5(implode('|', $hashComponents)), 0, 12);
            if ($preconnect === 'true') {
                $additionalLinkHeaders = [
                    '</_klaro-config/?m=' . $hash . '>; rel=preload; as=script',
                    sprintf(
                        '<%s>; rel=preload; as=style',
                        ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:client/node_modules/klaro/dist/klaro.min.css')
                    ),
                    sprintf(
                        '<%s>; rel=preload; as=script',
                        ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:client/node_modules/klaro/dist/klaro-no-css.js')
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
            Requirements::css(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:client/node_modules/klaro/dist/klaro.min.css'));
            Requirements::javascript('/_klaro-config/?m=' . $hash);
            Requirements::javascript(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:client/node_modules/klaro/dist/klaro-no-css.js'));
        }
    }
}
