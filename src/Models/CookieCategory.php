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
                if (is_array($category)) {
                    $CookieCategory = CookieCategory::create();
                    if (array_key_exists('Title', $category)) {
                        $CookieCategory->Title = $category['Title'];
                    }
                    if (array_key_exists('Required', $category)) {
                        $CookieCategory->Required = $category['Required'];
                    }
                    if (array_key_exists('Content', $category)) {
                        $CookieCategory->Content = $category['Content'];
                    }
                    if (array_key_exists('Key', $category)) {
                        $CookieCategory->Key = $category['Key'];
                    }
                    $CookieCategory->write();
                    if (array_key_exists('CookieEntries', $category)) {
                        if(is_array($category['CookieEntries'])) {

                            $CookieEntry = CookieEntry::create($category['CookieEntries']);

                            $CookieEntry->CookieCategoryID = $CookieCategory->ID;
                            $CookieEntry->write();

                        }
                    }
                }
            }
            DB::alteration_message("Added default CookieEntry & CookieCategories", "created");
        }
    }
}
