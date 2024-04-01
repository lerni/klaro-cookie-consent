<?php

namespace App\Dev\Tasks;

use SilverStripe\ORM\DB;
use SilverStripe\Dev\BuildTask;
use Symfony\Component\Yaml\Yaml;

// imports translations from NPM package
// is meant to run in dev-env only
class LangFilesFromNPM extends BuildTask
{
    protected $description = 'Generates language files based on Klaro YML files';
    private static $segment = 'gen-lang-files';
    private const INLINE_LEVEL = 4;
    private const INDENTATION_SPACES = 2;

    public function run($request)
    {
        $this->generateLangFiles();
    }

    private function generateLangFiles()
    {
        $modulePath = dirname(dirname(dirname(__FILE__)));
        $filesFromKlaro = glob($modulePath . '/client/src/translations/*.yml');

        $filesProcessed = 0;
        foreach ($filesFromKlaro as $file) {
            $filecontent = Yaml::parseFile($file);
            $lang = basename($file, '.yml');

            // Concatenate keys based on nesting level
            $filecontent = $this->concatenateKeys($filecontent);

            // Nest translations under language code and namespace
            $filecontent = [$lang => ['Kraftausdruck\KlaroCookie' => $filecontent]];

            // Generate Silverstripe language file
            $ssLangFile = $modulePath . '/lang/' . $lang . '.yml';
            file_put_contents($ssLangFile, Yaml::dump($filecontent, self::INLINE_LEVEL, self::INDENTATION_SPACES));
            DB::alteration_message("{$modulePath}/lang/{$lang}.yml");
            $filesProcessed++;
        }
        if ($filesProcessed === 0) {
            DB::alteration_message("No files generated!");
        }
    }

    private function concatenateKeys($array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->concatenateKeys($value, $prefix . $key));
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }
}
