<?php

namespace Kraftausdruck\Tasks;

use SilverStripe\ORM\DB;
use SilverStripe\i18n\i18n;
use SilverStripe\Dev\BuildTask;
use Symfony\Component\Yaml\Yaml;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class KlaroDefaults extends BuildTask
{
    protected string $title = 'Manage Klaro! Records';
    protected static string $description = 'Creates defaults and export entries';

    protected static string $commandName = 'klaro-defaults';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $modulePath = dirname(dirname(dirname(__FILE__)));

        $locale = i18n::get_locale() ?: i18n::config()->get('default_locale');
        $language = i18n::getData()->langFromLocale($locale);

        $output->writeln("Using locale: {$locale}");
        $output->writeln("Language: {$language}");
        $output->writeln("RFC1766: " . i18n::convert_rfc1766($locale));

        // Check for translations from Klaro
        $translationsFile = $modulePath . '/all-translations-from-klaro.yml';

        if (!file_exists($translationsFile)) {
            $output->writeln("<error>Translation file not found: {$translationsFile}</error>");
            return Command::FAILURE;
        }

        try {
            $allTranslations = Yaml::parseFile($translationsFile);
        } catch (\Exception $e) {
            $output->writeln("<error>Failed to parse translation file: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }

        if (!isset($allTranslations[$language])) {
            $output->writeln("<warning>No translation entries found for language: {$language}</warning>");
            return Command::SUCCESS; // Not an error, just no translations available
        }

        $defaults_from_klaro = $allTranslations[$language];
        $output->writeln("Found " . count($defaults_from_klaro) . " translation entries for language: {$language}");

        // Create mapping between Klaro translations and SiteConfig fields
        $klaroToSiteConfigMapping = [
            'acceptAll' => 'AcceptAll',
            'acceptSelected' => 'AcceptSelected',
            'decline' => 'Decline',
            'ok' => 'ConsentNoticeOK',
            'consentNotice.learnMore' => 'ConsentNoticeLearnMore',
            'consentNotice.title' => 'ConsentNoticeTitle',
            'consentNotice.description' => 'ConsentNoticeDescription',
            'consentModal.title' => 'ConsentModalTitle',
            'consentModal.description' => 'ConsentModalDescription',
            'privacyPolicy.name' => 'ConsentModalPrivacyPolicyName',
            'privacyPolicy.text' => 'ConsentModalPrivacyPolicyText'
        ];

        // Helper function to get nested array value by dot notation
        $getNestedValue = function($array, $key) {
            $keys = explode('.', $key);
            $value = $array;
            foreach ($keys as $k) {
                if (isset($value[$k])) {
                    $value = $value[$k];
                } else {
                    return null;
                }
            }
            return $value;
        };

        // Get SiteConfig and prepare defaults from Klaro
        $siteConfig = SiteConfig::current_site_config();
        $siteConfigNeedsWrite = false;

        // First, set values from Klaro translations
        foreach ($klaroToSiteConfigMapping as $klaroKey => $siteConfigField) {
            $klaroValue = $getNestedValue($defaults_from_klaro, $klaroKey);
            if ($klaroValue && empty($siteConfig->{$siteConfigField})) {
                $siteConfig->{$siteConfigField} = $klaroValue;
                $siteConfigNeedsWrite = true;
                $output->writeln("Set {$siteConfigField} from Klaro: {$klaroValue}");
            }
        }

        // Override with custom SilverStripe translations if available
        $customDefaults = [
            'ConsentNoticeOK' => _t('Kraftausdruck\KlaroCookie.ConsentNoticeOK', "That's ok"),
            'ConsentNoticeLearnMore' => _t('Kraftausdruck\KlaroCookie.ConsentNoticeLearnMore', 'Let me choose'),
            'ConsentNoticeTitle' => _t('Kraftausdruck\KlaroCookie.ConsentNoticeTitle', 'Cookie Consent'),
            'ConsentNoticeDescription' => _t('Kraftausdruck\KlaroCookie.ConsentNoticeDescription', 'Hi! Could we please enable some additional services for {purposes}? You can always change or withdraw your consent later.'),
            'ConsentModalTitle' => _t('Kraftausdruck\KlaroCookie.ConsentModalTitle', 'Services we would like to use'),
            'ConsentModalDescription' => _t('Kraftausdruck\KlaroCookie.ConsentModalDescription', "Here you can assess and customize the services that we'd like to use on this website. You're in charge! Enable or disable services as you see fit."),
            'ConsentModalPrivacyPolicyName' => _t('Kraftausdruck\KlaroCookie.ConsentModalPrivacyPolicyName', 'privacy policy'),
            'ConsentModalPrivacyPolicyText' => _t('Kraftausdruck\KlaroCookie.ConsentModalPrivacyPolicyText', 'To learn more, please read our {privacyPolicy}.'),
            'AcceptAll' => _t('Kraftausdruck\KlaroCookie.AcceptAll', 'Accept all'),
            'AcceptSelected' => _t('Kraftausdruck\KlaroCookie.AcceptSelected', 'Accept selected'),
            'Decline' => _t('Kraftausdruck\KlaroCookie.Decline', 'I decline')
        ];

        // Override Klaro values with custom translations if they exist
        foreach ($customDefaults as $field => $customValue) {
            if (!empty($customValue) && $customValue !== $field) {
                $siteConfig->{$field} = $customValue;
                $siteConfigNeedsWrite = true;
                $output->writeln("Overrode {$field} with custom translation: {$customValue}");
            }
        }

        // Write SiteConfig if any changes were made
        if ($siteConfigNeedsWrite) {
            $siteConfig->write();
            $output->writeln('<info>Updated SiteConfig with Klaro defaults and custom translations</info>');
        } else {
            $output->writeln('<comment>No SiteConfig updates needed - all fields already have values</comment>');
        }

        return Command::SUCCESS;
    }
}
