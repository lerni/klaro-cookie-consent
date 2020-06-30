# Silverstripe Klaro! Consent Manager
Silverstripe Klaro [klɛro] implements [KIProtect/klaro](https://github.com/KIProtect/klaro). A consent manager that helps to be transparent about third-party applications and be compliant with GDPR and ePrivacy. This module is inspred by [nomidi/kw-cookie-consent](https://github.com/nomidi/kw-cookie-consent).


## Requirements
- silverstripe/cms ^4
- silverstripe/siteconfig ^4
### Suggested
- lerni/erni/silverstripe-googleanalytics


## Installation
[Composer](https://getcomposer.org/) is the recommended way installing Silverstripe modules.

`composer require lerni/klaro-cookie-consent`

Run `dev/build`


## Getting started
The module loads [klaro.js](https://klaro.kiprotect.com/klaro.js) per `KlaroInitExtension` wich is applied to ContentController. The config is controlled per per `KlaroSiteConfigExtension` and available per `/_klaro-config`. You can link consent settings link `<a onClick="klaro.show();return false;">Cookie consent</a>`


## Managing third-party apps/trackers
To manage third-party scripts and ensure they only run if the user consents with their use, you simply replace the `src` attribute with `data-src`, change the `type` attribute to `text/plain` and add a `data-type` attribute with the original type, and add a `data-name` field that matches the name of the app as given in your config file. Example:
```html
<script type="text/plain"
    data-type="text/javascript"
    data-name="optimizely"
    data-src="https://cdn.optimizely.com/js/10196010078.js">
</script>
```
Klaro will then take care of executing the scripts if consent was given (you can chose to execute them before getting explicit consent as well).

The same method also works for images, stylesheets and other elements with a `src` or `type` attribute.

# Todo
- multilingual defaults from klaro and add translations if configured
- prepare defaults for google fonts, YouTube, gMaps
- respect defaults for SiteConfig
- multiple coockies with regex