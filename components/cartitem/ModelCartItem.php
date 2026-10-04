<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\CartItem;
/**
*	Manage elements of the table cart_item.
*/
class ModelCartItem extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    public $id_cart_item = '';
    public $id_user = '';
    public $cookie_id = null;
    public $id_product = '';
    public $id_product_variant = '';
    public $quantity = '';
    public $created_at = '';
    public $updated_at = '';


    /**
    * Constructor
    */
    public function __construct(){
        parent::__construct();
    }


        /**
    * Add a row in cart_item.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `cart_item`  ( id_cart_item, id_user, cookie_id, quantity, created_at, updated_at)
                    VALUES (
                    :id_cart_item,
                    :id_user,
                    :cookie_id,
                    :quantity,
                    :created_at,
                    :updated_at
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':id_cart_item' => $this->id_cart_item, ':id_user' => $this->id_user, ':cookie_id' => $this->cookie_id, ':quantity' => $this->quantity, ':created_at' => $this->created_at, ':updated_at' => $this->updated_at,))){
            $this->id_cart_item = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in cart_item.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_cart_item > 0){
            $query = "  UPDATE `cart_item` SET
                    `id_cart_item` = :id_cart_item,
                    `id_user` = :id_user,
                    `cookie_id` = :cookie_id,
                    `quantity` = :quantity,
                    `created_at` = :created_at,
                    `updated_at` = :updated_at
            WHERE  `id_cart_item` = :id_cart_item  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':id_cart_item' => $this->id_cart_item, ':id_user' => $this->id_user, ':cookie_id' => $this->cookie_id, ':quantity' => $this->quantity, ':created_at' => $this->created_at, ':updated_at' => $this->updated_at, ':id_cart_item' => $this->id_cart_item))){
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
    * Delete a row in cart_item.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `cart_item` WHERE  `id_cart_item` = :id_cart_item ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_cart_item' => $this->id_cart_item))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    
    /**
    * Get a row in cart_item.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT c.`id_cart_item`, c.`id_user`, c.`cookie_id`, c.`id_product`, c.`id_product_variant`, c.`quantity`, c.`created_at`, c.`updated_at`
                    , p.name AS p_name, p.id_product_variant AS p_id_product_variant
                    , u.email AS u_email, u.id_user AS u_id_user
                    , pr.name AS pr_name, pr.id_product AS pr_id_product
                                        FROM `cart_item` AS c 
                    LEFT JOIN `product_variant` p ON c.id_product_variant = p.id_product_variant
                    LEFT JOIN `user` u ON c.id_user = u.id_user
                    LEFT JOIN `product` pr ON c.id_product_variant = pr.id_product
                     

                    WHERE  `c`.`id_cart_item` = :id_cart_item  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_cart_item' => $this->id_cart_item))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_cart_item = $row['id_cart_item'];
                $this->id_user = $row['id_user'];
                $this->cookie_id = $row['cookie_id'];
                $this->id_product = $row['id_product'];
                $this->id_product_variant = $row['id_product_variant'];
                $this->quantity = $row['quantity'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];

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
    * Get a list of row from cart_item.
    * @param Int limitFrom
    * @param Int limitNumber
    * @param string orderBy
    * @param string order
    * @return int 0 or Number of elements
    */
    public function getList(int $limitFrom=null, int $limitNumber=null, array $filters=array(),  string $orderBy='', string $order='ASC', $textSearch = ''):int{

        $query = "  SELECT c.`id_cart_item`, c.`id_user`, c.`cookie_id`, c.`id_product`, c.`id_product_variant`, c.`quantity`, c.`created_at`, c.`updated_at`
                    FROM `cart_item` AS c                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
            $where .= "
                AND(
                    cookie_id LIKE :text_search )
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
               $this->list[$row['id_cart_item']]['id_cart_item'] = $row['id_cart_item'];
               $this->list[$row['id_cart_item']]['id_user'] = $row['id_user'];
               $this->list[$row['id_cart_item']]['cookie_id'] = $row['cookie_id'];
               $this->list[$row['id_cart_item']]['id_product'] = $row['id_product'];
               $this->list[$row['id_cart_item']]['id_product_variant'] = $row['id_product_variant'];
               $this->list[$row['id_cart_item']]['quantity'] = $row['quantity'];
               $this->list[$row['id_cart_item']]['created_at'] = $row['created_at'];
               $this->list[$row['id_cart_item']]['updated_at'] = $row['updated_at'];
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
                  FROM `cart_item`
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
    