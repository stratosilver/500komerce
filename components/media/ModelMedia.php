<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\Media;
/**
*	Manage elements of the table media.
*/
class ModelMedia extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    // Columns of the table: the only names accepted as filter and as sort field
    const COLUMNS = array('id_media', 'filename', 'mime_type', 'size_bytes', 'width', 'height', 'alt_text', 'caption', 'created_at', 'updated_at', 'deleted_at');

    public $id_media = '';
    public $filename = '';
    public $mime_type = '';
    public $size_bytes = null;
    public $width = null;
    public $height = null;
    public $alt_text = null;
    public $caption = null;
    public $created_at = '';
    public $updated_at = null;
    public $deleted_at = null;


    /**
    * Constructor
    */
    public function __construct(){
        parent::__construct();
    }


        /**
    * Add a row in media.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `media`  ( filename, mime_type, size_bytes, width, height, alt_text, caption)
                    VALUES (
                    :filename,
                    :mime_type,
                    :size_bytes,
                    :width,
                    :height,
                    :alt_text,
                    :caption
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':filename' => $this->filename, ':mime_type' => $this->mime_type, ':size_bytes' => $this->size_bytes, ':width' => $this->width, ':height' => $this->height, ':alt_text' => $this->alt_text, ':caption' => $this->caption,))){
            $this->id_media = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in media.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_media > 0){
            $query = "  UPDATE `media` SET
                    `filename` = :filename,
                    `mime_type` = :mime_type,
                    `size_bytes` = :size_bytes,
                    `width` = :width,
                    `height` = :height,
                    `alt_text` = :alt_text,
                    `caption` = :caption
            WHERE  `id_media` = :id_media  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':filename' => $this->filename, ':mime_type' => $this->mime_type, ':size_bytes' => $this->size_bytes, ':width' => $this->width, ':height' => $this->height, ':alt_text' => $this->alt_text, ':caption' => $this->caption, ':id_media' => $this->id_media))){
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
    * Delete a row in media.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `media` WHERE  `id_media` = :id_media ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_media' => $this->id_media))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


        /**
    * Logical delete a row in media.
    * @return int 0 or 1
    */
    public function logicalDel():int{

                $query = "UPDATE `media` SET deleted_at = NOW() WHERE  `id_media` = :id_media ";
                    $q = self::$db->prepare($query);

        if($q->execute(array(':id_media' => $this->id_media))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    /**
    * Logical undelete a row in media.
    * @return int 0 or 1
    */
    public function logicalUnDel():int{

        $query = "UPDATE `media` SET deleted_at = :deleted WHERE  `id_media` = :id_media  ";
        $q = self::$db->prepare($query);

        if($q->execute(array(':id_media' => $this->id_media, ':deleted' => NULL))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    
    /**
    * Get a row in media.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT m.`id_media`, m.`filename`, m.`mime_type`, m.`size_bytes`, m.`width`, m.`height`, m.`alt_text`, m.`caption`, m.`created_at`, m.`updated_at`, m.`deleted_at`
                                        FROM `media` AS m 
                     

                    WHERE  `m`.`id_media` = :id_media  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_media' => $this->id_media))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_media = $row['id_media'];
                $this->filename = $row['filename'];
                $this->mime_type = $row['mime_type'];
                $this->size_bytes = $row['size_bytes'];
                $this->width = $row['width'];
                $this->height = $row['height'];
                $this->alt_text = $row['alt_text'];
                $this->caption = $row['caption'];
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
    * Get a list of row from media.
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

        $query = "  SELECT m.`id_media`, m.`filename`, m.`mime_type`, m.`size_bytes`, m.`width`, m.`height`, m.`alt_text`, m.`caption`, m.`created_at`, m.`updated_at`, m.`deleted_at`
                    FROM `media` AS m                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
            $where .= "
                AND(
                    filename LIKE :text_search 
                    OR mime_type LIKE :text_search 
                    OR alt_text LIKE :text_search 
                    OR caption LIKE :text_search )
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
               $this->list[$row['id_media']]['id_media'] = $row['id_media'];
               $this->list[$row['id_media']]['filename'] = $row['filename'];
               $this->list[$row['id_media']]['mime_type'] = $row['mime_type'];
               $this->list[$row['id_media']]['size_bytes'] = $row['size_bytes'];
               $this->list[$row['id_media']]['width'] = $row['width'];
               $this->list[$row['id_media']]['height'] = $row['height'];
               $this->list[$row['id_media']]['alt_text'] = $row['alt_text'];
               $this->list[$row['id_media']]['caption'] = $row['caption'];
               $this->list[$row['id_media']]['created_at'] = $row['created_at'];
               $this->list[$row['id_media']]['updated_at'] = $row['updated_at'];
               $this->list[$row['id_media']]['deleted_at'] = $row['deleted_at'];
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
                  FROM `media`
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
    