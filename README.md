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

</details>

## Resources
- [Klaro! Documentation](https://klaro.kiprotect.com/docs), [Annotated Config](https://klaro.org/docs/integration/annotated-configuration)
- [Google Consent Mode v2 Guide](https://developers.google.com/tag-platform/security/guides/consent)
- [SilverStripe Configuration Documentation](https://docs.silverstripe.org/en/developer_guides/configuration/)
