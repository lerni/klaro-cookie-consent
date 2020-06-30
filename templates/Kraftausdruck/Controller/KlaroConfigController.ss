<% with $SiteConfig %>var klaroConfig = {
    CookieIsActive: '{$SiteConfig.CookieIsActive}',
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
            acceptAll: '{$SiteConfig.AcceptAll}',
            acceptSelected: '{$SiteConfig.AcceptSelected}',
            decline: '{$SiteConfig.Decline}',
            consentModal: {
                title: '{$SiteConfig.ConsentModalTitle}',
                description: '{$SiteConfig.ConsentModalDescription}',
                privacyPolicy: {
                    name: '{$SiteConfig.ConsentModalPrivacyPolicyName}',
                    text: '{$SiteConfig.ConsentModalPrivacyPolicyText}'
                }
            },
            consentNotice: {
                description: '{$SiteConfig.ConsentNoticeDescription}'
            },
            purposes: {
            <% loop $CookieCategories %>    {$Key}: '{$Title}'<% if not $Last %>,<% end_if %>
            <% end_loop %>},
        }
    },
    apps : [
    <% loop $CookieEntries %> {
            name : '{$CookieKey}',
            default: {$CookieCategory.Required},
            title : '{$Title}',
            description : ['{$Purpose}'],
            purposes : ['{$CookieCategory.Key}'],
            cookies : {$CookieNamesJS.RAW}
        }<% if not $Last %>,<% end_if %>
    <% end_loop %>]
}<% end_with %>