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
	translations: {
		{$Lang()}: {
			acceptAll: '{$AcceptAll}',
			acceptSelected: '{$AcceptSelected}',
			decline: '{$Decline}',
			ok: '<% if $ConsentNoticeOK %>{$ConsentNoticeOK}<% else %>{$AcceptAll}<% end_if %>',
			consentModal: {
				title: '{$ConsentModalTitle.JS}',
				description: '{$ConsentModalDescription.JS}',
				privacyPolicy: {
					name: '{$ConsentModalPrivacyPolicyName.JS}',
					text: '{$ConsentModalPrivacyPolicyText.JS}'
				}
			},
			consentNotice: {
				description: '{$ConsentNoticeDescription.JS}',
				learnMore: '{$ConsentNoticeLearnMore.JS}'
			},
			purposes: {
			<% loop $CookieCategories %>    {$Key.JS}: '{$Title.JS}'<% if not $Last %>,<% end_if %>
			<% end_loop %>},
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
		}<% if not $Last %>,<% end_if %>
	<% end_loop %>]
}<% end_with %><% end_cached %>
