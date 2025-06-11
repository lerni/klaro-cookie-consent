<?php

namespace App\Dev\Tasks;

use SilverStripe\ORM\DB;
use SilverStripe\Dev\BuildTask;
use Symfony\Component\Yaml\Yaml;

// this task imports translations from NPM package
// it's meant to be run on dev-env only
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

        // Check if source directory exists
        // $sourceDir = $modulePath .'/node_modules/klaro/src/translations';
        $sourceDir = $modulePath .'/client/node_modules/klaro/src/translations';
        if (!is_dir($sourceDir)) {
            DB::alteration_message($sourceDir . ' DOESN\'T EXIST --- please run npm install first', "error");
            return;
        }

        $filesFromKlaro = glob($sourceDir . '/*.yml');

        $allTranslations = [];
        $i = 0;
        foreach ($filesFromKlaro as $file) {
            $filecontent = Yaml::parseFile($file);
            $lang = explode('/', $file);
            $lang = end($lang);
            $lang = explode('.', $lang);
            $lang = $lang[0];

            // gahh special case
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

            // Add this language's translations to the combined array
            $allTranslations[$lang] = $filecontent;
            $i++;
        }

        if ($i > 0) {
            // Write all translations to a single file
            $allTranslationsYML = Yaml::dump($allTranslations);
            $outputFile = $modulePath .'/all-translations-from-klaro.yml';
            file_put_contents($outputFile, $allTranslationsYML);
            DB::alteration_message('wrote: '. $outputFile . ' with ' . $i . ' languages', "error");
        } else {
            DB::alteration_message('no file imported!', "info");
        }
    }
}
