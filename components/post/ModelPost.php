<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\Post;
/**
*	Manage elements of the table post.
*/
class ModelPost extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    // Columns of the table: the only names accepted as filter and as sort field
    const COLUMNS = array('id_post', 'id_user', 'lang', 'title', 'id_media', 'slug', 'excerpt', 'body', 'status', 'published_at', 'created_at', 'updated_at', 'deleted_at');

    public $id_post = '';
    public $id_user = null;
    public $lang = '';
    public $title = '';
    public $id_media = null;
    public $slug = '';
    public $excerpt = null;
    public $body = '';
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
    * Add a row in post.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `post`  ( id_user, lang, title, id_media, slug, excerpt, body, status, published_at)
                    VALUES (
                    :id_user,
                    :lang,
                    :title,
                    :id_media,
                    :slug,
                    :excerpt,
                    :body,
                    :status,
                    :published_at
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':id_user' => $this->id_user, ':lang' => $this->lang, ':title' => $this->title, ':id_media' => $this->id_media, ':slug' => $this->slug, ':excerpt' => $this->excerpt, ':body' => $this->body, ':status' => $this->status, ':published_at' => $this->published_at,))){
            $this->id_post = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in post.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_post > 0){
            $query = "  UPDATE `post` SET
                    `id_user` = :id_user,
                    `lang` = :lang,
                    `title` = :title,
                    `id_media` = :id_media,
                    `slug` = :slug,
                    `excerpt` = :excerpt,
                    `body` = :body,
                    `status` = :status,
                    `published_at` = :published_at
            WHERE  `id_post` = :id_post  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':id_user' => $this->id_user, ':lang' => $this->lang, ':title' => $this->title, ':id_media' => $this->id_media, ':slug' => $this->slug, ':excerpt' => $this->excerpt, ':body' => $this->body, ':status' => $this->status, ':published_at' => $this->published_at, ':id_post' => $this->id_post))){
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
    * Delete a row in post.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `post` WHERE  `id_post` = :id_post ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_post' => $this->id_post))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


        /**
    * Logical delete a row in post.
    * @return int 0 or 1
    */
    public function logicalDel():int{

                $query = "UPDATE `post` SET deleted_at = NOW() WHERE  `id_post` = :id_post ";
                    $q = self::$db->prepare($query);

        if($q->execute(array(':id_post' => $this->id_post))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    /**
    * Logical undelete a row in post.
    * @return int 0 or 1
    */
    public function logicalUnDel():int{

        $query = "UPDATE `post` SET deleted_at = :deleted WHERE  `id_post` = :id_post  ";
        $q = self::$db->prepare($query);

        if($q->execute(array(':id_post' => $this->id_post, ':deleted' => NULL))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    
    /**
    * Get a row in post.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT p.`id_post`, p.`id_user`, p.`lang`, p.`title`, p.`id_media`, p.`slug`, p.`excerpt`, p.`body`, p.`status`, p.`published_at`, p.`created_at`, p.`updated_at`, p.`deleted_at`
                    , u.email AS u_email, u.id_user AS u_id_user
                                        FROM `post` AS p 
                    LEFT JOIN `user` u ON p.id_user = u.id_user
                     

                    WHERE  `p`.`id_post` = :id_post  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_post' => $this->id_post))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_post = $row['id_post'];
                $this->id_user = $row['id_user'];
                $this->lang = $row['lang'];
                $this->title = $row['title'];
                $this->id_media = $row['id_media'];
                $this->slug = $row['slug'];
                $this->excerpt = $row['excerpt'];
                $this->body = $row['body'];
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
    * Get a list of row from post.
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

        $query = "  SELECT p.`id_post`, p.`id_user`, p.`lang`, p.`title`, p.`id_media`, p.`slug`, p.`excerpt`, p.`body`, p.`status`, p.`published_at`, p.`created_at`, p.`updated_at`, p.`deleted_at`
                    FROM `post` AS p                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
            $where .= "
                AND(
                    lang LIKE :text_search 
                    OR title LIKE :text_search 
                    OR slug LIKE :text_search 
                    OR excerpt LIKE :text_search 
                    OR body LIKE :text_search )
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
               $this->list[$row['id_post']]['id_post'] = $row['id_post'];
               $this->list[$row['id_post']]['id_user'] = $row['id_user'];
               $this->list[$row['id_post']]['lang'] = $row['lang'];
               $this->list[$row['id_post']]['title'] = $row['title'];
               $this->list[$row['id_post']]['id_media'] = $row['id_media'];
               $this->list[$row['id_post']]['slug'] = $row['slug'];
               $this->list[$row['id_post']]['excerpt'] = $row['excerpt'];
               $this->list[$row['id_post']]['body'] = $row['body'];
               $this->list[$row['id_post']]['status'] = $row['status'];
               $this->list[$row['id_post']]['published_at'] = $row['published_at'];
               $this->list[$row['id_post']]['created_at'] = $row['created_at'];
               $this->list[$row['id_post']]['updated_at'] = $row['updated_at'];
               $this->list[$row['id_post']]['deleted_at'] = $row['deleted_at'];
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
                  FROM `post`
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
    