<?php

namespace Kraftausdruck\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\RequiredFields;
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
        'SortOrder' => 'Int'
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

        return $fields;
    }
}
