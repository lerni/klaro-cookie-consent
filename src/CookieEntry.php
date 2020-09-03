<?php

class CookieEntry extends DataObject
{
    private static $singular_name = 'CookieEntry';

    private static $db = [
        'Title' => 'Varchar',
        'CookieKey' => 'Varchar',
        'Provider' => 'Varchar',
        'Purpose' => 'Text',
        'Policy' => 'Varchar',
        'CookieName' => 'Varchar',
        'Default' => 'Enum("false,true", "false")',
        'OptOut' => 'Enum("false,true", "false")',
        'Time' => 'Varchar'
    ];

    private static $has_one = [
        'CookieCategory' => CookieCategory::class
    ];

    public function getCMSValidator()
    {
        return new RequiredFields([
            'Title',
            'Provider',
            'Purpose',
            'Policy'
        ]);
    }

    public function CookieNamesJS()
    {
        return json_encode(explode(',', $this->CookieName));
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        return $fields;
    }
}
