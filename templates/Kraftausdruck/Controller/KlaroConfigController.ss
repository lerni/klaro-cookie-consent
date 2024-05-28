<% cached 'CookieConfig', $Locale, $SiteConfig.LastEdited, $List('Kraftausdruck\Models\CookieCategory').max('LastEdited'), $List('Kraftausdruck\Models\CookieCategory').count(), $List('Kraftausdruck\Models\CookieEntry').max('LastEdited'), $List('Kraftausdruck\Models\CookieEntry').count() %>
<% with $SiteConfig %>var klaroConfig = {
	CookieIsActive: '{$CookieIsActive}',
	elementID: 'klaro',
	cookieName: 'klaro',
	acceptAll: true,
	default: false,
	cookieExpiresAfterDays: 365,
	privacyPolicy: '{$CookieLinkPrivacy.Link()}',
	lang: '{$Lang()}',
	noNotice: false,
	<% if ConsentNoticeTitle %>showNoticeTitle: true,<% end_if %>
	translations: {
		{$Lang()}: {
			acceptAll: '{$AcceptAll}',
			acceptSelected: '{$AcceptSelected}',
			decline: '{$Decline}',
			ok: '<% if $ConsentNoticeOK %>{$ConsentNoticeOK}<% else %>{$AcceptAll}<% end_if %>',
			consentModal: {
				title: '{$ConsentModalTitle.JS}',
				description: '{$ConsentModalDescription.JS}',
			},
			consentNotice: {
				title: '{$ConsentNoticeTitle.JS}',
				description: '{$ConsentNoticeDescription.JS}',
				learnMore: '{$ConsentNoticeLearnMore.JS}'
			},
			purposes: {
			<% loop $CookieCategories %>    {$Key.JS}: '{$Title.JS}'<% if not $IsLast %>,<% end_if %>
			<% end_loop %>},
			contextualConsent: {
				acceptAlways: '{$ContextualConsentAcceptAlways.JS}',
				acceptOnce: '{$ContextualConsentAcceptOnce.JS}',
				description: '{$ContextualConsentDescription.JS}'
			},
			privacyPolicy: {
				name: '{$ConsentModalPrivacyPolicyName.JS}',
				text: '{$ConsentModalPrivacyPolicyText.JS}'
			}
		}
	},
	services : [
	<% loop $CookieEntries %> {
			name : '{$CookieKey.JS}',
			<% if $CookieCategory.Required %>required: {$CookieCategory.Required},<% end_if %>
			default: {$Default},
			optOut: {$OptOut},
			title : '{$Title.JS}',
			description : ['{$Purpose.JS}'],
			purposes : ['{$CookieCategory.Key.JS}'],
			cookies : {$CookieNamesJS.RAW}
		}<% if not $IsLast %>,<% end_if %>
	<% end_loop %>]
}<% end_with %><% end_cached %>
