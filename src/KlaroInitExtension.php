<?php

class KlaroInitExtension extends Extension
{
    public function contentControllerInit($controller)
    {
        Requirements::insertHeadTags('<script type="application/javascript" src="/_klaro-config/"></script>');
        Requirements::javascript('klaro-cookie-consent/dist/klaro.js');
    }
}
