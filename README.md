# Silverstripe Klaro! Consent Manager
Silverstripe Klaro! implements [KIProtect/klaro](https://github.com/KIProtect/klaro) for GDPR-compliant cookie consent management with Google Consent Mode v2 support.

## Quick Start
1. `composer require lerni/klaro-cookie-consent`
2. `dev/build`
3. `dev/tasks/klaro-defaults` (default values for SiteConfig)
4. Go to **/admin/settings#Root_CookieConsent** and enable "Cookie Is Active" & configureyour needs

## Requirements
- SilverStripe CMS ^5 or ^6
- PHP ^8.1

### Suggested Modules
- `lerni/silverstripe-tracking` - for Google Analytics, GTM, and Clarity integration

## Installation
```bash
# For SilverStripe 5.x/6.x (current)
composer require lerni/klaro-cookie-consent

# Legacy versions
composer require lerni/klaro-cookie-consent:dev-v2  # SS 4.x/5.x
composer require lerni/klaro-cookie-consent:dev-3.x # SS 3.x
```

CookieEntries & CookieCategories are automatically populated. To add values to SiteConfig use the task below, it populates SiteConfig with default translations from Klaro and applies custom translations from your language file.
```bash
php ./vendor/silverstripe/framework/cli-script.php dev/tasks/klaro-defaults
```

## Basic Usage

### CMS Configuration
1. **Settings > Cookie Consent**
2. Enable "Cookie Is Active"
3. Configure services (Google Analytics, GTM, etc.)
4. Customize consent modal text and appearance

### Adding Consent Links
```html
<!-- Manual link -->
<a href="#klaro" onClick="klaro.show();return false;">Cookie Settings</a>

<!-- Or use ShortCode in CMS -->
[ConsentLink beforeText="Manage your " afterText=" preferences"]
```

### Managing Third-Party Scripts
Replace `src` with `data-src` and add consent attributes:
```html
<!-- Before: Regular script -->
<script src="https://example.com/tracking.js"></script>

<!-- After: Consent-managed script -->
<script type="text/plain"
    data-type="text/javascript"
    data-name="analytics"
    data-src="https://example.com/tracking.js">
</script>
```

## Consent Mode v2 Support
Support for Google's privacy-compliant tracking with consent updates.

### Default Services Included
- **Google Tag Manager**
- **Google Analytics**
- **Google Ads**
- **Microsoft Clarity**

## Advanced Configuration

### Custom Consent Callbacks
Configure custom JavaScript for each service in **Settings > Cookie Consent**:

```javascript
// Google Analytics example
OnAccept: if(typeof gtag === "function") { gtag("consent", "update", { analytics_storage: "granted" }); }
OnDecline: if(typeof gtag === "function") { gtag("consent", "update", { analytics_storage: "denied" }); }

// Microsoft Clarity example  
OnAccept: if(typeof clarity === "function") { clarity("consent"); }
OnDecline: if(typeof clarity === "function") { clarity("consent", false); }
```

### Google Tag Manager Integration
When using with `lerni/silverstripe-tracking`, GTM events are automatically fired based on your callback:

**Default Events:**
- `klaro-google-analytics-accepted/declined`
- `klaro-google-ads-accepted/declined`
- `klaro-google-tag-manager-accepted/declined`

**Setting up GTM Triggers:**
1. Create **Custom Event** trigger in GTM
2. Use event name (e.g., `klaro-google-analytics-accepted`)
3. Fire your tracking tags based on consent

### Configuration Override
Override defaults in your `app/_config/klaro.yml`:
```yaml
Kraftausdruck\Models\CookieEntry:
  default_records:
    Analytics:
      Title: 'Custom Analytics Title'
      # Override any default settings
```

<details>
<summary>Styling Customization</summary>

```scss
// Example SCSS customization
html .klaro {
	--notice-max-width: 440px;
	.cookie-modal,
	.cookie-notice {
		z-index: 9100;
		a {
			color: lighten($link-color, 70%);
		}
		.cm-btn {
			border-radius: 2px;
			margin: 5px;
			color: white;
			background-color: $button-color;
			border: 1px solid transparent;
			outline: none;
			text-decoration: none;
			cursor: pointer;
			line-height: 1;
			font-weight: 400;
			transition: background-color 0.3s ease, color 0.3s ease;
		}
		.cm-btn:not(.cm-btn-manager):hover {
			background-color: darken($button-color, 10%);
		}
		.cm-btn.cm-btn-manager {
			background-color: transparent;
			color: lighten($text-color, 70%);
			border: 1px solid $button-color;
		}
		.cm-btn.cm-btn-manager:hover {
			background-color: rgba($button-color, 0.1);
		}
		.cm-btn.cm-btn-close {
			color: $button-color;
			font-size: 2em;
			line-height: 1;
			padding: 0 5px;
			border: 0;
			background-color: transparent;
		}
		.cm-btn.cm-btn-close:hover {
			color: darken($button-color, 30%);
		}
		.cookie-notice {
			.cm-buttons {
				display: flex;
				justify-content: space-between;
				flex-direction: row;
				margin-bottom: 0;
				.cm-btn {
					margin: 5px;
					flex: 1 1 auto;
					padding: 10px;
					max-width: 200px;
					&.cm-btn-manager {
						padding: 9px;
						flex-shrink: 2;
						font-size: 0.9em;
					}
				}
			}
		}
		.cookie-modal {
			.cm-modal {
				margin: 2rem auto;
				.cm-header {
					// border: 0;
					.title {
						margin-top: 0;
					}
				}
				.cm-body {
					.cm-app {
						border: 0;
						margin-bottom: 1rem;
						.cm-app-title {
							font-weight: 700;
						}
						.cm-app-required {
							color: orange;
						}
						.cm-app-title,
						p.cm-app-description {
							margin: 0 0 0 20px;
						}
						.cm-app-input {
							margin: 0 5px 0 0;
						}
					}
				}
				.cm-footer {
					border: 0;
					padding: 1rem 0 0 0;
					.cm-buttons {
						margin: 0;
						.cm-btn {
							padding: 0.5rem 1rem;
							font-size: 1rem;
						}
					}
				}
			}
		}
	}
}
```

</details>

## Resources
- [Klaro! Documentation](https://klaro.kiprotect.com/docs)
- [Google Consent Mode v2 Guide](https://developers.google.com/tag-platform/security/guides/consent)
- [SilverStripe Configuration Documentation](https://docs.silverstripe.org/en/developer_guides/configuration/)


## Installation
[Composer](https://getcomposer.org/) is the recommended way installing Silverstripe modules.

```bash
composer require lerni/klaro-cookie-consent:dev-v2
composer require lerni/klaro-cookie-consent:dev-3.x
composer require lerni/klaro-cookie-consent:dev-5.x
composer require lerni/klaro-cookie-consent:dev-6.x
```

Run `dev/build`

### KlaroDefaults Task
Populates SiteConfig with default translations from Klaro and applies custom translations from your language file.
```bash
php ./vendor/bin/sake tasks:gen-lang-files
```
`_config/klaro_defaults.yml` contains default-records for `CookieCategory` & `CookieEntry` in german. Those can can be [nulled](https://docs.silverstripe.org/en/6/developer_guides/configuration/configuration/#configuration-values) and overriden.

## Getting started
The module loads [klaro.js](https://klaro.kiprotect.com/klaro.js) per `KlaroInitExtension` which is applied to ContentController. The config is served with `KlaroConfigController` and available per `/_klaro-config`. Consent settings can be linked using `<a href="#klaro" onClick="klaro.show();return false;">Cookie consent</a>` or by using a ShortCode in CMS. ShortCode `[ConsentLink]` takes parameter `beforeText` & `afterText` and is shown conditionally of `SiteConfig->CookieIsActive`.

## Managing third-party apps/trackers
To manage third-party scripts and ensure they only run if the user consents with their use, simply replace the `src` attribute with `data-src`, change the `type` attribute to `text/plain` and add a `data-type` attribute with the original type and add a `data-name` field that matches the name of the app as given in config. Example:
```html
<script type="text/plain"
    data-type="text/javascript"
    data-name="optimizely"
    data-src="https://cdn.optimizely.com/js/10196010078.js">
</script>
```
Klaro will then manage script execution based on each service's consent settings and whether consent is required or if opt-out is the default behavior.

The same method also works for iframes, images, stylesheets and other elements with a `src` or `type` attribute.

## Advanced Configuration

### Custom Consent Callbacks
Custom JavaScript can be added that runs when users accept or decline services. This is where service-specific logic and Consent Mode updates are handled.

```javascript
// Example: Analytics with Consent Mode v2
onAccept: `
    if(typeof gtag === "function") { 
        gtag("consent", "update", { analytics_storage: "granted" }); 
    }
    console.log("Google Analytics accepted");
`

// Example: Microsoft Clarity (no gtag needed)
onAccept: `
    if (typeof clarity !== 'undefined') {
        clarity('consent');
    }
    console.log("Microsoft Clarity accepted");
`

// Example: Custom tracking service
onAccept: `
    if(typeof customTracker !== 'undefined') {
        customTracker.enable();
    }
`
```
Each service can have its own specific callback logic. Google services typically use `gtag('consent', 'update', ...)` while other services may have their own APIs.

### Integration with Google Tag Manager
When using Google Tag Manager, the consent mode updates are automatically handled:

```html
<!-- GTM script will automatically respect consent signals -->
<script data-type="application/javascript" data-name="google-analytics">
    gtag('config', 'GTM-XXXXXXX');
</script>
```

#### GTM Event Triggers
The module can fire custom events to Google Tag Manager's dataLayer based on the configured callback functions in the CMS. The default configuration includes event triggers, but you can customize these in **Settings > Cookie Consent** for each service.

**Default Event Configuration:**
The default `klaro_defaults.yml` includes these event triggers:

**Accept Events** (fired when user accepts a service):
- `klaro-google-tag-manager-accepted`
- `klaro-google-analytics-accepted`
- `klaro-google-ads-accepted`
- `klaro-clarity-accepted`

**Decline Events** (fired when user declines a service):
- `klaro-google-tag-manager-declined`
- `klaro-google-analytics-declined`
- `klaro-google-ads-declined`
- `klaro-clarity-declined`

**Customizing Events:**
You can modify these events or add new ones by editing the callback fields in the CMS:
- **OnInitCallback**: Runs once when the service is initialized
- **OnAcceptCallback**: Runs when user accepts the service
- **OnDeclineCallback**: Runs when user declines the service

**Setting up GTM Triggers:**
1. In Google Tag Manager, go to **Triggers** > **New**
2. Choose **Custom Event** as trigger type
3. Set **Event name** to match your configured event (e.g., `klaro-google-analytics-accepted`)
4. Use this trigger to fire your Google Analytics, Ads, or other tracking tags

**Important Notes:**
- Events are only fired if configured in the respective callback fields in the CMS
- The Google Tag Manager service includes `ads_data_redaction: true` for GDPR compliance
- The consent mode initialization happens in `TrackingTop.ss` template before any Google scripts load
- You can customize all callback functions per service in **Settings > Cookie Consent**

# Styling
Example SCSS customisation
```scss
// !klaro
html .klaro {
	--notice-max-width: 440px;
	.cookie-modal,
	.cookie-notice {
		z-index: 9100;
		a {
			color: lighten($link-color, 70%);
		}
		.cm-btn {
			cursor: pointer;
			font-size: 14px;
			border-radius: 0.1em;
			margin-right: 1.2em;
		}
	}

	.cookie-notice {
		.cn-body {
			// klaro sets font-size on block elements - we're calculating back to maintain horizontal spacing :-/
			@media (max-width: 1023px) {
				padding-right: #{$lh * math.div($font-size, 14px)}em !important;
				padding-left: #{$lh * math.div($font-size, 14px)}em !important;
				@include breakpoint($Mneg) {
					padding-right: #{0.5 * $lh * math.div($font-size, 14px)}em !important;
					padding-left: #{0.5 * $lh * math.div($font-size, 14px)}em !important;
				}
			}
		}
		h2 {
			font-size: 1.1em;
			margin-top: 0.6em;
		}
		p {
			margin: 0.3em 0 !important;
		}
		.cn-ok {
			display: flex;
			flex-wrap: wrap;
			justify-content: flex-start !important;
			.cn-buttons {
				display: flex !important;
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
				margin-right: 0;
				order: 2;
				flex: 0 0 auto;
				padding: 0.5em 0;
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
			background-color: $gray;
		}
		// slider-switches
		.cm-list-input:checked + .cm-list-label .slider {
			background-color: $link-color;
		}
		// required switch enabled
		.cm-list-input.required:checked + .cm-list-label .slider {
			background-color: darken($link-color, 10%);
			&::before {
				background-color: darken($white, 16%);
			}
		}
		// halve is used on parent if children are on & off
		.cm-list-input.only-required + .cm-list-label .slider,
		.cm-list-input.half-checked:checked + .cm-list-label .slider {
			background-color: mix($link-color, $white, 71%);
		}
		.cm-list-description {
			color: $gray--light;
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
	}
}
// klaro! contextual consent
[data-type="placeholder"] {
	position: absolute;
	background-color: $gray--light;
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
	width: 100%;
	height: 100%;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	padding: #{$lh}em;
	.cm-as-context-notice {
		height: auto;
	}
	.context-notice {
		&:last-child {
			margin-bottom: 0;
		}
		.cm-buttons {
			display: flex;
			gap: 1em;
		}
		button.cm-btn {
			display: inline-block;
			padding: #{math.div($lh, 4)}em #{math.div($lh, 2)}em;
			border: none;
			text-transform: uppercase;
			color: $white;
			font-size: 1em;
			@include bold;
			border-radius: 0;
			margin: 0 !important;
			cursor: pointer;
			&:first-of-type {
				background-color: $link-color;
			}
			&:last-of-type {
				background-color: mix($link-color, $gray--light, 70%);
			}
			&:not(:last-of-type) {
				margin-right: #{$lh}em;
			}
		}
	}
}
```

# Todo
- add template-parser to add data-attributes

## Resources
- [Klaro! Documentation](https://klaro.kiprotect.com/docs), [Annotated Config](https://klaro.org/docs/integration/annotated-configuration)
- [Google Consent Mode v2 Guide](https://developers.google.com/tag-platform/security/guides/consent)
- [SilverStripe Configuration Documentation](https://docs.silverstripe.org/en/developer_guides/configuration/)
