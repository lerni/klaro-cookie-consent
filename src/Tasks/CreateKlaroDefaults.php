<?php

namespace Kraftausdruck\Tasks;

use SilverStripe\ORM\DB;
use SilverStripe\i18n\i18n;
use SilverStripe\Dev\BuildTask;
use Symfony\Component\Yaml\Yaml;
use Kraftausdruck\Models\CookieCategory;
use SilverStripe\SiteConfig\SiteConfig;

class KlaroDefaults extends BuildTask
{
    protected $title = 'Manage Klaro! Records';
    protected $description = 'Creates defaults and export entries';

    private static $segment = 'klaro-defaults';

    public function run($request)
    {

        $modulePath = dirname(dirname(dirname(__FILE__)));

        $locale = $request->getVar('locale') ?: i18n::config()->get('default_locale');
        $language = i18n::getData()->langFromLocale($locale);

        DB::alteration_message('just locale: ' . $locale, "info");
        DB::alteration_message('language only: ' . $language, "info");
        DB::alteration_message('rfc1766: ' . i18n::convert_rfc1766($locale), "info");

        // Check for translations from Klaro
        $translationsFile = $modulePath . '/all-translations-from-klaro.yml';
        $defaults_from_klaro = [];

        if (file_exists($translationsFile)) {
            $allTranslations = Yaml::parseFile($translationsFile);

            if (isset($allTranslations[$language])) {
                $defaults_from_klaro = $allTranslations[$language];
                DB::alteration_message('Found ' . count($defaults_from_klaro) . ' translation entries for language: ' . $language, "info");
            } else {
                DB::alteration_message('No translation entries found for language: ' . $language, "info");
            }
        } else {
            DB::alteration_message('Translation file not found: ' . $translationsFile, "error");
        }

        if (count($defaults_from_klaro)) {
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
                    DB::alteration_message("Set {$siteConfigField} from Klaro: {$klaroValue}", "info");
                }
            }

            // Override with custom SilverStripe translations if available
            $customDefaults = [
                'ConsentNoticeOK' => _t('Kraftausdruck\KlaroCookie.ConsentNoticeOK', ''),
                'ConsentNoticeLearnMore' => _t('Kraftausdruck\KlaroCookie.ConsentNoticeLearnMore', ''),
                'ConsentNoticeTitle' => _t('Kraftausdruck\KlaroCookie.ConsentNoticeTitle', ''),
                'ConsentNoticeDescription' => _t('Kraftausdruck\KlaroCookie.ConsentNoticeDescription', ''),
                'ConsentModalTitle' => _t('Kraftausdruck\KlaroCookie.ConsentModalTitle', ''),
                'ConsentModalDescription' => _t('Kraftausdruck\KlaroCookie.ConsentModalDescription', ''),
                'ConsentModalPrivacyPolicyName' => _t('Kraftausdruck\KlaroCookie.ConsentModalPrivacyPolicyName', ''),
                'ConsentModalPrivacyPolicyText' => _t('Kraftausdruck\KlaroCookie.ConsentModalPrivacyPolicyText', ''),
                'AcceptAll' => _t('Kraftausdruck\KlaroCookie.AcceptAll', ''),
                'AcceptSelected' => _t('Kraftausdruck\KlaroCookie.AcceptSelected', ''),
                'Decline' => _t('Kraftausdruck\KlaroCookie.Decline', '')
            ];

            // Override Klaro values with custom translations if they exist
            foreach ($customDefaults as $field => $customValue) {
                if (!empty($customValue) && $customValue !== $field) {
                    $siteConfig->{$field} = $customValue;
                    $siteConfigNeedsWrite = true;
                    DB::alteration_message("Overrode {$field} with custom translation: {$customValue}", "info");
                }
            }

            // Write SiteConfig if any changes were made
            if ($siteConfigNeedsWrite) {
                $siteConfig->write();
                DB::alteration_message('Updated SiteConfig with Klaro defaults and custom translations', 'changed');
            } else {
                DB::alteration_message('No SiteConfig updates needed - all fields already have values', 'info');
            }
        }
    }
}
