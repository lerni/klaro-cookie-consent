<?php

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
            '%s<a onClick="klaro.show();return false;">%s</a>%s',
            $beforeText,
            _t('KlaroCookie.MODALLINK','none'),
            $afterText
        );
    }
    return $Linkstring;
});
