<?php

use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Parsers\ShortcodeParser;

ShortcodeParser::get('default')->register('ConsentLink', function($arguments, $parser, $shortcode) {

    $siteConfig = SiteConfig::current_site_config();

    $Linkstring = '';


    if (empty($arguments['beforeText'])) {
        $beforeText = '';
    } else {
        $beforeText = $arguments['beforeText'];
    }

    if (empty($arguments['afterText'])) {
        $afterText = '';
    } else {
        $afterText = $arguments['afterText'];
    }

    if ($siteConfig->CookieIsActive)
    {
        $Linkstring = sprintf(
            '%s<a  href="#klaro" onClick="klaro.show();return false;">%s</a>%s',
            $beforeText,
            _t('Kraftausdruck\KlaroCookie.MODALLINK','none'),
            $afterText
        );
    }
    return $Linkstring;
});
