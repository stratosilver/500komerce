<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\CustomerOrderItem;
/**
*	Manage elements of the table customer_order_item.
*/
class ModelCustomerOrderItem extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    public $id_customer_order_item = '';
    public $id_customer_order = '';
    public $id_product_variant = null;
    public $product_name = '';
    public $sku = '';
    public $quantity = '';
    public $unit_price_amount = '';
    public $discount_amount = '';
    public $tax_amount = '';
    public $line_total_amount = '';
    public $created_at = '';


    /**
    * Constructor
    */
    public function __construct(){
        parent::__construct();
    }


        /**
    * Add a row in customer_order_item.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `customer_order_item`  ( id_customer_order, id_product_variant, product_name, sku, quantity, unit_price_amount, discount_amount, tax_amount, line_total_amount)
                    VALUES (
                    :id_customer_order,
                    :id_product_variant,
                    :product_name,
                    :sku,
                    :quantity,
                    :unit_price_amount,
                    :discount_amount,
                    :tax_amount,
                    :line_total_amount
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':id_customer_order' => $this->id_customer_order, ':id_product_variant' => $this->id_product_variant, ':product_name' => $this->product_name, ':sku' => $this->sku, ':quantity' => $this->quantity, ':unit_price_amount' => $this->unit_price_amount, ':discount_amount' => $this->discount_amount, ':tax_amount' => $this->tax_amount, ':line_total_amount' => $this->line_total_amount,))){
            $this->id_customer_order_item = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in customer_order_item.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_customer_order_item > 0){
            $query = "  UPDATE `customer_order_item` SET
                    `id_customer_order` = :id_customer_order,
                    `id_product_variant` = :id_product_variant,
                    `product_name` = :product_name,
                    `sku` = :sku,
                    `quantity` = :quantity,
                    `unit_price_amount` = :unit_price_amount,
                    `discount_amount` = :discount_amount,
                    `tax_amount` = :tax_amount,
                    `line_total_amount` = :line_total_amount
            WHERE  `id_customer_order_item` = :id_customer_order_item  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':id_customer_order' => $this->id_customer_order, ':id_product_variant' => $this->id_product_variant, ':product_name' => $this->product_name, ':sku' => $this->sku, ':quantity' => $this->quantity, ':unit_price_amount' => $this->unit_price_amount, ':discount_amount' => $this->discount_amount, ':tax_amount' => $this->tax_amount, ':line_total_amount' => $this->line_total_amount, ':id_customer_order_item' => $this->id_customer_order_item))){
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
    * Delete a row in customer_order_item.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `customer_order_item` WHERE  `id_customer_order_item` = :id_customer_order_item ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_customer_order_item' => $this->id_customer_order_item))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    
    /**
    * Get a row in customer_order_item.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT c.`id_customer_order_item`, c.`id_customer_order`, c.`id_product_variant`, c.`product_name`, c.`sku`, c.`quantity`, c.`unit_price_amount`, c.`discount_amount`, c.`tax_amount`, c.`line_total_amount`, c.`created_at`
                    , cu.id_user AS cu_id_user, cu.id_customer_order AS cu_id_customer_order
                    , p.name AS p_name, p.id_product_variant AS p_id_product_variant
                                        FROM `customer_order_item` AS c 
                    LEFT JOIN `customer_order` cu ON c.id_customer_order = cu.id_customer_order
                    LEFT JOIN `product_variant` p ON c.id_product_variant = p.id_product_variant
                     

                    WHERE  `c`.`id_customer_order_item` = :id_customer_order_item  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_customer_order_item' => $this->id_customer_order_item))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_customer_order_item = $row['id_customer_order_item'];
                $this->id_customer_order = $row['id_customer_order'];
                $this->id_product_variant = $row['id_product_variant'];
                $this->product_name = $row['product_name'];
                $this->sku = $row['sku'];
                $this->quantity = $row['quantity'];
                $this->unit_price_amount = $row['unit_price_amount'];
                $this->discount_amount = $row['discount_amount'];
                $this->tax_amount = $row['tax_amount'];
                $this->line_total_amount = $row['line_total_amount'];
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
    * Get a list of row from customer_order_item.
    * @param Int limitFrom
    * @param Int limitNumber
    * @param string orderBy
    * @param string order
    * @return int 0 or Number of elements
    */
    public function getList(int $limitFrom=null, int $limitNumber=null, array $filters=array(),  string $orderBy='', string $order='ASC', $textSearch = ''):int{

        $query = "  SELECT c.`id_customer_order_item`, c.`id_customer_order`, c.`id_product_variant`, c.`product_name`, c.`sku`, c.`quantity`, c.`unit_price_amount`, c.`discount_amount`, c.`tax_amount`, c.`line_total_amount`, c.`created_at`
                    FROM `customer_order_item` AS c                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
            $where .= "
                AND(
                    product_name LIKE :text_search 
                    OR sku LIKE :text_search )
            ";
        }


    
        foreach($filters as $field=>$value){
            if($field != ''  && $value !='' ) {
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
               $this->list[$row['id_customer_order_item']]['id_customer_order_item'] = $row['id_customer_order_item'];
               $this->list[$row['id_customer_order_item']]['id_customer_order'] = $row['id_customer_order'];
               $this->list[$row['id_customer_order_item']]['id_product_variant'] = $row['id_product_variant'];
               $this->list[$row['id_customer_order_item']]['product_name'] = $row['product_name'];
               $this->list[$row['id_customer_order_item']]['sku'] = $row['sku'];
               $this->list[$row['id_customer_order_item']]['quantity'] = $row['quantity'];
               $this->list[$row['id_customer_order_item']]['unit_price_amount'] = $row['unit_price_amount'];
               $this->list[$row['id_customer_order_item']]['discount_amount'] = $row['discount_amount'];
               $this->list[$row['id_customer_order_item']]['tax_amount'] = $row['tax_amount'];
               $this->list[$row['id_customer_order_item']]['line_total_amount'] = $row['line_total_amount'];
               $this->list[$row['id_customer_order_item']]['created_at'] = $row['created_at'];
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
                  FROM `customer_order_item`
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
    