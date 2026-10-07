<?php
/**
 * Addresses of the users: one user has many addresses.
 * type = postal (invoice, mail) or delivery.
 * The texts are stored html-escaped, like the other components do: the views display them as they are stored.
 */

declare(strict_types=1);
namespace Apgenic\Address;

class ModelAddress extends \Apgenic\Classes\Model{

    public array $list = array();

    // Columns of the table: the only names accepted as filter and as sort field
    const COLUMNS = array('id_address', 'id_user', 'type', 'street', 'number', 'box', 'postal_code', 'city', 'country', 'phone', 'created_at', 'updated_at', 'deleted_at');
    const TYPES = array('postal', 'delivery');
    // Countries proposed in the forms, ISO code => name
    const COUNTRIES = array('BE' => 'Belgium', 'FR' => 'France', 'LU' => 'Luxembourg', 'NL' => 'Netherlands', 'DE' => 'Germany');
    // Fields typed by the user => maximum length
    const FIELDS = array('street' => 200, 'number' => 20, 'box' => 20, 'postal_code' => 20, 'city' => 100, 'phone' => 30);
    const REQUIRED = array('street', 'number', 'postal_code', 'city');

    public $id_address = 0;
    public $id_user = 0;
    public $type = 'postal';
    public $street = '';
    public $number = '';
    public $box = null;
    public $postal_code = '';
    public $city = '';
    public $country = 'BE';
    public $phone = null;
    public $created_at = '';
    public $updated_at = '';
    public $deleted_at = null;


    /**
    * Fill the address with the fields of a form and check them.
    * @param array $input the fields, ex: $_POST
    * @param string $prefix prefix of the names of the fields, ex: 'delivery_' for delivery_street
    * @return array field (with its prefix) => error message, empty if the address is valid
    */
    public function fill(array $input, string $prefix = ''):array{
        $errors = array();

        foreach(self::FIELDS as $field => $max){
            $value = $input[$prefix.$field] ?? '';
            $value = is_string($value) ? trim((string)filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS)) : '';

            if($value == '' && in_array($field, self::REQUIRED, true)){
                $errors[$prefix.$field] = 'Required';
            }
            elseif(strlen($value) > $max){
                $errors[$prefix.$field] = 'Too long';
            }
            $this->$field = $value;
        }

        $country = $input[$prefix.'country'] ?? '';
        if(!is_string($country) || !isset(self::COUNTRIES[$country])){
            $errors[$prefix.'country'] = 'Required';
        }
        else{
            $this->country = $country;
        }
        return $errors;
    }


    /**
    * Add a row in address.
    * @return int 0 or 1
    */
    public function add():int{

        $query = "  INSERT INTO `address` (id_user, type, street, number, box, postal_code, city, country, phone)
                    VALUES (:id_user, :type, :street, :number, :box, :postal_code, :city, :country, :phone)";

        $q = self::$db->prepare($query);
        if($q->execute(array(
            ':id_user' => (int)$this->id_user,
            ':type' => in_array($this->type, self::TYPES, true) ? $this->type : 'postal',
            ':street' => $this->street,
            ':number' => $this->number,
            ':box' => $this->box == '' ? null : $this->box,
            ':postal_code' => $this->postal_code,
            ':city' => $this->city,
            ':country' => $this->country,
            ':phone' => $this->phone == '' ? null : $this->phone,
        ))){
            $this->id_address = (int)self::$db->lastInsertId();
            return(1);
        }
        return(0);
    }


    /**
    * Get a row in address.
    * @param int $idUser when given, the address must belong to this user
    * @return int 0 or 1
    */
    public function get(int $idUser = 0):int{

        $query = "SELECT `".implode('`, `', self::COLUMNS)."` FROM `address` WHERE `id_address` = :id_address";
        $params = array(':id_address' => (int)$this->id_address);
        if($idUser > 0){
            $query .= " AND `id_user` = :id_user AND `deleted_at` IS NULL";
            $params[':id_user'] = $idUser;
        }

        $q = self::$db->prepare($query);
        if($q->execute($params) && $row = $q->fetch(\PDO::FETCH_ASSOC)){
            foreach(self::COLUMNS as $column){
                $this->$column = $row[$column];
            }
            return(1);
        }
        return(0);
    }


    /**
    * Addresses of a user, the most recent first.
    * @param string $type '' for all the types
    * @return int number of addresses, they are in $this->list
    */
    public function getListByUser(int $idUser, string $type = ''):int{

        $query = "SELECT `".implode('`, `', self::COLUMNS)."` FROM `address` WHERE `id_user` = :id_user AND `deleted_at` IS NULL";
        $params = array(':id_user' => $idUser);
        if(in_array($type, self::TYPES, true)){
            $query .= " AND `type` = :type";
            $params[':type'] = $type;
        }
        $query .= " ORDER BY `id_address` DESC";

        $this->list = array();
        $q = self::$db->prepare($query);
        if($q->execute($params)){
            while($row = $q->fetch(\PDO::FETCH_ASSOC)){
                $this->list[$row['id_address']] = $row;
            }
        }
        return(count($this->list));
    }


    /**
    * The address on one line, ex: Rue de la Loi 16 bte 2, 1000 Bruxelles, Belgium
    * @param array $row a row of the table
    * @return string
    */
    public static function line(array $row):string{
        $line = $row['street'].' '.$row['number'];
        if(($row['box'] ?? '') != ''){
            $line .= ' box '.$row['box'];
        }
        return $line.', '.$row['postal_code'].' '.$row['city'].', '.(self::COUNTRIES[$row['country']] ?? $row['country']);
    }
}
