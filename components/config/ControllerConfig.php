<?php
/**
 * Page Paramètres of the back office (/admin/config): name of the site and rate of the VAT,
 * saved in the table config. For the administrators only (classes/Auth.php).
 */

declare(strict_types=1);
namespace Apgenic\Config;


class ControllerConfig  extends \Apgenic\Classes\Controller {

    private ViewConfig $HTMLConfig;
    private ModelConfig $dataConfig;

    // Columns of the table config
    public static array $fieldsNames = array('id_config', 'key', 'value');

    /** Longest name of the site */
    const SITE_NAME_MAX = 100;


    /**
     * The settings are saved before the header of the page: the header shows the new name of the site
     * @return void
     */
    function render(){
        $this->HTMLConfig = new ViewConfig();
        $this->dataConfig = new ModelConfig();

        // One page: the form of the settings (editlist is the default task of the back office)
        $task = filter_var($_GET['task'] ?? 'edit', FILTER_SANITIZE_SPECIAL_CHARS);
        if($task != 'edit' && $task != 'editlist'){
            throw new \ErrorException(T_PAGE_NOT_FOUND, 404, E_ERROR);
        }

        $values = self::saved();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $values = $this->save();
        }

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLConfig->header(T_COMPONENT_CONFIG);
        }
        $this->HTMLConfig->edit($values, $this->message);
        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLConfig->footer();
        }
    }


    /**
     * The settings as they are saved, as displayed in the form
     * @return array
     */
    private static function saved():array{
        return array(
            ModelConfig::SITE_NAME => ModelConfig::siteName(),
            ModelConfig::TAX_RATE => self::rateText(ModelConfig::taxRate()),
            ModelConfig::DEFAULT_LANGUAGE => ModelConfig::defaultLanguage(),
        );
    }


    /**
     * Check and save the values of the form
     * @return array the values to display in the form: saved, or typed when they are not valid
     */
    private function save():array{
        $siteName = trim(strip_tags((string)($_POST[ModelConfig::SITE_NAME] ?? '')));
        $siteName = function_exists('mb_substr') ? mb_substr($siteName, 0, self::SITE_NAME_MAX) : substr($siteName, 0, self::SITE_NAME_MAX);
        $rateTyped = trim(str_replace(',', '.', (string)($_POST[ModelConfig::TAX_RATE] ?? '')));
        $language = (string)($_POST[ModelConfig::DEFAULT_LANGUAGE] ?? '');
        $values = array(ModelConfig::SITE_NAME => $siteName, ModelConfig::TAX_RATE => str_replace('.', ',', $rateTyped), ModelConfig::DEFAULT_LANGUAGE => $language);

        // Rate: a number from 0 to 100, 2 decimals at most (21, 6, 5,5...)
        $rateOk = preg_match('/^\d{1,3}(\.\d{1,2})?$/', $rateTyped) && (float)$rateTyped <= 100;

        if($siteName == ''){
            $this->message = array('type' => 'danger', 'text' => T_CONFIG_SITE_NAME_REQUIRED);
            return $values;
        }
        if(!$rateOk){
            $this->message = array('type' => 'danger', 'text' => T_CONFIG_TAX_RATE_INVALID);
            return $values;
        }
        // One of the languages of the site (LANGUAGES, translations.php)
        if(!isset(LANGUAGES[$language])){
            $this->message = array('type' => 'danger', 'text' => T_CONFIG_DEFAULT_LANGUAGE_INVALID);
            return $values;
        }

        $rate = self::rateText((float)$rateTyped, '.');
        if($this->dataConfig->set(ModelConfig::SITE_NAME, $siteName) && $this->dataConfig->set(ModelConfig::TAX_RATE, $rate)
            && $this->dataConfig->set(ModelConfig::DEFAULT_LANGUAGE, $language)){
            $this->message = array('type' => 'success', 'text' => T_MSG_SAVED);
        }
        else{
            $this->message = array('type' => 'danger', 'text' => T_CONFIG_NOT_SAVED);
        }
        return self::saved();
    }


    /**
     * Rate without the useless zeros: 21, 5,5
     * @return string
     */
    private static function rateText(float $rate, string $decimal = ','):string{
        return rtrim(rtrim(number_format($rate, 2, $decimal, ''), '0'), $decimal);
    }
}
