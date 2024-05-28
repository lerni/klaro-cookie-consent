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
        'ConsentNoticeTitle' => 'Varchar(100)',
        'ConsentNoticeDescription' => 'Text',
        'ConsentNoticeOK' => 'Varchar(100)',
        'ConsentModalTitle' => 'Varchar(100)',
        'ConsentNoticeLearnMore' => 'Varchar(100)',
        'ConsentModalDescription' => 'Text',
        'ConsentModalPrivacyPolicyName' => 'Varchar(100)',
        'ConsentModalPrivacyPolicyText' => 'Text',
        'AcceptAll' => 'Varchar(100)',
        'AcceptSelected' => 'Varchar(100)',
        'Decline' => 'Varchar(100)',
        'ContextualConsentAcceptAlways' => 'Varchar(100)',
        'ContextualConsentAcceptOnce' => 'Varchar(100)',
        'ContextualConsentDescription' => 'Text'
    ];

    private static $has_one = [
        'CookieLinkPrivacy' => SiteTree::class
    ];

    private static $translate = [
        'ConsentNoticeTitle',
        'ConsentNoticeDescription',
        'ConsentNoticeOK',
        'ConsentNoticeLearnMore',
        'ConsentModalTitle',
        'ConsentModalDescription',
        'ConsentModalPrivacyPolicyName',
        'ConsentModalPrivacyPolicyText',
        'AcceptAll',
        'AcceptSelected',
        'Decline',
        'ContextualConsentAcceptAlways',
        'ContextualConsentAcceptOnce',
        'ContextualConsentDescription'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $tab = 'Root.' . _t(__CLASS__ . '.COOKIETAB', 'CookieConsent');
        $fields->addFieldToTab($tab, CheckboxField::create('CookieIsActive', _t(__CLASS__ . '.CookieIsActive', 'Klaro! active')));
        $fields->addFieldToTab($tab, TextField::create('ConsentNoticeTitle', _t(__CLASS__ . '.CONSENTNOTICETITLE', 'Notice: Titel')));
        $fields->addFieldToTab($tab, TextareaField::create('ConsentNoticeDescription', _t(__CLASS__ . '.CONSENTNOTICEDESCRIPTION', 'Notice: Description')));
        $fields->addFieldToTab($tab, TextField::create('ConsentNoticeLearnMore', _t(__CLASS__ . '.CONSENTNOTICELEARNMORE', 'Notice: Cookie settings')));
        $fields->addFieldToTab($tab, TextField::create('ConsentNoticeOK', _t(__CLASS__ . '.CONSENTNOTICEOK', 'Notice: OK/accept')));
        $fields->addFieldToTab($tab, TextField::create('ConsentModalTitle', _t(__CLASS__ . '.CONSENTMODALTITLE', 'Modal: Title')));
        $fields->addFieldToTab($tab, TextareaField::create('ConsentModalDescription', _t(__CLASS__ . '.CONSENTMODALDESCRIPTION', 'Modal: Description')));
        $fields->addFieldToTab($tab, TextField::create('ConsentModalPrivacyPolicyName', _t(__CLASS__ . '.CONSENTMODALPRIVACYPOLICYNAME', 'Modal: Privacy Policy link name')));
        $fields->addFieldToTab($tab, TextareaField::create('ConsentModalPrivacyPolicyText', _t(__CLASS__ . '.CONSENTMODALPRIVACYPOLICYTEXT', 'Modal: Privacy Policy link text')));
        $fields->addFieldToTab($tab, TextField::create('AcceptAll', _t(__CLASS__ . '.ACCEPTALL', 'Modal: "Accept all"')));
        $fields->addFieldToTab($tab, TextField::create('AcceptSelected', _t(__CLASS__ . '.ACCEPTSELECTED', 'Modal: "Accept selected"')));
        $fields->addFieldToTab($tab, TextField::create('Decline', _t(__CLASS__ . '.DECLINE', 'Modal: "Decline"')));
        $fields->addFieldToTab($tab, TextField::create('ContextualConsentAcceptAlways', _t(__CLASS__ . '.CONTEXTUALCONSENTACCEPTALWAYS', 'Contextual: "Accept always"')));
        $fields->addFieldToTab($tab, TextField::create('ContextualConsentAcceptOnce', _t(__CLASS__ . '.CONTEXTUALCONSENTACCEPTONCE', 'Contextual: "Accept once"')));
        $fields->addFieldToTab($tab, TextareaField::create('ContextualConsentDescription', _t(__CLASS__ . '.CONTEXTUALCONSENTDESCRIPTION', 'Contextual: Description')));

        $fields->addFieldToTab($tab, TreeDropdownField::create('CookieLinkPrivacyID', _t(__CLASS__ . '.COOKIELINKPRIVACY', 'Link Privacy Policy'), SiteTree::class), 'AcceptAll');

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
