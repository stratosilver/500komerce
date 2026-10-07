<?php
/**
 * Access control, the same rules for the web interface (index.php) and the API (api.php).
 *
 * Each user has a level of permissions from 0 to 100 (field `permissions` of the table `user`, 0 by default):
 *   0 and more                        the consultation pages (view, viewlist) and his own profile
 *   PERMISSION_EDIT (50) and more     all the tasks of all the components, except the users
 *   PERMISSION_ADMIN (100)            the users: list, edit, delete, change the permissions
 * The two levels can be changed in config.php.
 *
 * A task that is not listed here is refused: a new task needs PERMISSION_EDIT until it is declared in READ_TASKS.
 */

declare(strict_types=1);
namespace Apgenic\Classes;

class Auth
{
    // Tasks that only display data
    const READ_TASKS = array('view', 'viewlist');
    // Tasks of the component user that a logged user does on his own account
    const SELF_TASKS = array('profile', 'me');

    private static bool $loaded = false;
    private static ?int $level = null;


    public static function levelEdit():int{
        return defined('PERMISSION_EDIT') ? (int)PERMISSION_EDIT : 50;
    }

    public static function levelAdmin():int{
        return defined('PERMISSION_ADMIN') ? (int)PERMISSION_ADMIN : 100;
    }


    /**
     * Permissions of the user of the session.
     * Read in the database at each request: a change of level, a suspended or deleted account
     * takes effect immediately, without waiting for the next login.
     * @return int|null null if nobody is logged
     */
    public static function level():?int{
        if(self::$loaded){
            return self::$level;
        }
        self::$loaded = true;
        self::$level = null;

        $id = (int)($_SESSION['user']['id_user'] ?? 0);
        if($id < 1){
            return null;
        }

        new Model();
        if(!isset(Model::$db)){
            return null;
        }
        $q = Model::$db->prepare("SELECT `permissions` FROM `user` WHERE `id_user` = :id_user AND `status` = 'active' AND `deleted_at` IS NULL");
        $q->execute(array(':id_user' => $id));
        $row = $q->fetch(\PDO::FETCH_ASSOC);

        if($row === false){
            // The account does not exist anymore or is not active: the session is closed
            unset($_SESSION['user']);
            return null;
        }
        self::$level = max(0, min(100, (int)$row['permissions']));
        return self::$level;
    }


    public static function isLogged():bool{
        return self::level() !== null;
    }

    /** Can the user add, edit and delete (all the components except the users) */
    public static function canEdit():bool{
        return self::level() !== null && self::level() >= self::levelEdit();
    }

    /** Can the user manage the users */
    public static function isAdmin():bool{
        return self::level() !== null && self::level() >= self::levelAdmin();
    }


    /**
     * Tasks reachable without being logged: sign in, registration, password recovery
     * @return bool
     */
    public static function isPublic(string $component, string $task):bool{
        return $component === 'user' && in_array($task, \Apgenic\User\ControllerUser::AUTH_TASKS, true);
    }


    /**
     * Is the user of the session allowed to execute a task of a component
     * @param string $task name of the task, as written in the controller of the component
     * @return bool
     */
    public static function can(string $component, string $task):bool{
        if(self::isPublic($component, $task)){
            return true;
        }
        $level = self::level();
        if($level === null){
            return false;
        }

        // The users (emails, status, permissions) are managed by the administrators only
        if($component === 'user'){
            return in_array($task, self::SELF_TASKS, true) || $level >= self::levelAdmin();
        }

        if(in_array($task, self::READ_TASKS, true)){
            return true;
        }
        return $level >= self::levelEdit();
    }
}
