<?php
/**
 * Settings of the site edited in the back office (/admin/config): table config, one row by setting (key, value).
 * The table is read once by request. A setting that is not in the table, or a database without the table
 * (_dev/config_stock_price_ttc.sql not run yet), gives the default value: the constants of config.php.
 */

declare(strict_types=1);
namespace Apgenic\Config;

class ModelConfig extends \Apgenic\Classes\Model{

    /** Settings of the page Paramètres */
    const SITE_NAME = 'site_name';
    const TAX_RATE = 'tax_rate';
    const DEFAULT_LANGUAGE = 'default_language';

    /** key => value of the table config, null until the first read */
    private static ?array $values = null;


    public function __construct(){
        parent::__construct();
    }


    /**
     * All the settings of the table
     * @return array key => value
     */
    public static function all():array{
        if(self::$values === null){
            self::$values = array();
            // Connection to the database, if no model has done it yet
            new self();
            try{
                if(isset(self::$db)){
                    foreach(self::$db->query("SELECT `key`, `value` FROM `config`")->fetchAll(\PDO::FETCH_ASSOC) as $row){
                        self::$values[(string)$row['key']] = (string)$row['value'];
                    }
                }
            }
            catch(\Throwable $e){
                error_log('Config: '.$e->getMessage());
            }
        }
        return self::$values;
    }


    /**
     * Value of a setting
     * @param string $default value when the setting is not in the table or is empty
     * @return string
     */
    public static function get(string $key, string $default = ''):string{
        $values = self::all();
        return isset($values[$key]) && $values[$key] !== '' ? $values[$key] : $default;
    }


    /**
     * Name of the site, not escaped (APP_NAME of config.php by default)
     * @return string
     */
    public static function siteName():string{
        return self::get(self::SITE_NAME, defined('APP_NAME') ? (string)APP_NAME : '100Komerce');
    }


    /**
     * Rate of the VAT, in percent (TAX_RATE of config.php by default)
     * @return float
     */
    public static function taxRate():float{
        $rate = self::get(self::TAX_RATE, defined('TAX_RATE') ? (string)TAX_RATE : '21');
        return is_numeric($rate) ? (float)$rate : 21.0;
    }


    /**
     * Default language of the site: the one of the visitors who have not chosen one (switch FR | EN).
     * A code of LANGUAGES (translations.php); the first of LANGUAGES when the setting is missing or not valid.
     * Called by translations.php, before the texts are defined: no T_ constant here.
     * @return string
     */
    public static function defaultLanguage():string{
        $languages = defined('LANGUAGES') ? LANGUAGES : array('fr' => 'Français');
        $lang = self::get(self::DEFAULT_LANGUAGE, '');
        return isset($languages[$lang]) ? $lang : (string)array_key_first($languages);
    }


    /**
     * Save a setting: the row of the key is updated, or added when it is not in the table
     * @return int 0 or 1
     */
    public function set(string $key, string $value):int{
        try{
            $q = self::$db->prepare("UPDATE `config` SET `value` = :value WHERE `key` = :key");
            $q->execute(array(':value' => $value, ':key' => $key));
            if($q->rowCount() == 0){
                $q = self::$db->prepare("INSERT INTO `config` (`key`, `value`) VALUES (:key, :value)");
                $q->execute(array(':key' => $key, ':value' => $value));
            }
        }
        catch(\Throwable $e){
            error_log('Config: '.$e->getMessage());
            return 0;
        }
        // Read again at the next get()
        self::$values = null;
        return 1;
    }
}
