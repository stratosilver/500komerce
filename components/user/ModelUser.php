<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\User;
/**
*	Manage elements of the table user.
*/
class ModelUser extends \Apgenic\Classes\Model{

    private string $tableName = '';
    public array $list;

    // Columns of the table: the only names accepted as filter and as sort field
    const COLUMNS = array('id_user', 'email', 'email_verified', 'password_hash', 'first_name', 'last_name', 'id_media', 'provider', 'provider_user_id', 'status', 'permissions', 'last_login_at', 'created_at', 'updated_at', 'deleted_at');

    public $id_user = '';
    public $email = '';
    public $email_verified = '';
    public $password_hash = null;
    public $first_name = '';
    public $last_name = '';
    public $id_media = null;
    public $provider = '';
    public $provider_user_id = null;
    public $status = array();
    // Level of permissions, from 0 to 100 (see \Apgenic\Classes\Auth)
    public $permissions = 0;
    public $last_login_at = null;
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
    * Add a row in user.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `user`  ( email, email_verified, password_hash, first_name, last_name, id_media, provider, provider_user_id, status, permissions, last_login_at)
                    VALUES (
                    :email,
                    :email_verified,
                    :password_hash,
                    :first_name,
                    :last_name,
                    :id_media,
                    :provider,
                    :provider_user_id,
                    :status,
                    :permissions,
                    :last_login_at
                    )";



