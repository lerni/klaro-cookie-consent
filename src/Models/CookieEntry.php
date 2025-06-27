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

    private static $summary_fields = [
        'Title' => 'Name',
        'CookieCategory.Title' => 'Category'
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
            // Check if the name is a regex pattern (starts and ends with /)
            if (preg_match('/^\/(.+)\/([gimsuxy]*)$/', $name, $matches)) {
                // It's a regex pattern - return as JavaScript regex
                $pattern = $matches[1];
                $flags = $matches[2] ?? '';

                // Escape backslashes for JavaScript
                $pattern = addcslashes($pattern, '\\');

                return '/' . $pattern . '/' . $flags;
            } else {
                // It's a regular string - return as JSON string
                return json_encode($name, JSON_UNESCAPED_UNICODE);
            }
        }, $names)) . ']';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('SortOrder');

        $CategoryRequired = _t(__CLASS__ . '.NOTSET', 'not set');
        if ($this->CookieCategory() && $this->CookieCategory()->exists()) {
            $CategoryRequired = $this->CookieCategory()->Required ? 'true' : 'false';
        }

        if ($requiredField = $fields->dataFieldByName('Required')) {
            $requiredField->setEmptyString('--');
            $requiredField->setDescription(_t(__CLASS__ . '.REQUIREDDESCRIPTION', 'Overrides category setting: <strong>{CategoryRequired}</strong> - i.g. Tag Manager', ['CategoryRequired' => $CategoryRequired]));
        }

        if ($CookieNameField = $fields->dataFieldByName('CookieName')) {
            $CookieNameField->setDescription(_t(__CLASS__ . '.NameFieldDescription', '"cookieName" for exact match, "_ga,_gat,_gid" for multiple cookies (comma-separated), "/^_ga.*$/" for regex patterns.'));
        }

        $fields->addFieldsToTab('Root.Main', [
            TextareaField::create('OnAcceptCallback', _t(__CLASS__ . '.ONACCEPTCALLBACK', 'On Accept Callback'))
                ->setDescription(_t(__CLASS__ . '.ONACCEPTCALLBACKDESCRIPTION', 'JavaScript code to run when user accepts this service. Include Consent Mode calls here if needed for this service.'))
                ->setRows(3),

            TextareaField::create('OnDeclineCallback', _t(__CLASS__ . '.ONDECLINECALLBACK', 'On Decline Callback'))
                ->setDescription(_t(__CLASS__ . '.ONDECLINECALLBACKDESCRIPTION', 'JavaScript code to run when user declines this service. Include Consent Mode calls here if needed for this service.'))
                ->setRows(3)
        ]);

        return $fields;
    }

    public function RequiredWithInherence() {
        // Service-level Required takes precedence
        if ($this->Required !== null) {
            return $this->Required;
        }

        // Fall back to category-level Required
        $category = $this->CookieCategory();
        if ($category && $category->Required) {
            return 'true';
        }
        // Default to false
        return 'false';
    }

}
