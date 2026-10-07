<?php
/**
 * Cart of the shop, stored in the table cart_item.
 * The cart belongs to the logged user (id_user), or to the visitor: a random identifier
 * kept in the cookie "cart_id" (cookie_id). When the visitor signs in, his cart joins the one of his account.
 */

declare(strict_types=1);
namespace Apgenic\Shop;

use Apgenic\Classes\Auth;

class ModelCart extends \Apgenic\Classes\Model{

    const COOKIE = 'cart_id';
    // Life of the cart of a visitor, in days
    const COOKIE_DAYS = 30;
    const MAX_QUANTITY = 99;

    private int $idUser = 0;
    private string $cookieId = '';

    /** Lines of the cart, filled by load() */
    public array $items = array();
    /** Amounts in cents, filled by load() */
    public int $subtotal = 0;
    public int $tax = 0;
    public int $total = 0;


    public function __construct(){
        parent::__construct();
        $this->identify();
    }


    /**
     * Rate of the tax added to the prices, in percent (TAX_RATE in config.php)
     * @return float
     */
    public static function taxRate():float{
        return defined('TAX_RATE') ? (float)TAX_RATE : 21.0;
    }


    /**
     * Find the owner of the cart: the user of the session, or the cookie of the visitor.
     * Must be called again after a login made during the request.
     * @return void
     */
    public function identify():void{
        $this->idUser = Auth::isLogged() ? (int)$_SESSION['user']['id_user'] : 0;

        $cookie = $_COOKIE[self::COOKIE] ?? '';
        $this->cookieId = is_string($cookie) && preg_match('/^[a-f0-9]{40}$/', $cookie) ? $cookie : '';

        // The visitor has signed in: his cart joins the cart of his account
        if($this->idUser > 0 && $this->cookieId != ''){
            $this->merge();
        }
    }


