# Silverstripe Klaro! Consent Manager
Silverstripe Klaro [klɛro] implements [KIProtect/klaro](https://github.com/KIProtect/klaro). A consent manager that helps to be transparent about third-party applications and be compliant with GDPR and ePrivacy. This module is inspired by [nomidi/kw-cookie-consent](https://github.com/nomidi/kw-cookie-consent).


## Requirements
- silverstripe/cms ^4 | ^5
- silverstripe/siteconfig ^4 | ^5
- symbiote/silverstripe-gridfieldextensions ^3 | ^4
### Compatibility Version
- There is a [3.x](https://github.com/lerni/klaro-cookie-consent/tree/3.x) branch with a backport for Silverstripe 3.
- For Silverstripe 4.x & 5.x [v2](https://github.com/lerni/klaro-cookie-consent/tree/v2) is recommended ATM
### Suggested
- lerni/erni/silverstripe-googleanalytics


## Installation
[Composer](https://getcomposer.org/) is the recommended way installing Silverstripe modules.

`composer require lerni/klaro-cookie-consent:v2.x-dev`
or
`composer require lerni/klaro-cookie-consent:3.x-dev`

Run `dev/build`

## Getting started
The module loads [klaro.js](https://klaro.kiprotect.com/klaro.js) per `KlaroInitExtension` wich is applied to ContentController. The config is served with `KlaroConfigController` and available per `/_klaro-config`. You can link consent settings like `<a href="#klaro" onClick="klaro.show();return false;">Cookie consent</a>` or use a ShortCode in CMS. ShortCode `[ConsentLink]` takes parameter `beforeText` & `afterText` and is shown conditionally of `SiteConfig->CookieIsActive`.


## Managing third-party apps/trackers
To manage third-party scripts and ensure they only run if the user consents with their use, simply replace the `src` attribute with `data-src`, change the `type` attribute to `text/plain` and add a `data-type` attribute with the original type and add a `data-name` field that matches the name of the app as given in config. Example:
```html
<script type="text/plain"
    data-type="text/javascript"
    data-name="optimizely"
    data-src="https://cdn.optimizely.com/js/10196010078.js">
</script>
```
Klaro will then take care of executing the scripts if consent was given (you can chose to execute them before getting explicit consent with `OptOut`).

The same method also works for images, stylesheets and other elements with a `src` or `type` attribute.

# Styling
Example SCSS customisation
```scss
// !klaro
html .klaro {
    .cookie-modal,
    .cookie-notice {
        a {
            color: $link-color;
        }
        .cm-btn {
            cursor: pointer;
            font-size: 14px;
            border-radius: 0;
        }
    }
    .cookie-notice {
        .cn-ok {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start !important;
            .cn-buttons {
                order: 1;
                // decline
                .cm-btn.cn-decline {
                    background-color: $gray;
                    order: 1;
                }
                // accept all
                .cm-btn.cm-btn-success {
                    background-color: $link-color;
                    order: 0;
                }
            }
            // modal link
            .cn-learn-more {
                display: block;
                margin-top: .6em;
                order: 2;
                flex: 0 0 100%;
            }
        }
    }

    .cookie-modal {
        .cm-header a {
            @include bold;
        }
        .cm-app-title {
            font-size: 14px;
        }
        // switch disabled
        .cm-list-label .slider {
            background-color: $gray--light;
        }
        // slider-switches
        .cm-list-input:checked + .cm-list-label .slider {
            background-color: $link-color;
        }
        // subitems
        .cm-list-description {
            color: $white;
        }
        // required switch enabled
        .cm-list-input.required:checked + .cm-list-label .slider {
            background-color: darken($link-color, 10%);
            &::before {
                background-color: darken($white, 16%);
            }
        }
        // halve is used on parent if children are on & off
        .cm-list-input.half-checked:checked + .cm-list-label .slider {
            background-color: mix($link-color, $white, 71%);
        }

        // accept all
        .cm-btn.cm-btn-accept-all {
            background-color: $link-color;
        }
        // save selection, decline
        .cm-btn.cm-btn-accept,
        .cm-btn.cm-btn-decline {
            background-color: $gray;
        }
        // klaro link
        .cm-modal .cm-footer .cm-powered-by {
        }
    }
}
```

# Todo
- multilingual defaults from klaro, add translations if configured<br/>ATM cache-block in `KlaroConfigController.ss` causes malfunctioning with fluent
- add template-parser to add data-attributes and ditch suggested modules from composer
- add defaults for google fonts, YouTube, gMaps etc.
