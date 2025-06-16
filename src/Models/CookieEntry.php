<?php

namespace Kraftausdruck\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use Kraftausdruck\Models\CookieCategory;

class CookieEntry extends DataObject
{
    private static $singular_name = 'CookieEntry';

    private static $table_name = 'CookieEntry';

    private static $db = [
        'Title' => 'Varchar',
        'CookieKey' => 'Varchar',
        'Purpose' => 'Text',
        'CookieName' => 'Varchar',
        'Default' => 'Enum("false,true", "false")',
        'OptOut' => 'Enum("false,true", "false")',
        'Required' => 'Enum("false,true", "0")',
        'SortOrder' => 'Int',
        'ConsentModeType' => 'Enum("analytics_storage,ad_storage,ad_user_data,ad_personalization,functionality_storage,personalization_storage,security_storage", "")',
        'ConsentModeDefault' => 'Enum("granted,denied", "denied")',
        'OnAcceptCallback' => 'Text',
        'OnDeclineCallback' => 'Text'
    ];

    // do not translate with fluent
    private static $field_exclude = [
        'CookieKey'
    ];

    private static $has_one = [
        'CookieCategory' => CookieCategory::class
    ];

    private static $default_sort = 'SortOrder ASC';

    private static $field_labels = [];

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = _t(__CLASS__ . '.TITLE', 'Title');
        $labels['CookieKey'] = _t(__CLASS__ . '.COOKIEKEY', 'Cookie Key');
        $labels['Purpose'] = _t(__CLASS__ . '.PURPOSE', 'Purpose');
        $labels['CookieName'] = _t(__CLASS__ . '.COOKIENAME', 'Cookie Name');
        $labels['Default'] = _t(__CLASS__ . '.DEFAULT', 'Default');
        $labels['OptOut'] = _t(__CLASS__ . '.OPTOUT', 'Opt Out');
        $labels['Required'] = _t(__CLASS__ . '.REQUIRED', 'Service Required');
        $labels['ConsentModeType'] = _t(__CLASS__ . '.CONSENTMODETYPE', 'Google Consent Mode Type');
        $labels['ConsentModeDefault'] = _t(__CLASS__ . '.CONSENTMODEDEFAULT', 'Default Consent State');
        $labels['OnAcceptCallback'] = _t(__CLASS__ . '.ONACCEPTCALLBACK', 'On Accept Callback');
        $labels['OnDeclineCallback'] = _t(__CLASS__ . '.ONDECLINECALLBACK', 'On Decline Callback');
        $labels['CookieCategory'] = _t(__CLASS__ . '.COOKIECATEGORY', 'Cookie Category');

        return $labels;
    }

    public function getCMSValidator()
    {
        return new RequiredFields([
            'Title',
            'CookieName',
            'Purpose'
        ]);
    }

    public function CookieNamesJS()
    {
        $names = array_map('trim', explode(',', (string)$this->CookieName));
        $names = array_filter($names); // Remove empty values

        if (empty($names)) {
            return '[]';
        }

        return '[' . implode(', ', array_map(function($name) {
            return json_encode($name, JSON_UNESCAPED_UNICODE);
        }, $names)) . ']';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('SortOrder');

        // Add Required field to Main tab
        $requiredOptions = singleton(CookieEntry::class)->dbObject('Required')->enumValues();
        $CategoryRequired = _t(__CLASS__ . '.NOTSET', 'not set');
        if ($this->CookieCategory() && $this->CookieCategory()->exists()) {
            $CategoryRequired = $this->CookieCategory()->Required ? 'true' : 'false';
        }
        $fields->addFieldToTab('Root.Main',
            DropdownField::create('Required', _t(__CLASS__ . '.REQUIRED', 'Service Required'))
                ->setSource($requiredOptions)
                ->setEmptyString('--')
                ->setDescription(_t(__CLASS__ . '.REQUIREDDESCRIPTION', 'Overrides category setting: <strong>{CategoryRequired}</strong> - i.g. Tag Manager', ['CategoryRequired' => $CategoryRequired]))
        );

        // Add Consent Mode v2 fields
        $consentModeTypes = singleton(CookieEntry::class)->dbObject('ConsentModeType')->enumValues();
        $consentModeDefaults = singleton(CookieEntry::class)->dbObject('ConsentModeDefault')->enumValues();

        $fields->addFieldsToTab('Root.Main', [
            DropdownField::create('ConsentModeType', _t(__CLASS__ . '.CONSENTMODETYPE', 'Google Consent Mode Type'))
                ->setSource($consentModeTypes)
                ->setEmptyString(_t(__CLASS__ . '.CONSENTMODETYPEEMPTY', '-- Select Type --'))
                ->setDescription(_t(__CLASS__ . '.CONSENTMODETYPEDESCRIPTION', 'Select the Google Consent Mode type this service relates to')),

            DropdownField::create('ConsentModeDefault', _t(__CLASS__ . '.CONSENTMODEDEFAULT', 'Default Consent State'))
                ->setSource($consentModeDefaults)
                ->setDescription(_t(__CLASS__ . '.CONSENTMODEDEFAULTDESCRIPTION', 'Default state before user gives consent')),

            TextareaField::create('OnAcceptCallback', _t(__CLASS__ . '.ONACCEPTCALLBACK', 'On Accept Callback'))
                ->setDescription(_t(__CLASS__ . '.ONACCEPTCALLBACKDESCRIPTION', 'JavaScript code to run when user accepts this service (optional)'))
                ->setRows(3),

            TextareaField::create('OnDeclineCallback', _t(__CLASS__ . '.ONDECLINECALLBACK', 'On Decline Callback'))
                ->setDescription(_t(__CLASS__ . '.ONDECLINECALLBACKDESCRIPTION', 'JavaScript code to run when user declines this service (optional)'))
                ->setRows(3)
        ]);

        return $fields;
    }

    public function RequiredWithInherence() {
        // Service-level Required takes precedence
        if ($this->Required && $this->Required !== '0') {
            return $this->Required;
        }

        // Fall back to category-level Required
        $category = $this->CookieCategory();
        if ($category && $category->Required) {
            return $category->Required;
        }
        // Default to false
        return 'false';
    }

    /**
     * Get consent mode JavaScript for this service
     */
    public function getConsentModeJS()
    {
        if (!$this->ConsentModeType) {
            return [
                'onAccept' => $this->OnAcceptCallback ?: '',
                'onDecline' => $this->OnDeclineCallback ?: ''
            ];
        }

        $onAccept = sprintf("gtag('consent', 'update', {'%s': 'granted'});", $this->ConsentModeType);
        $onDecline = sprintf("gtag('consent', 'update', {'%s': 'denied'});", $this->ConsentModeType);

        // Add custom callbacks if provided
        if ($this->OnAcceptCallback) {
            $onAccept .= "\n" . $this->OnAcceptCallback;
        }
        if ($this->OnDeclineCallback) {
            $onDecline .= "\n" . $this->OnDeclineCallback;
        }

        return [
            'onAccept' => $onAccept,
            'onDecline' => $onDecline
        ];
    }
}
