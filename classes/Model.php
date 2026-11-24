<?php
/**
 * Created by PhpStorm.
 * User: TILMED199
 * Date: 15-02-22
 * Time: 13:22
 * Project: exsilicon.com
 */
namespace Apgenic\Classes;
use \pdo;

class Model{
    static $db;

    function __construct(){
        if(!isset(self::$db)){
            // DB Connexion: SQLite database file (DB_FILE in config.php)
            // ------------------------------------------------------------------------------------------------
            $file = defined('DB_FILE') ? DB_FILE : dirname(__DIR__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'500komerce.sqlite';
            try {
                if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
                    throw new \RuntimeException('the PHP extension pdo_sqlite is not enabled');
                }
                // PDO creates an empty database when the file does not exist: the file must be there
                if (!is_file($file)) {
                    throw new \RuntimeException('database file not found: '.$file);
                }
                $db = new PDO('sqlite:' . $file, null, null, array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // Seconds to wait when another request is writing in the database
                    PDO::ATTR_TIMEOUT => 5,
                ));
                // SQLite checks the foreign keys (ON DELETE CASCADE / SET NULL) only when asked, MySQL always does
                $db->exec('PRAGMA foreign_keys = ON');
                self::$db = $db;
            } catch (\Throwable $e) {
                error_log('DB Connexion failed: '.$e->getMessage());
                echo '<h1>DB Connexion failed</h1>';
            }
        }
    }


    /**
     * Keep only the filters made on a column of the table.
     * PDO can bind a value but not the name of a column: the name is written in the query, so it is
     * accepted only if it is one of the columns of the table (COLUMNS of the model). The value is bound.
     * @param array $filters column => value, ex: the field_search / field_search_value of the search form
     * @return array
     */
    protected function safeFilters(array $filters):array{
        $safe = array();
        foreach($filters as $field => $value){
            if(is_string($field) && in_array($field, static::COLUMNS, true) && ($value === null || is_scalar($value))){
                $safe[$field] = $value;
            }
        }
        return $safe;
    }


    /**
     * Sort column written in the query: only a column of the table is accepted
     * @return string the column, or '' for no sort
     */
    protected function safeOrderBy(string $orderBy):string{
        $orderBy = trim($orderBy, " `");
        return in_array($orderBy, static::COLUMNS, true) ? $orderBy : '';
    }
}
