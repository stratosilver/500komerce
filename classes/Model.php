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
}
