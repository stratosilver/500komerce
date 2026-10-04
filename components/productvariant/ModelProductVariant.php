<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\ProductVariant;
/**
*	Manage elements of the table product_variant.
*/
class ModelProductVariant extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    public $id_product_variant = '';
    public $name = '';
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
    * Add a row in product_variant.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `product_variant`  ( name)
                    VALUES (
                    :name
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':name' => $this->name,))){
            $this->id_product_variant = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in product_variant.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_product_variant > 0){
            $query = "  UPDATE `product_variant` SET
                    `name` = :name
            WHERE  `id_product_variant` = :id_product_variant  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':name' => $this->name, ':id_product_variant' => $this->id_product_variant))){
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
    * Delete a row in product_variant.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `product_variant` WHERE  `id_product_variant` = :id_product_variant ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_product_variant' => $this->id_product_variant))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


        /**
    * Logical delete a row in product_variant.
    * @return int 0 or 1
    */
    public function logicalDel():int{

                $query = "UPDATE `product_variant` SET deleted_at = NOW() WHERE  `id_product_variant` = :id_product_variant ";
                    $q = self::$db->prepare($query);

        if($q->execute(array(':id_product_variant' => $this->id_product_variant))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    /**
    * Logical undelete a row in product_variant.
    * @return int 0 or 1
    */
    public function logicalUnDel():int{

        $query = "UPDATE `product_variant` SET deleted_at = :deleted WHERE  `id_product_variant` = :id_product_variant  ";
        $q = self::$db->prepare($query);

        if($q->execute(array(':id_product_variant' => $this->id_product_variant, ':deleted' => NULL))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    
    /**
    * Get a row in product_variant.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT p.`id_product_variant`, p.`name`, p.`created_at`, p.`updated_at`, p.`deleted_at`
                                        FROM `product_variant` AS p 
                     

                    WHERE  `p`.`id_product_variant` = :id_product_variant  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_product_variant' => $this->id_product_variant))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_product_variant = $row['id_product_variant'];
                $this->name = $row['name'];
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
    * Get a list of row from product_variant.
    * @param Int limitFrom
    * @param Int limitNumber
    * @param string orderBy
    * @param string order
    * @return int 0 or Number of elements
    */
    public function getList(int $limitFrom=null, int $limitNumber=null, array $filters=array(),  string $orderBy='', string $order='ASC', $textSearch = ''):int{

        $query = "  SELECT p.`id_product_variant`, p.`name`, p.`created_at`, p.`updated_at`, p.`deleted_at`
                    FROM `product_variant` AS p                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
            $where .= "
                AND(
                    name LIKE :text_search )
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
               $this->list[$row['id_product_variant']]['id_product_variant'] = $row['id_product_variant'];
               $this->list[$row['id_product_variant']]['name'] = $row['name'];
               $this->list[$row['id_product_variant']]['created_at'] = $row['created_at'];
               $this->list[$row['id_product_variant']]['updated_at'] = $row['updated_at'];
               $this->list[$row['id_product_variant']]['deleted_at'] = $row['deleted_at'];
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
                  FROM `product_variant`
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
    