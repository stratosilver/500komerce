<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\Category;
/**
*	Manage elements of the table category.
*/
class ModelCategory extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    public $id_category = '';
    public $id_parent = null;
    public $name = '';
    public $slug = '';
    public $position = '';
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
    * Add a row in category.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `category`  ( id_parent, name, slug, position)
                    VALUES (
                    :id_parent,
                    :name,
                    :slug,
                    :position
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':id_parent' => $this->id_parent, ':name' => $this->name, ':slug' => $this->slug, ':position' => $this->position,))){
            $this->id_category = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in category.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_category > 0){
            $query = "  UPDATE `category` SET
                    `id_parent` = :id_parent,
                    `name` = :name,
                    `slug` = :slug,
                    `position` = :position
            WHERE  `id_category` = :id_category  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':id_parent' => $this->id_parent, ':name' => $this->name, ':slug' => $this->slug, ':position' => $this->position, ':id_category' => $this->id_category))){
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
    * Delete a row in category.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `category` WHERE  `id_category` = :id_category ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_category' => $this->id_category))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


        /**
    * Logical delete a row in category.
    * @return int 0 or 1
    */
    public function logicalDel():int{

                $query = "UPDATE `category` SET deleted_at = NOW() WHERE  `id_category` = :id_category ";
                    $q = self::$db->prepare($query);

        if($q->execute(array(':id_category' => $this->id_category))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    /**
    * Logical undelete a row in category.
    * @return int 0 or 1
    */
    public function logicalUnDel():int{

        $query = "UPDATE `category` SET deleted_at = :deleted WHERE  `id_category` = :id_category  ";
        $q = self::$db->prepare($query);

        if($q->execute(array(':id_category' => $this->id_category, ':deleted' => NULL))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    
    /**
    * Get a row in category.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT c.`id_category`, c.`id_parent`, c.`name`, c.`slug`, c.`position`, c.`created_at`, c.`updated_at`, c.`deleted_at`
                    , ca.name AS ca_name, ca.id_category AS ca_id_category
                                        FROM `category` AS c 
                    LEFT JOIN `category` ca ON c.id_parent = ca.id_category
                     

                    WHERE  `c`.`id_category` = :id_category  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_category' => $this->id_category))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_category = $row['id_category'];
                $this->id_parent = $row['id_parent'];
                $this->name = $row['name'];
                $this->slug = $row['slug'];
                $this->position = $row['position'];
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
    * Get a list of row from category.
    * @param Int limitFrom
    * @param Int limitNumber
    * @param string orderBy
    * @param string order
    * @return int 0 or Number of elements
    */
    public function getList(int $limitFrom=null, int $limitNumber=null, array $filters=array(),  string $orderBy='', string $order='ASC', $textSearch = ''):int{

        $query = "  SELECT c.`id_category`, c.`id_parent`, c.`name`, c.`slug`, c.`position`, c.`created_at`, c.`updated_at`, c.`deleted_at`
                    FROM `category` AS c                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
            $where .= "
                AND(
                    name LIKE :text_search 
                    OR slug LIKE :text_search )
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
               $this->list[$row['id_category']]['id_category'] = $row['id_category'];
               $this->list[$row['id_category']]['id_parent'] = $row['id_parent'];
               $this->list[$row['id_category']]['name'] = $row['name'];
               $this->list[$row['id_category']]['slug'] = $row['slug'];
               $this->list[$row['id_category']]['position'] = $row['position'];
               $this->list[$row['id_category']]['created_at'] = $row['created_at'];
               $this->list[$row['id_category']]['updated_at'] = $row['updated_at'];
               $this->list[$row['id_category']]['deleted_at'] = $row['deleted_at'];
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
                  FROM `category`
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
    