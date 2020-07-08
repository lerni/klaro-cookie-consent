<?php

use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Parsers\ShortcodeParser;

ShortcodeParser::get('default')->register('ConsentLink', function($arguments, $parser, $shortcode) {

    $siteConfig = SiteConfig::current_site_config();

    $Linkstring = '';

    $beforeText = $arguments['beforeText'];
    $afterText = $arguments['afterText'];

    if ($siteConfig->CookieIsActive)
    {
        $Linkstring = sprintf(
            '%s<a onClick="klaro.show();return false;">%s</a>%s',
            $beforeText,
            _t('Kraftausdruck\KlaroCookie.MODALLINK','none'),
            $afterText
        );
    }
    return $Linkstring;
});
