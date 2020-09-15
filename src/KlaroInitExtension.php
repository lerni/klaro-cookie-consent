<?php

class KlaroInitExtension extends Extension
{
    public function contentControllerInit($controller)
    {
        $siteConfig = SiteConfig::current_site_config();
        if ($siteConfig->CookieIsActive) {
            Requirements::insertHeadTags('<script type="application/javascript" src="/_klaro-config/"></script>');
            Requirements::javascript('klaro-cookie-consent/dist/klaro.js');
        }
    }
}
