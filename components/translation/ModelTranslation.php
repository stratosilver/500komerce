<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\Translation;
/**
*	Manage elements of the table translation.
*/
class ModelTranslation extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    public $id_translation = '';
    public $text_key = '';
    public $lang = '';
    public $text = '';
    public $created_at = '';
    public $updated_at = '';
    public $deleted_at = null;


    /**
    * Constructor
    */
    public function __construct(){
        parent::__construct();
    }


        /**
    * Add a row in translation.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `translation`  ( text_key, lang, text)
                    VALUES (
                    :text_key,
                    :lang,
                    :text
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':text_key' => $this->text_key, ':lang' => $this->lang, ':text' => $this->text,))){
            $this->id_translation = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in translation.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_translation > 0){
            $query = "  UPDATE `translation` SET
                    `text_key` = :text_key,
                    `lang` = :lang,
                    `text` = :text
            WHERE  `id_translation` = :id_translation  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':text_key' => $this->text_key, ':lang' => $this->lang, ':text' => $this->text, ':id_translation' => $this->id_translation))){
                return (1);
            }
            else{
                //var_dump($q->errorInfo());
                //$q->debugDumpParams();
                return(0);
            }
        }
        // the element don't exists
        else{
            return($this->add());
        }


    }


    /**
    * Delete a row in translation.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `translation` WHERE  `id_translation` = :id_translation ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_translation' => $this->id_translation))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


        /**
    * Logical delete a row in translation.
    * @return int 0 or 1
    */
    public function logicalDel():int{

                $query = "UPDATE `translation` SET deleted_at = NOW() WHERE  `id_translation` = :id_translation ";
                    $q = self::$db->prepare($query);

        if($q->execute(array(':id_translation' => $this->id_translation))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    /**
    * Logical undelete a row in translation.
    * @return int 0 or 1
    */
    public function logicalUnDel():int{

        $query = "UPDATE `translation` SET deleted_at = :deleted WHERE  `id_translation` = :id_translation  ";
        $q = self::$db->prepare($query);

        if($q->execute(array(':id_translation' => $this->id_translation, ':deleted' => NULL))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    
    /**
    * Get a row in translation.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT t.`id_translation`, t.`text_key`, t.`lang`, t.`text`, t.`created_at`, t.`updated_at`, t.`deleted_at`
                                        FROM `translation` AS t 
                     

                    WHERE  `t`.`id_translation` = :id_translation  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_translation' => $this->id_translation))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_translation = $row['id_translation'];
                $this->text_key = $row['text_key'];
                $this->lang = $row['lang'];
                $this->text = $row['text'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                $this->deleted_at = $row['deleted_at'];

                return(1);
            }
            else{
                return(0);
            }
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Get a list of row from translation.
    * @param Int limitFrom
    * @param Int limitNumber
    * @param string orderBy
    * @param string order
    * @return int 0 or Number of elements
    */
    public function getList(int $limitFrom=null, int $limitNumber=null, array $filters=array(),  string $orderBy='', string $order='ASC', $textSearch = ''):int{

        $query = "  SELECT t.`id_translation`, t.`text_key`, t.`lang`, t.`text`, t.`created_at`, t.`updated_at`, t.`deleted_at`
                    FROM `translation` AS t                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
            $where .= "
                AND(
                    text_key LIKE :text_search 
                    OR lang LIKE :text_search 
                    OR text LIKE :text_search )
            ";
        }


        if(isset($filters['deleted_at']) && $filters['deleted_at'] == 0){
            $where .= " AND (deleted_at = 0 OR deleted_at IS NULL)";
        }
        elseif(isset($filters['deleted_at']) && $filters['deleted_at'] == 1){
            $where .= " AND deleted_at > 0 ";
            }
    
        foreach($filters as $field=>$value){
            if($field != ''  && $value !='' && $field !='deleted_at') {
                $where .= " AND $field = :$field ";
            }
        }
        $query .= $where;

        // Set order
        if($order != 'ASC') $order = 'DESC';
        if($orderBy){
            $query .= " ORDER BY $orderBy $order ";
        }

        // Set limits
        if($limitNumber){
            $query .= " LIMIT :limitFrom, :limitNumber ";
        }


        $q = self::$db->prepare($query);

        // Bind search
        if($textSearch != ''){
                        $q->bindValue(':text_search', '%'.$textSearch.'%');
        }

        // Bind all filters
        foreach($filters as $field=>$value){
            if($field !='' && $value !='' && $field !='deleted_at') {
                $q->bindValue(":$field", $value);
            }
        }

        // Bind limits
        if($limitNumber){
            $q->bindValue(':limitFrom', intval($limitFrom), \PDO::PARAM_INT);
            $q->bindValue(':limitNumber', intval($limitNumber), \PDO::PARAM_INT);
        }


        if($q->execute()){
            $this->list = array();
                        while($row = $q->fetch(\PDO::FETCH_ASSOC)){
               $this->list[$row['id_translation']]['id_translation'] = $row['id_translation'];
               $this->list[$row['id_translation']]['text_key'] = $row['text_key'];
               $this->list[$row['id_translation']]['lang'] = $row['lang'];
               $this->list[$row['id_translation']]['text'] = $row['text'];
               $this->list[$row['id_translation']]['created_at'] = $row['created_at'];
               $this->list[$row['id_translation']]['updated_at'] = $row['updated_at'];
               $this->list[$row['id_translation']]['deleted_at'] = $row['deleted_at'];
                                }
            return($this->count($where, $filters, $textSearch));
        }
       else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    



    /**
    * Count total rows found after getList
    * @return int 0 or Number of elements
    */
    public function count(string $where, $filters = [], string $textSearch):int{

        $query = "SELECT COUNT(*) AS nbRows
                  FROM `translation`
                  $where";

        $q = self::$db->prepare($query);

        // Bind all filters
        foreach($filters as $field=>$value){
            if($field !='' && $value !='' && $field !='deleted_at') {
                $q->bindValue(":$field", $value);
            }
        }

        // Bind search
        if($textSearch != ''){
                $q->bindValue(':text_search', '%'.$textSearch.'%');
            }

        if($q->execute()){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                return($row['nbRows']);
            }
            else{
                return(0);
            }
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }
}
    