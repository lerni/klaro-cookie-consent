<% cached 'CookieConfig', $SiteConfig.LastEdited, $List('CookieCategory').max('LastEdited'), $List('CookieCategory').count(), $List('CookieEntry').max('LastEdited'), $List('CookieEntry').count() %><% with $SiteConfig %>var klaroConfig = {
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
                description: '{$ConsentNoticeDescription}'
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
}<% end_with %><% end_cached %>
