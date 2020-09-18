<?php

class KlaroInitExtension extends Extension
{
    public function contentControllerInit($controller)
    {
        $siteConfig = SiteConfig::current_site_config();
        $hash = substr(md5($siteConfig->LastEdited . i18n::get_locale()), 0, 12);
        if ($siteConfig->CookieIsActive) {
            Requirements::insertHeadTags('<script type="application/javascript" src="/_klaro-config/?v='.$hash.'"></script>');
            Requirements::javascript('klaro-cookie-consent/dist/klaro.js');
        }
    }
}
