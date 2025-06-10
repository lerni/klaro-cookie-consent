<?php

namespace App\Dev\Tasks;

use \Page;
use Locale;
use SilverStripe\ORM\DB;
use SilverStripe\Dev\BuildTask;
use Symfony\Component\Yaml\Yaml;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Symfony\Component\Yaml\Exception\DumpException;


// this task imports translations from NPM package
// it's meant to be run on dev-env only
// generated files should be committed into git
class LangFilesFromNPM extends BuildTask
{
    protected $description = 'generates language files based on klaro yml files';
    private static $segment = 'gen-lang-files';

	public function run($request)
    {
        $this->generateLangFiles();
    }

    function generateLangFiles()
	{

        $modulePath = dirname(dirname(dirname(__FILE__)));
        // $lang = Locale::getPrimaryLanguage(i18n::get_locale());

        // may copy files over manually
        if (!is_dir($modulePath .'/translations-gen/')) {
            DB::alteration_message($modulePath .'/translations-gen/ DOEN\'T EXISTS --- may copy files over manually from ' . $modulePath .'/node_modules/klaro/src/translations', "error");
            $src = $modulePath .'/node_modules/klaro/src/translations';
            $dest = $modulePath .'/translations-gen';
            shell_exec('cp -r $src $dest');
        }

        $filesFromKlaro = glob($modulePath .'/translations-gen/*.yml');

        $i = 0;
        foreach ($filesFromKlaro as $file) {
            $filecontent = Yaml::parseFile($file);
            $lang = explode('/', $file);
            $lang = end($lang);
            $lang = explode('.', $lang);
            $lang = $lang[0];

            // gahh ATM just special case
            // todo:  better mapping
            // sr@latin.yml vs sr.yml & sr_cyrl.yml
            if ($lang == 'sr') {
                $lang = 'sr@latin';
            }
            if ($lang == 'sr_cyrl') {
                $lang = 'sr';
            }

            if ($lang[0] == '_') {
                $lang = ltrim($lang, '_');
            }

            if (!file_exists($modulePath .'/translations-gen/_'. $lang . '.yml')) {
                $newTranslationFile = [];
                $newTranslationFile[$lang] = $filecontent;
                $newTranslationFileYML = Yaml::dump($newTranslationFile);

                file_put_contents($modulePath .'/translations-gen/_'. $lang . '.yml', $newTranslationFileYML);

                $obj= DBHTMLText::create();
                $obj->setValue('<p><b>wrote:</b> '. $modulePath .'/translations-gen/_'. $lang . '.yml<br/></p>');
                echo ($obj);
                $i++;
            }
        }
        if (!$i) {
            $obj= DBHTMLText::create();
            $obj->setValue('<p><b>no file imported!</b></p>');
            echo ($obj);
        }
    }
}