        $q = self::$db->prepare($query);
        if($q->execute(array(':email' => $this->email, ':email_verified' => $this->email_verified, ':password_hash' => $this->password_hash, ':first_name' => $this->first_name, ':last_name' => $this->last_name, ':id_media' => $this->id_media, ':provider' => $this->provider, ':provider_user_id' => $this->provider_user_id, ':status' => $this->status, ':permissions' => (int)$this->permissions, ':last_login_at' => $this->last_login_at,))){
            $this->id_user = self::$db->lastInsertId();
            return(1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


    /**
    * Update a row in user.
    * @return int 0 or 1
    */
    public function save():int{

        // the element exists
        if($this->id_user > 0){
            $query = "  UPDATE `user` SET
                    `email` = :email,
                    `email_verified` = :email_verified,
                    `password_hash` = :password_hash,
                    `first_name` = :first_name,
                    `last_name` = :last_name,
                    `id_media` = :id_media,
                    `provider` = :provider,
                    `provider_user_id` = :provider_user_id,
                    `status` = :status,
                    `permissions` = :permissions,
                    `last_login_at` = :last_login_at
            WHERE  `id_user` = :id_user  ";


                $q = self::$db->prepare($query);
            if($q->execute(array(':email' => $this->email, ':email_verified' => $this->email_verified, ':password_hash' => $this->password_hash, ':first_name' => $this->first_name, ':last_name' => $this->last_name, ':id_media' => $this->id_media, ':provider' => $this->provider, ':provider_user_id' => $this->provider_user_id, ':status' => $this->status, ':permissions' => (int)$this->permissions, ':last_login_at' => $this->last_login_at, ':id_user' => $this->id_user))){
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
    * Delete a row in user.
    * @return int 0 or 1
    */
    public function del():int{

        $query = "DELETE FROM `user` WHERE  `id_user` = :id_user ";

        $q = self::$db->prepare($query);

        if($q->execute(array(':id_user' => $this->id_user))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }


        /**
    * Logical delete a row in user.
    * @return int 0 or 1
    */
    public function logicalDel():int{

                $query = "UPDATE `user` SET deleted_at = NOW() WHERE  `id_user` = :id_user ";
                    $q = self::$db->prepare($query);

        if($q->execute(array(':id_user' => $this->id_user))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    /**
    * Logical undelete a row in user.
    * @return int 0 or 1
    */
    public function logicalUnDel():int{

        $query = "UPDATE `user` SET deleted_at = :deleted WHERE  `id_user` = :id_user  ";
        $q = self::$db->prepare($query);

        if($q->execute(array(':id_user' => $this->id_user, ':deleted' => NULL))){
            return (1);
        }
        else{
            //var_dump($q->errorInfo());
            //$q->debugDumpParams();
            return(0);
        }
    }

    
    /**
    * Get a row in user.
    * @return int 0 or 1
    */
    public function get():int{

        $query = "  SELECT u.`id_user`, u.`email`, u.`email_verified`, u.`password_hash`, u.`first_name`, u.`last_name`, u.`id_media`, u.`provider`, u.`provider_user_id`, u.`status`, u.`permissions`, u.`last_login_at`, u.`created_at`, u.`updated_at`, u.`deleted_at`
                    , m.filename AS m_filename, m.id_media AS m_id_media
                                        FROM `user` AS u 
                    LEFT JOIN `media` m ON u.id_media = m.id_media
                     

                    WHERE  `u`.`id_user` = :id_user  ";


        $q = self::$db->prepare($query);
        if($q->execute(array(':id_user' => $this->id_user))){
            if($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->id_user = $row['id_user'];
                $this->email = $row['email'];
                $this->email_verified = $row['email_verified'];
                $this->password_hash = $row['password_hash'];
                $this->first_name = $row['first_name'];
                $this->last_name = $row['last_name'];
                $this->id_media = $row['id_media'];
                $this->provider = $row['provider'];
                $this->provider_user_id = $row['provider_user_id'];
                $this->status = $row['status'];
                $this->permissions = (int)$row['permissions'];
                $this->last_login_at = $row['last_login_at'];
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
    * Get a list of row from user.
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

        $query = "  SELECT u.`id_user`, u.`email`, u.`email_verified`, u.`password_hash`, u.`first_name`, u.`last_name`, u.`id_media`, u.`provider`, u.`provider_user_id`, u.`status`, u.`permissions`, u.`last_login_at`, u.`created_at`, u.`updated_at`, u.`deleted_at`
                    FROM `user` AS u                    ";

        // Add all filters
        $where = 'WHERE 1 ';

        if($textSearch != ''){
            $where .= "
                AND(
                    email LIKE :text_search 
                    OR password_hash LIKE :text_search 
                    OR first_name LIKE :text_search 
                    OR last_name LIKE :text_search 
                    OR provider LIKE :text_search 
                    OR provider_user_id LIKE :text_search )
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
               $this->list[$row['id_user']]['id_user'] = $row['id_user'];
               $this->list[$row['id_user']]['email'] = $row['email'];
               $this->list[$row['id_user']]['email_verified'] = $row['email_verified'];
               $this->list[$row['id_user']]['password_hash'] = $row['password_hash'];
               $this->list[$row['id_user']]['first_name'] = $row['first_name'];
               $this->list[$row['id_user']]['last_name'] = $row['last_name'];
               $this->list[$row['id_user']]['id_media'] = $row['id_media'];
               $this->list[$row['id_user']]['provider'] = $row['provider'];
               $this->list[$row['id_user']]['provider_user_id'] = $row['provider_user_id'];
               $this->list[$row['id_user']]['status'] = $row['status'];
               $this->list[$row['id_user']]['permissions'] = $row['permissions'];
               $this->list[$row['id_user']]['last_login_at'] = $row['last_login_at'];
               $this->list[$row['id_user']]['created_at'] = $row['created_at'];
               $this->list[$row['id_user']]['updated_at'] = $row['updated_at'];
               $this->list[$row['id_user']]['deleted_at'] = $row['deleted_at'];
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
                  FROM `user`
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

    // Authentication
    // ------------------------------------------------------------------------------------------------

    /**
    * Load a user from his email address.
    * @param bool $onlyUsable ignore logically deleted users
    * @return int 0 or 1
    */
    public function getByEmail(string $email, bool $onlyUsable = true):int{

        $query = "SELECT `id_user` FROM `user` WHERE `email` = :email";
        if($onlyUsable){
            $query .= " AND `deleted_at` IS NULL";
        }

        $q = self::$db->prepare($query);
        if($q->execute(array(':email' => $email)) && $row = $q->fetch(\PDO::FETCH_ASSOC)){
            $this->id_user = $row['id_user'];
            return($this->get());
        }
        return(0);
    }


    /**
    * Create a local account (email, password_hash, first_name and last_name must be set).
    * @return int 0 or 1
    */
    public function register():int{

        $query = "  INSERT INTO `user` (email, email_verified, password_hash, first_name, last_name, provider, status)
                    VALUES (:email, 0, :password_hash, :first_name, :last_name, 'local', 'active')";

        $q = self::$db->prepare($query);
        if($q->execute(array(':email' => $this->email, ':password_hash' => $this->password_hash, ':first_name' => $this->first_name, ':last_name' => $this->last_name))){
            $this->id_user = self::$db->lastInsertId();
            return(1);
        }
        return(0);
    }


    /**
    * Change only the password of the user.
    * @return int 0 or 1
    */
    public function updatePassword(string $passwordHash):int{

        $q = self::$db->prepare("UPDATE `user` SET `password_hash` = :password_hash WHERE `id_user` = :id_user");
        return($q->execute(array(':password_hash' => $passwordHash, ':id_user' => $this->id_user)) ? 1 : 0);
    }


    /**
    * Change only what a user can edit in his own profile. A new email address has to be verified again.
    * @return int 0 or 1
    */
    public function updateProfile(string $email, string $firstName, string $lastName):int{

        $q = self::$db->prepare("UPDATE `user` SET `email_verified` = IF(`email` = :email_old, `email_verified`, 0), `email` = :email, `first_name` = :first_name, `last_name` = :last_name WHERE `id_user` = :id_user");
        return($q->execute(array(':email_old' => $email, ':email' => $email, ':first_name' => $firstName, ':last_name' => $lastName, ':id_user' => $this->id_user)) ? 1 : 0);
    }


    /**
    * Store the date of the last successful login.
    * @return int 0 or 1
    */
    public function touchLastLogin():int{

        $q = self::$db->prepare("UPDATE `user` SET `last_login_at` = NOW() WHERE `id_user` = :id_user");
        return($q->execute(array(':id_user' => $this->id_user)) ? 1 : 0);
    }


    /**
    * Create a single use password reset token for the user.
    * Only the SHA-256 hash of the token is stored, the clear token is returned to be sent by email.
    * @param int $validity validity in minutes
    * @return string the clear token
    */
    public function createPasswordResetToken(int $validity = 60):string{

        $this->deletePasswordResetTokens();

        $token = bin2hex(random_bytes(32));

        $q = self::$db->prepare("INSERT INTO `user_password_reset` (id_user, token_hash, expires_at)
                                 VALUES (:id_user, :token_hash, DATE_ADD(NOW(), INTERVAL :validity MINUTE))");
        $q->bindValue(':id_user', $this->id_user);
        $q->bindValue(':token_hash', hash('sha256', $token));
        $q->bindValue(':validity', $validity, \PDO::PARAM_INT);
        $q->execute();

        return($token);
    }


    /**
    * Load the user owning a valid (not expired) password reset token.
    * @return int 0 or 1
    */
    public function getByPasswordResetToken(string $token):int{

        if(!preg_match('/^[a-f0-9]{64}$/', $token)){
            return(0);
        }

        $q = self::$db->prepare("SELECT r.`id_user`
                                 FROM `user_password_reset` r
                                 INNER JOIN `user` u ON u.id_user = r.id_user
                                 WHERE r.`token_hash` = :token_hash
                                 AND r.`expires_at` > NOW()
                                 AND u.`deleted_at` IS NULL
                                 AND u.`status` = 'active'");

        if($q->execute(array(':token_hash' => hash('sha256', $token))) && $row = $q->fetch(\PDO::FETCH_ASSOC)){
            $this->id_user = $row['id_user'];
            return($this->get());
        }
        return(0);
    }


    /**
    * Delete the password reset tokens of the user and all the expired ones.
    * @return int 0 or 1
    */
    public function deletePasswordResetTokens():int{

        $q = self::$db->prepare("DELETE FROM `user_password_reset` WHERE `id_user` = :id_user OR `expires_at` < NOW()");
        return($q->execute(array(':id_user' => $this->id_user)) ? 1 : 0);
    }


    /**
    * Load a user from his identity at an external provider (google, facebook, x).
    * @return int 0 or 1
    */
    public function getByProvider(string $provider, string $providerUserId):int{

        $q = self::$db->prepare("SELECT `id_user` FROM `user` WHERE `provider` = :provider AND `provider_user_id` = :provider_user_id");

        if($q->execute(array(':provider' => $provider, ':provider_user_id' => $providerUserId)) && $row = $q->fetch(\PDO::FETCH_ASSOC)){
            $this->id_user = $row['id_user'];
            return($this->get());
        }
        return(0);
    }


    /**
    * Create an account from an external provider, without password
    * (email, first_name, last_name, provider and provider_user_id must be set).
    * @return int 0 or 1
    */
    public function registerSocial():int{

        $query = "  INSERT INTO `user` (email, email_verified, password_hash, first_name, last_name, provider, provider_user_id, status)
                    VALUES (:email, 1, NULL, :first_name, :last_name, :provider, :provider_user_id, 'active')";

        $q = self::$db->prepare($query);
        if($q->execute(array(':email' => $this->email, ':first_name' => $this->first_name, ':last_name' => $this->last_name, ':provider' => $this->provider, ':provider_user_id' => $this->provider_user_id))){
            $this->id_user = self::$db->lastInsertId();
            return(1);
        }
        return(0);
    }
}
