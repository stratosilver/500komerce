<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\ProductMedia;
/**
*	Manage elements of the table product_media.
*/
class ModelProductMedia extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    // Columns of the table: the only names accepted as filter and as sort field
    const COLUMNS = array('id_product', 'id_media', 'position');

    public $id_product = '';
    public $id_media = '';
    public $position = '';


    /**
    * Constructor
    */
    public function __construct(){
        parent::__construct();
    }


        /**
    * Add a row in product_media.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `product_media`  ( id_product, id_media, position)
                    VALUES (
                    :id_product,
                    :id_media,
                    :position
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':id_product' => $this->id_product, ':id_media' => $this->id_media, ':position' => $this->position,))){
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
    * Update a row in product_media.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_product > 0 && $this->id_media > 0){
            $query = "  UPDATE `product_media` SET
                    `id_product` = :id_product,
                    `id_media` = :id_media,
                    `position` = :position
            WHERE  `id_product` = :id_product  AND  `id_media` = :id_media  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':id_product' => $this->id_product, ':id_media' => $this->id_media, ':position' => $this->position, ':id_product' => $this->id_product, ':id_media' => $this->id_media))){
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
    * Delete a row in product_media.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `product_media` WHERE  `id_product` = :id_product  AND  `id_media` = :id_media ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_product' => $this->id_product, ':id_media' => $this->id_media))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    
    /**
    * Get a row in product_media.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT p.`id_product`, p.`id_media`, p.`position`
                    , m.filename AS m_filename, m.id_media AS m_id_media
                    , pr.name AS pr_name, pr.id_product AS pr_id_product
                                        FROM `product_media` AS p 
                    LEFT JOIN `media` m ON p.id_media = m.id_media
                    LEFT JOIN `product` pr ON p.id_product = pr.id_product
                     

                    WHERE  `p`.`id_product` = :id_product  AND  `p`.`id_media` = :id_media  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_product' => $this->id_product, ':id_media' => $this->id_media))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_product = $row['id_product'];
                $this->id_media = $row['id_media'];
                $this->position = $row['position'];

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
    * Get a list of row from product_media.
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

        $query = "  SELECT p.`id_product`, p.`id_media`, p.`position`
                    FROM `product_media` AS p                    ";

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
               $this->list[$i]['id_media'] = $row['id_media'];
               $this->list[$i]['position'] = $row['position'];
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
                  FROM `product_media`
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
    