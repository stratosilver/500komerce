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
            // DB Connexion
            // ------------------------------------------------------------------------------------------------
            try {
                if (DB_PORT != '') {
                    self::$db = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_DB . ';charset=utf8', DB_USER, DB_PASSWORD);
                } else {
                    self::$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DB . ';charset=utf8', DB_USER, DB_PASSWORD);
                }
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {
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
