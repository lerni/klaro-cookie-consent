<?php

namespace Kraftausdruck\Tasks;

use SilverStripe\ORM\DB;
use SilverStripe\Dev\BuildTask;
use Symfony\Component\Yaml\Yaml;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

// this task imports translations from NPM package
// it's meant to be run on dev-env only
class LangFilesFromNPM extends BuildTask
{
    protected string $title = 'Generate Language Files from NPM';
    protected static string $description = 'generates language files based on klaro yml files (requires npm packages to be installed)';
    protected static string $commandName = 'gen-lang-files';

	protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $modulePath = dirname(dirname(dirname(__FILE__)));

        // Check if source directory exists
        $sourceDir = $modulePath .'/client/node_modules/klaro/src/translations';
        if (!is_dir($sourceDir)) {
            $output->writeln("<error>{$sourceDir} DOESN'T EXIST --- please run npm install first</error>");
            return Command::FAILURE;
        }

        $output->writeln("Reading translations from: {$sourceDir}");
        $filesFromKlaro = glob($sourceDir . '/*.yml');

        if (empty($filesFromKlaro)) {
            $output->writeln("<warning>No translation files found in {$sourceDir}</warning>");
            return Command::SUCCESS;
        }

        $allTranslations = [];
        $processedCount = 0;

        foreach ($filesFromKlaro as $file) {
            try {
                $filecontent = Yaml::parseFile($file);
                $lang = $this->extractLanguageFromFilename($file);

                // Add this language's translations to the combined array
                $allTranslations[$lang] = $filecontent;
                $processedCount++;
                $output->writeln("Processed: {$lang}");

            } catch (\Exception $e) {
                $output->writeln("<error>Failed to parse {$file}: " . $e->getMessage() . "</error>");
                continue;
            }
        }

        if ($processedCount > 0) {
            // Write all translations to a single file
            $outputFile = $modulePath .'/all-translations-from-klaro.yml';
            try {
                $allTranslationsYML = Yaml::dump($allTranslations);
                file_put_contents($outputFile, $allTranslationsYML);
                $output->writeln("<info>Successfully wrote: {$outputFile} with {$processedCount} languages</info>");
            } catch (\Exception $e) {
                $output->writeln("<error>Failed to write output file: " . $e->getMessage() . "</error>");
                return Command::FAILURE;
            }
        } else {
            $output->writeln("<warning>No files were processed successfully</warning>");
        }

        return Command::SUCCESS;
    }

    /**
     * Extract language code from filename and handle special cases
     */
    private function extractLanguageFromFilename(string $file): string
    {
        $lang = basename($file, '.yml');

        // Handle special cases
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

        return $lang;
    }
}
