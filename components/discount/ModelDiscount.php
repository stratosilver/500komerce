<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\Discount;
/**
*	Manage elements of the table discount.
*/
class ModelDiscount extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    // Columns of the table: the only names accepted as filter and as sort field
    const COLUMNS = array('id_discount', 'date_from', 'date_to', 'id_category', 'id_product', 'amount', 'percentage', 'created_at', 'updated_at', 'deleted_at');

    public $id_discount = '';
    public $date_from = null;
    public $date_to = null;
    public $id_category = null;
    public $id_product = null;
    public $amount = null;
    public $percentage = null;
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
    * Add a row in discount.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `discount`  ( date_from, date_to, id_category, id_product, amount, percentage)
                    VALUES (
                    :date_from,
                    :date_to,
                    :id_category,
                    :id_product,
                    :amount,
                    :percentage
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':date_from' => $this->date_from, ':date_to' => $this->date_to, ':id_category' => $this->id_category, ':id_product' => $this->id_product, ':amount' => $this->amount, ':percentage' => $this->percentage,))){
            $this->id_discount = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in discount.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_discount > 0){
            $query = "  UPDATE `discount` SET
                    `date_from` = :date_from,
                    `date_to` = :date_to,
                    `id_category` = :id_category,
                    `id_product` = :id_product,
                    `amount` = :amount,
                    `percentage` = :percentage
            WHERE  `id_discount` = :id_discount  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':date_from' => $this->date_from, ':date_to' => $this->date_to, ':id_category' => $this->id_category, ':id_product' => $this->id_product, ':amount' => $this->amount, ':percentage' => $this->percentage, ':id_discount' => $this->id_discount))){
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
    * Delete a row in discount.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `discount` WHERE  `id_discount` = :id_discount ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_discount' => $this->id_discount))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


        /**
    * Logical delete a row in discount.
    * @return int 0 or 1
    */
    public function logicalDel():int{

                $query = "UPDATE `discount` SET deleted_at = NOW() WHERE  `id_discount` = :id_discount ";
                    $q = self::$db->prepare($query);

        if($q->execute(array(':id_discount' => $this->id_discount))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    /**
    * Logical undelete a row in discount.
    * @return int 0 or 1
    */
    public function logicalUnDel():int{

        $query = "UPDATE `discount` SET deleted_at = :deleted WHERE  `id_discount` = :id_discount  ";
        $q = self::$db->prepare($query);

        if($q->execute(array(':id_discount' => $this->id_discount, ':deleted' => NULL))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    
    /**
    * Get a row in discount.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT d.`id_discount`, d.`date_from`, d.`date_to`, d.`id_category`, d.`id_product`, d.`amount`, d.`percentage`, d.`created_at`, d.`updated_at`, d.`deleted_at`
                                        FROM `discount` AS d 
                     

                    WHERE  `d`.`id_discount` = :id_discount  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_discount' => $this->id_discount))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_discount = $row['id_discount'];
                $this->date_from = $row['date_from'];
                $this->date_to = $row['date_to'];
                $this->id_category = $row['id_category'];
                $this->id_product = $row['id_product'];
                $this->amount = $row['amount'];
                $this->percentage = $row['percentage'];
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
    * Get a list of row from discount.
    * @param Int limitFrom
    * @param Int limitNumber
    * @param string orderBy
    * @param string order
    * @return int 0 or Number of elements
    */
    public function getList(int $limitFrom=null, int $limitNumber=null, array $filters=array(),  string $orderBy='', string $order='ASC', $textSearch = ''):int{

        // The names of columns cannot be bound: they are checked, the values are bound
        $filters = $this->safeFilters($filters);
        $orderBy = $this->safeOrderBy($orderBy);

        $query = "  SELECT d.`id_discount`, d.`date_from`, d.`date_to`, d.`id_category`, d.`id_product`, d.`amount`, d.`percentage`, d.`created_at`, d.`updated_at`, d.`deleted_at`
                    FROM `discount` AS d                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
        }


        if(isset($filters['deleted_at']) && $filters['deleted_at'] == 0){
            $where .= " AND (deleted_at = 0 OR deleted_at IS NULL)";
        }
        elseif(isset($filters['deleted_at']) && $filters['deleted_at'] == 1){
            $where .= " AND deleted_at > 0 ";
            }
    
        foreach($filters as $field=>$value){
            if($field != ''  && $value !='' && $field !='deleted_at') {
                $where .= " AND `$field` = :$field ";
            }
        }
        $query .= $where;

        // Set order
        if($order != 'ASC') $order = 'DESC';
        if($orderBy){
            $query .= " ORDER BY `$orderBy` $order ";
        }

        // Set limits
        if($limitNumber){
            $query .= " LIMIT :limitFrom, :limitNumber ";
        }


        $q = self::$db->prepare($query);

        // Bind search
        if($textSearch != ''){
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
               $this->list[$row['id_discount']]['id_discount'] = $row['id_discount'];
               $this->list[$row['id_discount']]['date_from'] = $row['date_from'];
               $this->list[$row['id_discount']]['date_to'] = $row['date_to'];
               $this->list[$row['id_discount']]['id_category'] = $row['id_category'];
               $this->list[$row['id_discount']]['id_product'] = $row['id_product'];
               $this->list[$row['id_discount']]['amount'] = $row['amount'];
               $this->list[$row['id_discount']]['percentage'] = $row['percentage'];
               $this->list[$row['id_discount']]['created_at'] = $row['created_at'];
               $this->list[$row['id_discount']]['updated_at'] = $row['updated_at'];
               $this->list[$row['id_discount']]['deleted_at'] = $row['deleted_at'];
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

        $filters = $this->safeFilters($filters);

        $query = "SELECT COUNT(*) AS nbRows
                  FROM `discount`
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
    