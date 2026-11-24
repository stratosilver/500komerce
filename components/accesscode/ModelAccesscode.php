<?php
/**
 * Tables access_code (the codes that open the form) and access_form (the forms sent).
 */

declare(strict_types=1);
namespace Apgenic\Accesscode;

class ModelAccesscode extends \Apgenic\Classes\Model{

    const COLUMNS = array('id_access_code', 'code', 'id_access_form', 'created_at', 'updated_at', 'deleted_at');

    public function __construct(){
        parent::__construct();
    }


    /**
     * Id of a code that exists and is not deleted
     * @return int 0 if the code does not exist
     */
    public function findCode(string $code):int{
        $q = self::$db->prepare("SELECT `id_access_code` FROM `access_code` WHERE `code` = :code AND `deleted_at` IS NULL");
        $q->execute(array(':code' => $code));
        $row = $q->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? 0 : (int)$row['id_access_code'];
    }


    // Letters and digits that cannot be confused when they are typed (no 0/O, 1/I/L)
    const CODE_ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';


    /**
     * Save a form, create the token of the link and the new codes sent with it, in one transaction
     * @param array $values field_01..field_15 => value
     * @return array ['token' => token to put in the link, 'codes' => the new codes], empty if nothing could be saved
     */
    public function saveFormWithCodes(int $idAccessCode, array $values, string $email, int $validityDays, int $numberOfCodes):array{
        self::$db->beginTransaction();
        try{
            $saved = $this->saveForm($idAccessCode, $values, $email, $validityDays);
            if($saved === array()){
                self::$db->rollBack();
                return array();
            }
            $saved['codes'] = $this->createCodes($saved['id_access_form'], $numberOfCodes);
            self::$db->commit();
            return $saved;
        }
        catch(\Throwable $e){
            if(self::$db->inTransaction()){
                self::$db->rollBack();
            }
            return array();
        }
    }


    /**
     * Create new random codes, ex: K7QM-3XPA
     * @return array the codes
     */
    private function createCodes(int $idAccessForm, int $count):array{
        $q = self::$db->prepare("INSERT INTO `access_code` (`code`, `id_access_form`) VALUES (:code, :id_access_form)");
        $codes = array();
        $max = strlen(self::CODE_ALPHABET) - 1;

        while(count($codes) < $count){
            $code = '';
            for($i = 0; $i < 8; $i++){
                $code .= ($i == 4 ? '-' : '').self::CODE_ALPHABET[random_int(0, $max)];
            }
            try{
                $q->execute(array(':code' => $code, ':id_access_form' => $idAccessForm));
                $codes[] = $code;
            }
            catch(\PDOException $e){
                // 23000: the code already exists, another one is drawn
                if($e->getCode() !== '23000'){
                    throw $e;
                }
            }
        }
        return $codes;
    }


    /**
     * Save a form and create the token of the link sent by email
     * @param array $values field_01..field_15 => value
     * @return array ['id_access_form', 'token'], empty if the form could not be saved
     */
    private function saveForm(int $idAccessCode, array $values, string $email, int $validityDays):array{
        $token = bin2hex(random_bytes(32));

        $columns = array('id_access_code', 'email', 'token_hash', 'token_expires_at');
        $params = array(
            ':id_access_code' => $idAccessCode,
            ':email' => $email,
            ':token_hash' => hash('sha256', $token),
            ':token_expires_at' => date('Y-m-d H:i:s', time() + $validityDays * 86400),
        );
        foreach(array_keys(ControllerAccesscode::FIELDS) as $field){
            $columns[] = $field;
            $params[':'.$field] = ($values[$field] ?? '') === '' ? null : $values[$field];
        }

        $query = "INSERT INTO `access_form` (`".implode('`, `', $columns)."`) VALUES (:".implode(', :', $columns).")";
        $q = self::$db->prepare($query);
        if(!$q->execute($params)){
            return array();
        }
        return array('id_access_form' => (int)self::$db->lastInsertId(), 'token' => $token);
    }


    /**
     * Form reached with the link sent by email
     * @return array|null the row of access_form, null if the link is not valid or has expired
     */
    public function getFormByToken(string $token):?array{
        if(!preg_match('/^[0-9a-f]{64}$/', $token)){
            return null;
        }
        $q = self::$db->prepare("SELECT * FROM `access_form` WHERE `token_hash` = :token_hash AND `token_expires_at` > datetime('now', 'localtime')");
        $q->execute(array(':token_hash' => hash('sha256', $token)));
        $row = $q->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }
}
