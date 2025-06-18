<?php

namespace Kraftausdruck\Models;

use SilverStripe\ORM\DataObject;
use Kraftausdruck\Models\CookieEntry;

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

    // do not translate with fluent
    private static $field_exclude = [
        'Key'
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

        $fields->removeByName([
            'CookieEntries',
            'SortOrder'
        ]);

        if ($RequiredCheckbox = $fields->dataFieldByName('Required')) {
            $RequiredCheckbox->setDescription(_t( __CLASS__ . '.RequiredCheckboxDescription', 'If set, inherited to CookieEntries if not explicitly set otherwise there.'));
        }

        return $fields;
    }
}
