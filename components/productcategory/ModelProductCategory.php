<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\ProductCategory;
/**
*	Manage elements of the table product_category.
*/
class ModelProductCategory extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    // Columns of the table: the only names accepted as filter and as sort field
    const COLUMNS = array('id_product', 'id_category');

    public $id_product = '';
    public $id_category = '';


    /**
    * Constructor
    */
    public function __construct(){
        parent::__construct();
    }


        /**
    * Add a row in product_category.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `product_category`  ( id_product, id_category)
                    VALUES (
                    :id_product,
                    :id_category
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':id_product' => $this->id_product, ':id_category' => $this->id_category,))){
            $this->id_product = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in product_category.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_product > 0 && $this->id_category > 0){
            $query = "  UPDATE `product_category` SET
                    `id_product` = :id_product,
                    `id_category` = :id_category
            WHERE  `id_product` = :id_product  AND  `id_category` = :id_category  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':id_product' => $this->id_product, ':id_category' => $this->id_category, ':id_product' => $this->id_product, ':id_category' => $this->id_category))){
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
    * Delete a row in product_category.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `product_category` WHERE  `id_product` = :id_product  AND  `id_category` = :id_category ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_product' => $this->id_product, ':id_category' => $this->id_category))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    
    /**
    * Get a row in product_category.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT p.`id_product`, p.`id_category`
                    , pr.name AS pr_name, pr.id_product AS pr_id_product
                    , c.name AS c_name, c.id_category AS c_id_category
                                        FROM `product_category` AS p 
                    LEFT JOIN `product` pr ON p.id_product = pr.id_product
                    LEFT JOIN `category` c ON p.id_category = c.id_category
                     

                    WHERE  `p`.`id_product` = :id_product  AND  `p`.`id_category` = :id_category  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_product' => $this->id_product, ':id_category' => $this->id_category))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_product = $row['id_product'];
                $this->id_category = $row['id_category'];

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
    * Get a list of row from product_category.
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

        $query = "  SELECT p.`id_product`, p.`id_category`
                    FROM `product_category` AS p                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
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
            $i=0;            while($row = $q->fetch(\PDO::FETCH_ASSOC)){
               $this->list[$i]['id_product'] = $row['id_product'];
               $this->list[$i]['id_category'] = $row['id_category'];
                    $i++;            }
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
                  FROM `product_category`
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
    