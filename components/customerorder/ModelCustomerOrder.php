<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\CustomerOrder;
/**
*	Manage elements of the table customer_order.
*/
class ModelCustomerOrder extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    // Columns of the table: the only names accepted as filter and as sort field
    const COLUMNS = array('id_customer_order', 'id_user', 'created_at');

    public $id_customer_order = '';
    public $id_user = '';
    public $created_at = '';


    /**
    * Constructor
    */
    public function __construct(){
        parent::__construct();
    }


        /**
    * Add a row in customer_order.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `customer_order`  ( id_user)
                    VALUES (
                    :id_user
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':id_user' => $this->id_user,))){
            $this->id_customer_order = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in customer_order.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_customer_order > 0){
            $query = "  UPDATE `customer_order` SET
                    `id_user` = :id_user
            WHERE  `id_customer_order` = :id_customer_order  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':id_user' => $this->id_user, ':id_customer_order' => $this->id_customer_order))){
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
    * Delete a row in customer_order.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `customer_order` WHERE  `id_customer_order` = :id_customer_order ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_customer_order' => $this->id_customer_order))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    
    /**
    * Get a row in customer_order.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT c.`id_customer_order`, c.`id_user`, c.`created_at`
                    , p.name AS p_name, p.id_user AS p_id_user
                                        FROM `customer_order` AS c 
                    LEFT JOIN `product` p ON c.id_user = p.id_user
                     

                    WHERE  `c`.`id_customer_order` = :id_customer_order  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_customer_order' => $this->id_customer_order))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_customer_order = $row['id_customer_order'];
                $this->id_user = $row['id_user'];
                $this->created_at = $row['created_at'];

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
    * Get a list of row from customer_order.
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

        $query = "  SELECT c.`id_customer_order`, c.`id_user`, c.`created_at`
                    FROM `customer_order` AS c                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
            $where .= "
                AND(
                    id_user LIKE :text_search )
            ";
        }


    
        foreach($filters as $field=>$value){
            if($field != ''  && $value !='' ) {
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
                        $q->bindValue(':text_search', '%'.$textSearch.'%');
        }

        // Bind all filters
        foreach($filters as $field=>$value){
            if($field !='' && $value !='' ) {
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
               $this->list[$row['id_customer_order']]['id_customer_order'] = $row['id_customer_order'];
               $this->list[$row['id_customer_order']]['id_user'] = $row['id_user'];
               $this->list[$row['id_customer_order']]['created_at'] = $row['created_at'];
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
                  FROM `customer_order`
                  $where";

        $q = self::$db->prepare($query);

        // Bind all filters
        foreach($filters as $field=>$value){
            if($field !='' && $value !='' ) {
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
    