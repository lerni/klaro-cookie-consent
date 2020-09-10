<?php

namespace Kraftausdruck\Models;

use SilverStripe\ORM\DB;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Config\Config;
use SilverStripe\SiteConfig\SiteConfig;

class CookieCategory extends DataObject
{
    private static $singular_name = 'Cookie Category';

    private static $table_name = 'CookieCategory';

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
        'Content' => 'Text'
    ];

    private static $summary_fields = [
        'Title' => 'Titel',
        'Key' => 'Javascript Key'
    ];

    private static $searchable_fields = [
        'Title',
        'Key'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('CookieEntries');
        $fields->removeByName('SortOrder');

        return $fields;
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        $hasCookieCategories = CookieCategory::get()->count();
        $hasCookieEntries = CookieCategory::get()->count();
        if (!$hasCookieCategories && !$hasCookieEntries) {
            $GenerateConfig = Config::inst()->get('Kraftausdruck\Models\CookieCategory', 'OnInit');
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
            DB::alteration_message('Added default CookieEntry & CookieCategories', 'created');

            // since we just add fields to SiteConfig per extension, DB/ORM defaults won't get us anywhere
            // we assume it's save to write values to SiteConfig if a field is empty and also no CookieCategory & CookieEntry is present
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
            foreach ($defaults as $key => $value) {
                if ($siteConfig->{$key} == '') {
                    $siteConfig->{$key} = $value;
                    $siteConfigNeedsWrite = 1;
                }
            }
            if ($siteConfigNeedsWrite) {
                $siteConfig->write();
                DB::alteration_message('Added default values for entries per KlaroSiteConfigExtension', 'changed');
            }
        }
    }
}
