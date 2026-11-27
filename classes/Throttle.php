<?php
/**
 * Limit of attempts (sign in, password recovery, registration, access codes), stored in the database.
 *
 * The counters of the session are not enough: a program that does not send the session cookie starts a new
 * session at each request. These counters are kept by IP address and by account, in the table `throttle`
 * (created here when it does not exist, see also _dev/500komerce_sqlite.sql).
 * Only a hash of the IP address and of the email is stored.
 *
 * Usage
 *   if(Throttle::lockedFor('login-ip', Throttle::ip()) > 0) ... refuse
 *   Throttle::hit('login-ip', Throttle::ip(), 20, 900, 900);   // 20 attempts in 15 minutes => locked 15 minutes
 *   Throttle::clear('login-email', $email);                    // after a success
 *
 * A problem with the database never blocks the users: the limit is then not applied (written in the log).
 */

declare(strict_types=1);
namespace Apgenic\Classes;

class Throttle
{
    private static bool $tableReady = false;


    /** IP address of the visitor */
    public static function ip():string{
        return (string)($_SERVER['REMOTE_ADDR'] ?? '');
    }


    /**
     * Number of seconds before a new attempt is allowed
     * @param string $bucket kind of counter, ex: 'login-ip'
     * @param string $key what is counted, ex: the IP address or the email
     * @return int 0 when the attempt is allowed
     */
    public static function lockedFor(string $bucket, string $key):int{
        $db = self::db();
        if($db === null){
            return 0;
        }
        try{
            $q = $db->prepare("SELECT `locked_until` FROM `throttle` WHERE `bucket` = :bucket AND `key_hash` = :key_hash");
            $q->execute(array(':bucket' => $bucket, ':key_hash' => self::hash($key)));
            $until = (int)$q->fetchColumn();
            return max(0, $until - time());
        }
        catch(\Throwable $e){
            error_log('Throttle: '.$e->getMessage());
            return 0;
        }
    }


    /**
     * Count one attempt. After $max attempts in $window seconds, the key is locked for $lock seconds.
     * @return int number of seconds of the lock, 0 if the key is not locked
     */
    public static function hit(string $bucket, string $key, int $max, int $window, int $lock):int{
        $db = self::db();
        if($db === null){
            return 0;
        }
        $now = time();
        $params = array(':bucket' => $bucket, ':key_hash' => self::hash($key));
        try{
            // One statement: two requests at the same time cannot both read the old count.
            // No transaction here: this can be called while the caller has one open
            $q = $db->prepare("INSERT INTO `throttle` (`bucket`, `key_hash`, `attempts`, `window_start`, `locked_until`)
                               VALUES (:bucket, :key_hash, 1, :now, 0)
                               ON CONFLICT(`bucket`, `key_hash`) DO UPDATE SET
                                   `attempts` = CASE WHEN `window_start` > :since THEN `attempts` + 1 ELSE 1 END,
                                   `window_start` = CASE WHEN `window_start` > :since THEN `window_start` ELSE :now END");
            $q->execute($params + array(':now' => $now, ':since' => $now - $window));

            // Too many attempts: locked, the count starts again
            $q = $db->prepare("UPDATE `throttle` SET `locked_until` = :until, `attempts` = 0, `window_start` = :now
                               WHERE `bucket` = :bucket AND `key_hash` = :key_hash AND `attempts` >= :max");
            $q->execute($params + array(':until' => $now + $lock, ':now' => $now, ':max' => $max));

            // From time to time, the old counters are removed
            if(random_int(1, 50) == 1){
                $db->prepare("DELETE FROM `throttle` WHERE `window_start` < :old AND `locked_until` < :now")
                   ->execute(array(':old' => $now - 86400, ':now' => $now));
            }

            $q = $db->prepare("SELECT `locked_until` FROM `throttle` WHERE `bucket` = :bucket AND `key_hash` = :key_hash");
            $q->execute($params);
            return max(0, (int)$q->fetchColumn() - $now);
        }
        catch(\Throwable $e){
            error_log('Throttle: '.$e->getMessage());
            return 0;
        }
    }


    /**
     * Forget the attempts of a key, ex: after a successful sign in
     * @return void
     */
    public static function clear(string $bucket, string $key):void{
        $db = self::db();
        if($db === null){
            return;
        }
        try{
            $db->prepare("DELETE FROM `throttle` WHERE `bucket` = :bucket AND `key_hash` = :key_hash")
               ->execute(array(':bucket' => $bucket, ':key_hash' => self::hash($key)));
        }
        catch(\Throwable $e){
            error_log('Throttle: '.$e->getMessage());
        }
    }


    /** The emails are counted whatever their case */
    private static function hash(string $key):string{
        return hash('sha256', strtolower(trim($key)));
    }


    /**
     * Connection to the database, the table is created the first time
     * @return \PDO|null
     */
    private static function db():?\PDO{
        new Model();
        if(!isset(Model::$db)){
            return null;
        }
        if(!self::$tableReady){
            try{
                Model::$db->exec("CREATE TABLE IF NOT EXISTS `throttle` (
                    `bucket` TEXT NOT NULL,
                    `key_hash` TEXT NOT NULL,
                    `attempts` INTEGER NOT NULL DEFAULT 0,
                    `window_start` INTEGER NOT NULL DEFAULT 0,
                    `locked_until` INTEGER NOT NULL DEFAULT 0,
                    PRIMARY KEY (`bucket`, `key_hash`)
                )");
                self::$tableReady = true;
            }
            catch(\Throwable $e){
                error_log('Throttle: '.$e->getMessage());
                return null;
            }
        }
        return Model::$db;
    }
}