    /**
     * Move the lines of the cart of the visitor to the cart of the user
     * @return void
     */
    private function merge():void{
        if(!isset(self::$db)) return;

        $q = self::$db->prepare("SELECT `id_cart_item`, `id_product`, `quantity` FROM `cart_item` WHERE `cookie_id` = :cookie_id AND `id_user` IS NULL");
        $q->execute(array(':cookie_id' => $this->cookieId));

        foreach($q->fetchAll(\PDO::FETCH_ASSOC) as $row){
            $same = self::$db->prepare("SELECT `id_cart_item`, `quantity` FROM `cart_item` WHERE `id_user` = :id_user AND `id_product` = :id_product");
            $same->execute(array(':id_user' => $this->idUser, ':id_product' => $row['id_product']));

            if($existing = $same->fetch(\PDO::FETCH_ASSOC)){
                // Same product in the two carts: the quantities are added
                $quantity = min(self::MAX_QUANTITY, (int)$existing['quantity'] + (int)$row['quantity']);
                self::$db->prepare("UPDATE `cart_item` SET `quantity` = :quantity WHERE `id_cart_item` = :id")->execute(array(':quantity' => $quantity, ':id' => $existing['id_cart_item']));
                self::$db->prepare("DELETE FROM `cart_item` WHERE `id_cart_item` = :id")->execute(array(':id' => $row['id_cart_item']));
            }
            else{
                self::$db->prepare("UPDATE `cart_item` SET `id_user` = :id_user, `cookie_id` = NULL WHERE `id_cart_item` = :id")->execute(array(':id_user' => $this->idUser, ':id' => $row['id_cart_item']));
            }
        }

        // The cookie is not needed anymore
        $this->cookieId = '';
        unset($_COOKIE[self::COOKIE]);
        if(!headers_sent()){
            setcookie(self::COOKIE, '', array('expires' => time() - 42000, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax'));
        }
    }


    /**
     * Condition selecting the lines of the current cart
     * @return array sql condition and its values, empty condition if the visitor has no cart yet
     */
    private function owner():array{
        if($this->idUser > 0){
            return array('`id_user` = :owner', array(':owner' => $this->idUser));
        }
        if($this->cookieId != ''){
            return array('`id_user` IS NULL AND `cookie_id` = :owner', array(':owner' => $this->cookieId));
        }
        return array('', array());
    }


    /**
     * Add a product to the cart. Only an active product can be bought.
     * For a visitor, the cookie is created here: call it before any output.
     * @return bool
     */
    public function add(int $idProduct, int $quantity = 1):bool{
        $quantity = max(1, min(self::MAX_QUANTITY, $quantity));

        $q = self::$db->prepare("SELECT `id_product` FROM `product` WHERE `id_product` = :id_product AND `status` = 'active' AND `deleted_at` IS NULL");
        $q->execute(array(':id_product' => $idProduct));
        if(!$q->fetch(\PDO::FETCH_ASSOC)){
            return false;
        }

        if($this->idUser < 1 && $this->cookieId == ''){
            $this->cookieId = bin2hex(random_bytes(20));
            $_COOKIE[self::COOKIE] = $this->cookieId;
            setcookie(self::COOKIE, $this->cookieId, array(
                'expires' => time() + self::COOKIE_DAYS * 86400,
                'path' => '/',
                'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off',
                'httponly' => true,
                'samesite' => 'Lax',
            ));
        }
        list($where, $params) = $this->owner();

        // The product is already in the cart: one line, a bigger quantity
        $q = self::$db->prepare("SELECT `id_cart_item`, `quantity` FROM `cart_item` WHERE $where AND `id_product` = :id_product");
        $q->execute($params + array(':id_product' => $idProduct));
        if($row = $q->fetch(\PDO::FETCH_ASSOC)){
            $q = self::$db->prepare("UPDATE `cart_item` SET `quantity` = :quantity WHERE `id_cart_item` = :id");
            return $q->execute(array(':quantity' => min(self::MAX_QUANTITY, (int)$row['quantity'] + $quantity), ':id' => $row['id_cart_item']));
        }

        $q = self::$db->prepare("INSERT INTO `cart_item` (id_user, cookie_id, id_product, quantity) VALUES (:id_user, :cookie_id, :id_product, :quantity)");
        return $q->execute(array(
            ':id_user' => $this->idUser > 0 ? $this->idUser : null,
            ':cookie_id' => $this->idUser > 0 ? null : $this->cookieId,
            ':id_product' => $idProduct,
            ':quantity' => $quantity,
        ));
    }


    /**
     * Change the quantity of a line of the current cart, 0 removes the line
     * @return bool
     */
    public function setQuantity(int $idCartItem, int $quantity):bool{
        list($where, $params) = $this->owner();
        if($where == ''){
            return false;
        }
        if($quantity < 1){
            $q = self::$db->prepare("DELETE FROM `cart_item` WHERE $where AND `id_cart_item` = :id");
            return $q->execute($params + array(':id' => $idCartItem));
        }
        $q = self::$db->prepare("UPDATE `cart_item` SET `quantity` = :quantity WHERE $where AND `id_cart_item` = :id");
        return $q->execute($params + array(':quantity' => min(self::MAX_QUANTITY, $quantity), ':id' => $idCartItem));
    }


    /**
     * Empty the current cart
     * @return bool
     */
    public function clear():bool{
        list($where, $params) = $this->owner();
        if($where == ''){
            return true;
        }
        $this->items = array();
        return self::$db->prepare("DELETE FROM `cart_item` WHERE $where")->execute($params);
    }


    /**
     * Number of articles in the current cart (sum of the quantities)
     * @return int
     */
    public function count():int{
        list($where, $params) = $this->owner();
        if($where == '' || !isset(self::$db)){
            return 0;
        }
        $q = self::$db->prepare("SELECT SUM(`quantity`) AS nb FROM `cart_item` WHERE $where");
        $q->execute($params);
        $row = $q->fetch(\PDO::FETCH_ASSOC);
        return (int)($row['nb'] ?? 0);
    }


    /**
     * Load the lines of the current cart with the name and the price of the products, and compute the amounts.
     * A product that is not sold anymore (not active, deleted) is removed from the cart.
     * The prices are without tax; the tax is computed line by line, like in customer_order_item.
     * @return int number of lines
     */
    public function load():int{
        $this->items = array();
        $this->subtotal = $this->tax = $this->total = 0;

        list($where, $params) = $this->owner();
        if($where == ''){
            return 0;
        }

        $q = self::$db->prepare("SELECT c.`id_cart_item`, c.`id_product`, c.`quantity`, p.`name`, p.`slug`, p.`price_amount`, p.`status`, p.`deleted_at`
                                 FROM `cart_item` c
                                 LEFT JOIN `product` p ON p.id_product = c.id_product
                                 WHERE ".str_replace('`id_user`', 'c.`id_user`', $where)."
                                 ORDER BY c.`id_cart_item`");
        $q->execute($params);

        foreach($q->fetchAll(\PDO::FETCH_ASSOC) as $row){
            if($row['status'] != 'active' || $row['deleted_at'] !== null){
                $this->setQuantity((int)$row['id_cart_item'], 0);
                continue;
            }
            $quantity = (int)$row['quantity'];
            $unit = (int)$row['price_amount'];
            $lineSubtotal = $quantity * $unit;
            $lineTax = (int)round($lineSubtotal * self::taxRate() / 100);

            $this->items[(int)$row['id_cart_item']] = array(
                'id_cart_item' => (int)$row['id_cart_item'],
                'id_product' => (int)$row['id_product'],
                'name' => (string)$row['name'],
                'sku' => ($row['slug'] ?? '') != '' ? (string)$row['slug'] : 'P-'.$row['id_product'],
                'quantity' => $quantity,
                'unit_price_amount' => $unit,
                'subtotal_amount' => $lineSubtotal,
                'tax_amount' => $lineTax,
                'line_total_amount' => $lineSubtotal + $lineTax,
            );
            $this->subtotal += $lineSubtotal;
            $this->tax += $lineTax;
        }
        $this->total = $this->subtotal + $this->tax;
        return count($this->items);
    }
}
