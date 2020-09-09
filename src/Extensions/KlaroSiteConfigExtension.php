<?php

namespace Kraftausdruck\Extensions;

use Locale;
use SilverStripe\i18n\i18n;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\CMS\Model\SiteTree;
use Kraftausdruck\Models\CookieEntry;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextareaField;
use Kraftausdruck\Models\CookieCategory;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\Forms\GridField\GridField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

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

    private static $translate = [
        'ConsentNoticeDescription',
        'ConsentModalTitle',
        'ConsentModalDescription',
        'ConsentModalPrivacyPolicyName',
        'ConsentModalPrivacyPolicyText',
        'AcceptAll',
        'AcceptSelected',
        'Decline'
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

        $CategoryGridFieldConfig = GridFieldConfig_RecordEditor::create();
        $CategoryGridFieldConfig->addComponent(new GridFieldOrderableRows('SortOrder'));
        $fields->addFieldToTab(
            $tab,
            GridField::create('CookieCategory', 'Cookie Kategorien', CookieCategory::get(), $CategoryGridFieldConfig)
        );

        $CookieGridFieldConfig = GridFieldConfig_RecordEditor::create();
        $CookieGridFieldConfig->addComponent(new GridFieldOrderableRows('SortOrder'));
        $fields->addFieldToTab(
            $tab,
            GridField::create('CookieEntry', 'Cookies', CookieEntry::get(), $CookieGridFieldConfig)
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
