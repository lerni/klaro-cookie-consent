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
            consentModal: {
                title: '{$ConsentModalTitle}',
                description: '{$ConsentModalDescription}',
                privacyPolicy: {
                    name: '{$ConsentModalPrivacyPolicyName}',
                    text: '{$ConsentModalPrivacyPolicyText}'
                }
            },
            consentNotice: {
                description: '{$ConsentNoticeDescription}',
                learnMore: '{$ConsentNoticeLearnMore}'
            },
            purposes: {
            <% loop $CookieCategories %>    {$Key}: '{$Title}'<% if not $Last %>,<% end_if %>
            <% end_loop %>},
        }
    },
    apps : [
    <% loop $CookieEntries %> {
            name : '{$CookieKey}',
            <% if $CookieCategory.Required %>required: {$CookieCategory.Required},<% end_if %>
            default: {$Default},
            optOut: {$OptOut},
            title : '{$Title}',
            description : ['{$Purpose}'],
            purposes : ['{$CookieCategory.Key}'],
            cookies : {$CookieNamesJS.RAW}
        }<% if not $Last %>,<% end_if %>
    <% end_loop %>]
}<% end_with %>
