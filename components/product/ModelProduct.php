<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\Product;
/**
*	Manage elements of the table product.
*/
class ModelProduct extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    // Columns of the table: the only names accepted as filter and as sort field
    const COLUMNS = array('id_product', 'id_user', 'name', 'slug', 'summary', 'description', 'status', 'published_at', 'created_at', 'updated_at', 'deleted_at');

    public $id_product = '';
    public $id_user = null;
    public $name = '';
    public $slug = null;
    public $summary = null;
    public $description = null;
    public $status = array();
    public $published_at = null;
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
    * Add a row in product.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `product`  ( id_user, name, slug, summary, description, status, published_at)
                    VALUES (
                    :id_user,
                    :name,
                    :slug,
                    :summary,
                    :description,
                    :status,
                    :published_at
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':id_user' => $this->id_user, ':name' => $this->name, ':slug' => $this->slug, ':summary' => $this->summary, ':description' => $this->description, ':status' => $this->status, ':published_at' => $this->published_at,))){
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
    * Update a row in product.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_product > 0){
            $query = "  UPDATE `product` SET
                    `id_user` = :id_user,
                    `name` = :name,
                    `slug` = :slug,
                    `summary` = :summary,
                    `description` = :description,
                    `status` = :status,
                    `published_at` = :published_at
            WHERE  `id_product` = :id_product  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':id_user' => $this->id_user, ':name' => $this->name, ':slug' => $this->slug, ':summary' => $this->summary, ':description' => $this->description, ':status' => $this->status, ':published_at' => $this->published_at, ':id_product' => $this->id_product))){
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
    * Delete a row in product.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `product` WHERE  `id_product` = :id_product ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_product' => $this->id_product))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


        /**
    * Logical delete a row in product.
    * @return int 0 or 1
    */
    public function logicalDel():int{

                $query = "UPDATE `product` SET deleted_at = NOW() WHERE  `id_product` = :id_product ";
                    $q = self::$db->prepare($query);

        if($q->execute(array(':id_product' => $this->id_product))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    /**
    * Logical undelete a row in product.
    * @return int 0 or 1
    */
    public function logicalUnDel():int{

        $query = "UPDATE `product` SET deleted_at = :deleted WHERE  `id_product` = :id_product  ";
        $q = self::$db->prepare($query);

        if($q->execute(array(':id_product' => $this->id_product, ':deleted' => NULL))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    
    /**
    * Get a row in product.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT p.`id_product`, p.`id_user`, p.`name`, p.`slug`, p.`summary`, p.`description`, p.`status`, p.`published_at`, p.`created_at`, p.`updated_at`, p.`deleted_at`
                    , u.email AS u_email, u.id_user AS u_id_user
                                        FROM `product` AS p 
                    LEFT JOIN `user` u ON p.id_user = u.id_user
                     

                    WHERE  `p`.`id_product` = :id_product  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_product' => $this->id_product))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_product = $row['id_product'];
                $this->id_user = $row['id_user'];
                $this->name = $row['name'];
                $this->slug = $row['slug'];
                $this->summary = $row['summary'];
                $this->description = $row['description'];
                $this->status = $row['status'];
                $this->published_at = $row['published_at'];
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
    * Get a list of row from product.
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

        $query = "  SELECT p.`id_product`, p.`id_user`, p.`name`, p.`slug`, p.`summary`, p.`description`, p.`status`, p.`published_at`, p.`created_at`, p.`updated_at`, p.`deleted_at`
                    FROM `product` AS p                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
            $where .= "
                AND(
                    name LIKE :text_search 
                    OR slug LIKE :text_search 
                    OR summary LIKE :text_search 
                    OR description LIKE :text_search )
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
               $this->list[$row['id_product']]['id_product'] = $row['id_product'];
               $this->list[$row['id_product']]['id_user'] = $row['id_user'];
               $this->list[$row['id_product']]['name'] = $row['name'];
               $this->list[$row['id_product']]['slug'] = $row['slug'];
               $this->list[$row['id_product']]['summary'] = $row['summary'];
               $this->list[$row['id_product']]['description'] = $row['description'];
               $this->list[$row['id_product']]['status'] = $row['status'];
               $this->list[$row['id_product']]['published_at'] = $row['published_at'];
               $this->list[$row['id_product']]['created_at'] = $row['created_at'];
               $this->list[$row['id_product']]['updated_at'] = $row['updated_at'];
               $this->list[$row['id_product']]['deleted_at'] = $row['deleted_at'];
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
                  FROM `product`
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
    