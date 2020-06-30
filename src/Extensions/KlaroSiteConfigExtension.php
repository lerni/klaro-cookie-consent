<?php

namespace Kraftausdruck\Extensions;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use Kraftausdruck\Models\CookieEntry;
use Kraftausdruck\Models\CookieCategory;
use Locale;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\i18n\i18n;

class KlaroSiteConfigExtension extends DataExtension
{
    private static $db = [
        'CookieIsActive' => 'Boolean',
        'ConsentNoticeDescription' => 'Text',
        'ConsentModalTitle' => 'Varchar',
        'ConsentModalDescription' => 'Text',
        'ConsentModalPrivacyPolicyName' => 'Varchar',
        'ConsentModalPrivacyPolicyText' => 'Varchar',
        'AcceptAll' => 'Varchar',
        'AcceptSelected' => 'Varchar',
        'Decline' => 'Varchar'
    ];


    private static $has_one = [
        'CookieLinkPrivacy' => SiteTree::class
    ];


    private static $defaults = [
        'CookieIsActive' => true,
        'ConsentNoticeDescription' => 'Auf dieser Webseite werden Cookies für folgende Zwecke eingesetzt: {purposes}.',
        'ConsentModalTitle' => 'Verwendete Cookies',
        'ConsentModalDescription' => 'Datenschutz-Einstellungen für diese Webseite einsehen und anpassen.',
        'ConsentModalPrivacyPolicyName' => 'Datenschutzerklärung',
        'ConsentModalPrivacyPolicyText' => 'Details {privacyPolicy}.',
        'AcceptAll' => 'Allen zustimmen',
        'AcceptSelected' => 'Auswahl speichern',
        'Decline' => 'Ablehnen'
    ];


    public function updateCMSFields(FieldList $fields)
    {

        $tab = 'Root.CookieConsent';
        $fields->addFieldToTab($tab, CheckboxField::create('CookieIsActive'));
        $fields->addFieldToTab($tab, TextareaField::create('ConsentNoticeDescription'));
        $fields->addFieldToTab($tab, TextField::create('ConsentModalTitle'));
        $fields->addFieldToTab($tab, TextareaField::create('ConsentModalDescription'));
        $fields->addFieldToTab($tab, TextField::create('ConsentModalPrivacyPolicyName'));
        $fields->addFieldToTab($tab, TextField::create('ConsentModalPrivacyPolicyText'));
        $fields->addFieldToTab($tab, TextField::create('AcceptAll'));
        $fields->addFieldToTab($tab, TextField::create('AcceptSelected'));
        $fields->addFieldToTab($tab, TextField::create('Decline'));

        $fields->addFieldToTab($tab, TreeDropdownField::create('CookieLinkPrivacyID', 'Link Privacy Policy', SiteTree::class));

        $fields->addFieldToTab(
            $tab,
            GridField::create('CookieCategory', 'Cookie Kategorien', CookieCategory::get(), GridFieldConfig_RecordEditor::create())
        );

        $fields->addFieldToTab(
            $tab,
            GridField::create('CookieEntry', 'Cookies', CookieEntry::get(), GridFieldConfig_RecordEditor::create())
        );
    }

    // todo may add a relation
    public function CookieEntries()
    {
        return CookieEntry::get();
    }


    // todo may add a relation
    public function CookieCategories()
    {
        return CookieCategory::get();
    }


    public function Lang()
    {   
        return Locale::getPrimaryLanguage(i18n::get_locale());
    }
}
