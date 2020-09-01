<?php

class KlaroConfigController extends Controller
{

    public function index(SS_HTTPRequest $request)
    {
        $siteConfig = SiteConfig::current_site_config();

        $this->getResponse()->addHeader("Content-Type", "text/javascript; charset=utf-8");

        if ($siteConfig->CookieIsActive) {
            return $this->owner->customise(['SiteConfig' => $siteConfig])->renderWith('KlaroConfigController');
        } else {
            return $this->httpError(404);
        }
    }
}
