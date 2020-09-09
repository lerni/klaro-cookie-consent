<?php

class CookieCategory extends DataObject
{
    private static $singular_name = 'Cookie Category';

    private static $db = [
        'Title' => 'Varchar',
        'Key' => 'Varchar',
        'Content' => 'Text',
        'Required' => 'Boolean',
        'SortOrder' => 'Int'
    ];

    private static $has_many = [
        'CookieEntries' => CookieEntry::class
    ];

    private static $default_sort = 'SortOrder ASC';

    private static $field_labels = [
        'Title' => 'Titel',
        'Key' => 'Javascript Key',
        'Content' => 'Text',
        'Required' => 'Required'
    ];

    private static $summary_fields = [
        'Title' => 'Titel',
        'Key' => 'Javascript Key',
        'Required' => 'Required'
    ];

    private static $searchable_fields = [
        'Title',
        'Key'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('CookieEntries');

        return $fields;
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        $entry = CookieCategory::get()->first();
        if (!$entry) {
            $GenerateConfig = Config::inst()->get('CookieCategory', 'OnInit');
            foreach ($GenerateConfig as $key => $category) {
                $CookieCategory = CookieCategory::create();
                // write to have an ID needed to associate CookieEntries()
                $CookieCategory->write();
                foreach ($category as $nestedkey => $nestedvalue) {
                    if (is_array($nestedvalue) && $nestedkey == 'CookieEntries') {
                        $CookieEntry = CookieEntry::create($nestedvalue);
                        $CookieEntry->CookieCategoryID = $CookieCategory->ID;
                        $CookieEntry->write();
                    } else {
                        $CookieCategory->{$nestedkey} = $nestedvalue;
                    }
                }
                $CookieCategory->write();
            }
            DB::alteration_message("Added default CookieEntry & CookieCategories", "created");

            // since we just add to SiteConfig, DB/ORM defaults wont get us anywhere
            // therefor we assume its save to write values to SiteConfig if empty and  also no CookieCategory is present
            $siteConfig = SiteConfig::current_site_config();

            $defaults = [
                'ConsentNoticeDescription' => 'Auf dieser Webseite werden Cookies für folgende Zwecke eingesetzt: {purposes}.',
                'ConsentModalTitle' => 'Verwendete Cookies',
                'ConsentModalDescription' => 'Datenschutz-Einstellungen für diese Webseite einsehen und anpassen.',
                'ConsentModalPrivacyPolicyName' => 'Datenschutzerklärung',
                'ConsentModalPrivacyPolicyText' => 'Details {privacyPolicy}.',
                'AcceptAll' => 'Allen zustimmen',
                'AcceptSelected' => 'Auswahl speichern',
                'Decline' => 'Ablehnen'
            ];
            $siteConfigNeedsWrite = 0;
            foreach($defaults as $key => $value)
            {
                if($siteConfig->{$key} == '') {
                    $siteConfig->{$key} = $value;
                    $siteConfigNeedsWrite = 1;
                }
            }
            if($siteConfigNeedsWrite) {
                $siteConfig->write();
                DB::alteration_message('Added default values for entries per KlaroSiteConfigExtension', 'changed');
            }
        }
    }
}
