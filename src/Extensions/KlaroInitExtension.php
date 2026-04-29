<?php

namespace Kraftausdruck\Extensions;

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

        if ($siteConfig->CookieIsActive && $this->owner->response) {
            $hash = self::getCacheHash($siteConfig);
            Requirements::css(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:client/node_modules/klaro/dist/klaro.min.css'));
            Requirements::javascript('/_klaro-config?m=' . $hash);
            Requirements::javascript(ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:client/node_modules/klaro/dist/klaro-no-css.js'));
        }
    }

    public function updateMetaComponents(array &$tags)
    {
        $siteConfig = SiteConfig::current_site_config();
        $preconnect = Config::inst()->get(self::class, 'preconnect');

        if (!$siteConfig->CookieIsActive || $preconnect !== 'true') {
            return;
        }

        $hash = self::getCacheHash($siteConfig);

        $tags['klaroPreloadConfig'] = [
            'tag' => 'link',
            'attributes' => [
                'rel' => 'preload',
                'as' => 'script',
                'href' => '/_klaro-config?m=' . $hash,
            ],
        ];
        $tags['klaroPreloadCss'] = [
            'tag' => 'link',
            'attributes' => [
                'rel' => 'preload',
                'as' => 'style',
                'href' => ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:client/node_modules/klaro/dist/klaro.min.css'),
            ],
        ];
        $tags['klaroPreloadJs'] = [
            'tag' => 'link',
            'attributes' => [
                'rel' => 'preload',
                'as' => 'script',
                'href' => ModuleResourceLoader::resourceURL('lerni/klaro-cookie-consent:client/node_modules/klaro/dist/klaro-no-css.js'),
            ],
        ];
    }

    private static function getCacheHash(SiteConfig $siteConfig): string
    {
        // cachebooster similar to template caching
        $hashComponents = [
            $siteConfig->LastEdited,
            CookieCategory::get()->max('LastEdited'),
            CookieCategory::get()->count(),
            CookieEntry::get()->max('LastEdited'),
            CookieEntry::get()->count(),
        ];

        return substr(md5(implode('|', $hashComponents)), 0, 12);
    }
}
