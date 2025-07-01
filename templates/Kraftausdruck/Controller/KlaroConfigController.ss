<% cached 'CookieConfig', $SiteConfig.LastEdited, $List('Kraftausdruck\Models\CookieCategory').max('LastEdited'), $List('Kraftausdruck\Models\CookieCategory').count(), $List('Kraftausdruck\Models\CookieEntry').max('LastEdited'), $List('Kraftausdruck\Models\CookieEntry').count() %><% with $SiteConfig %>var klaroConfig = {
	CookieIsActive: <% if $CookieIsActive %>true<% else %>false<% end_if %>,
	elementID: 'klaro',
	cookieName: 'klaro',
	acceptAll: true,
	default: false,
	cookieExpiresAfterDays: 365,
	noNotice: false,
	<% if ConsentNoticeTitle %>showNoticeTitle: true,<% end_if %>
	translations: {<% loop $Up.LocalisedSiteConfigs %>
		{$KLang()}: {
			privacyPolicy: '{$CookieLinkPrivacy.Link()}',
			acceptAll: '{$AcceptAll.JS}',
			acceptSelected: '{$AcceptSelected.JS}',
			decline: '{$Decline.JS}',
			ok: '<% if $ConsentNoticeOK %>{$ConsentNoticeOK.JS}<% else %>{$AcceptAll.JS}<% end_if %>',
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
			<% loop $LocalizedCookieCategories %>    {$Key.JS}: '{$Title.JS}'<% if not $IsLast %>,<% end_if %>
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
		}<% if not $IsLast %>,<% end_if %>
		<% end_loop %>
	},
	services: [
	<% loop $Up.GlobalServices %> {
		name : '{$CookieKey.JS}',
		required: $RequiredWithInherence,
		default: {$Default},
		optOut: {$OptOut},
		purposes : ['{$CookieCategory.Key.JS}'],
		cookies : {$CookieNamesJS.RAW},
		<% if $onInitCallback %>onInit: `$onInitCallback.RAW`,<% end_if %>
		<% if $OnAcceptCallback %>onAccept: `$OnAcceptCallback.RAW`,<% end_if %>
		<% if $OnDeclineCallback %>onDecline: `$OnDeclineCallback.RAW`,<% end_if %>
		translations: {<% loop $ServiceTranslations %>
			{$KLang}: {
				title: '{$Title.JS}',
				description: '{$Description.JS}'
			}<% if not $IsLast %>,<% end_if %>
		<% end_loop %>}
	}<% if not $IsLast %>,<% end_if %>
	<% end_loop %>]
}<% end_with %><% end_cached %>
