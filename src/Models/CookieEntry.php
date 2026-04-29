<?php

namespace Kraftausdruck\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextareaField;
use Kraftausdruck\Models\CookieCategory;
use SilverStripe\Forms\Validation\CompositeValidator;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;

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
        'OnInitCallback' => 'Text',
        'OnAcceptCallback' => 'Text',
        'OnDeclineCallback' => 'Text',
    ];

    // do not translate with fluent
    private static $field_exclude = [
        'CookieKey',
    ];

    private static $has_one = [
        'CookieCategory' => CookieCategory::class,
    ];

    private static $summary_fields = [
        'Title' => 'Name',
        'CookieCategory.Title' => 'Category',
    ];

    private static $default_sort = 'SortOrder ASC';

    private static $field_labels = [];

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = _t(self::class . '.TITLE', 'Title');
        $labels['CookieKey'] = _t(self::class . '.COOKIEKEY', 'Cookie Key');
        $labels['Purpose'] = _t(self::class . '.PURPOSE', 'Purpose');
        $labels['CookieName'] = _t(self::class . '.COOKIENAME', 'Cookie Name');
        $labels['Default'] = _t(self::class . '.DEFAULT', 'Default');
        $labels['OptOut'] = _t(self::class . '.OPTOUT', 'Opt Out');
        $labels['Required'] = _t(self::class . '.REQUIRED', 'Service Required');
        $labels['OnInitCallback'] = _t(self::class . '.ONINITCALLBACK', 'On Init Callback');
        $labels['OnAcceptCallback'] = _t(self::class . '.ONACCEPTCALLBACK', 'On Accept Callback');
        $labels['OnDeclineCallback'] = _t(self::class . '.ONDECLINECALLBACK', 'On Decline Callback');
        $labels['CookieCategory'] = _t(self::class . '.COOKIECATEGORY', 'Cookie Category');

        return $labels;
    }

    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $validator->addValidator(RequiredFieldsValidator::create([
            'Title',
            'Purpose',
        ]));

        return $validator;
    }

    public function CookieNamesJS()
    {
        $names = array_map('trim', explode(',', (string)$this->CookieName));
        $names = array_filter($names); // Remove empty values

        if (empty($names)) {
            return '[]';
        }

        return '[' . implode(', ', array_map(function ($name) {
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

        $CategoryRequired = _t(self::class . '.NOTSET', 'not set');
        if ($this->CookieCategory() && $this->CookieCategory()->exists()) {
            $CategoryRequired = $this->CookieCategory()->Required ? 'true' : 'false';
        }

        if ($CookieKeyField = $fields->dataFieldByName('CookieKey')) {
            $CookieKeyField->setDescription(_t(self::class . '.CookieKeyDescription', 'match HTML "data-name"-parameter'));
        }

        if ($requiredField = $fields->dataFieldByName('Required')) {
            $requiredField->setEmptyString('--');
            $requiredField->setDescription(_t(self::class . '.REQUIREDDESCRIPTION', 'Overrides category setting: <strong>{CategoryRequired}</strong> - i.g. Tag Manager', ['CategoryRequired' => $CategoryRequired]));
        }

        if ($CookieNameField = $fields->dataFieldByName('CookieName')) {
            $CookieNameField->setDescription(_t(self::class . '.NameFieldDescription', '"cookieName" for exact match, "_ga,_gat,_gid" for multiple cookies (comma-separated), "/^_ga.*$/" for regex patterns.'));
        }

        $fields->addFieldsToTab('Root.Main', [
            TextareaField::create('OnInitCallback', _t(self::class . '.ONINITCALLBACK', 'On Init Callback'))
                ->setDescription(_t(self::class . '.ONINITCALLBACKDESCRIPTION', 'JavaScript code to run when the service is initialized. This is called before the user makes any consent decision.'))
                ->setRows(3),

            TextareaField::create('OnAcceptCallback', _t(self::class . '.ONACCEPTCALLBACK', 'On Accept Callback'))
                ->setDescription(_t(self::class . '.ONACCEPTCALLBACKDESCRIPTION', 'JavaScript code to run when user accepts this service. Include Consent Mode calls here if needed for this service.'))
                ->setRows(3),

            TextareaField::create('OnDeclineCallback', _t(self::class . '.ONDECLINECALLBACK', 'On Decline Callback'))
                ->setDescription(_t(self::class . '.ONDECLINECALLBACKDESCRIPTION', 'JavaScript code to run when user declines this service. Include Consent Mode calls here if needed for this service.'))
                ->setRows(3),
        ]);

        return $fields;
    }

    public function RequiredWithInherence()
    {
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
