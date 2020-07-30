<?php

namespace Kraftausdruck\Models;

use SilverStripe\ORM\DB;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataObject;

class CookieCategory extends DataObject
{
    private static $singular_name = 'Cookie Category';

    private static $table_name = 'CookieCategory';

    private static $db = [
        'Title' => 'Varchar',
        'Key' => 'Varchar',
        'Content' => 'Text',
        'Required' => 'Boolean'
    ];

    private static $has_many = [
        'CookieEntries' => CookieEntry::class
    ];

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
            DB::alteration_message("Added default CookieEntry & CookieCategories", "created");
        }
    }
}
