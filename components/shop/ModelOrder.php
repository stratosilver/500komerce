<?php
/**
 * Order created by the checkout: one row in customer_order, one row in customer_order_item by line of the cart.
 * The name and the price of the products are copied in the order: it does not change when a product does.
 *
 * Stock (product.stock = the units that can still be sold):
 *   - the checkout takes the quantities out of the stock, in the transaction that creates the order. Each line is
 *     one UPDATE ... WHERE stock >= quantity: two customers cannot buy the same last unit. Not enough stock for a
 *     line: nothing is saved, $stockErrors tells which products.
 *   - cancelling the order puts the quantities back, once (customer_order.stock_reduced). Cancelled is final.
 *   - the other statuses (paid, shipped) do not change the stock.
 * The transactions are BEGIN IMMEDIATE: SQLite gives the right to write at the start, the other requests wait
 * their turn (PDO::ATTR_TIMEOUT of classes/Model.php) instead of failing in the middle.
 */

declare(strict_types=1);
namespace Apgenic\Shop;

class ModelOrder extends \Apgenic\Classes\Model{

    /** Statuses of an order => color of its badge. The texts are T_VALUE_ + status (translations.php) */
    const STATUSES = array('pending' => 'warning', 'paid' => 'info', 'shipped' => 'success', 'cancelled' => 'secondary');
    const STATUS_NEW = 'pending';
    const STATUS_CANCELLED = 'cancelled';

    public int $id_customer_order = 0;
    /** Filled by getForUser() */
    public array $order = array();
    public array $items = array();
    /** Filled by create() when the stock is too low: id_product => array(name, stock) */
    public array $stockErrors = array();
    /** Set by setStatus(): the quantities of the order have been put back in the stock */
    public bool $restocked = false;


    /**
     * Create the order of a user with the lines of his cart, and take the quantities out of the stock.
     * All or nothing (transaction).
     * @param array $items lines of ModelCart::load()
     * @param int $total total of the order, cents, tax included
     * @return int id of the order, 0 if it could not be created (see $stockErrors)
     */
    public function create(int $idUser, array $items, int $total, int $idAddressDelivery, int $idAddressPostal):int{
        $this->stockErrors = array();
        if($idUser < 1 || !count($items)){
            return 0;
        }

        $started = false;
        try{
            self::$db->exec('BEGIN IMMEDIATE');
            $started = true;

            // 1. Stock: taken out only if there is enough, in one statement
            $take = self::$db->prepare("UPDATE `product` SET `stock` = `stock` - :quantity WHERE `id_product` = :id_product AND `stock` >= :quantity");
            $left = self::$db->prepare("SELECT `stock` FROM `product` WHERE `id_product` = :id_product");
            foreach($items as $item){
                $take->execute(array(':quantity' => (int)$item['quantity'], ':id_product' => (int)$item['id_product']));
                if($take->rowCount() == 0){
                    $left->execute(array(':id_product' => (int)$item['id_product']));
                    $this->stockErrors[(int)$item['id_product']] = array('name' => (string)$item['name'], 'stock' => max(0, (int)$left->fetchColumn()));
                }
            }
            if(count($this->stockErrors)){
                self::$db->exec('ROLLBACK');
                return 0;
            }

            // 2. The order, its stock is taken out
            $q = self::$db->prepare("INSERT INTO `customer_order` (id_user, id_address_delivery, id_address_postal, total_amount, status, stock_reduced)
                                     VALUES (:id_user, :id_address_delivery, :id_address_postal, :total_amount, :status, 1)");
            $q->execute(array(':id_user' => $idUser, ':id_address_delivery' => $idAddressDelivery, ':id_address_postal' => $idAddressPostal, ':total_amount' => $total, ':status' => self::STATUS_NEW));
            $this->id_customer_order = (int)self::$db->lastInsertId();

            // 3. The lines, with their product (to put the stock back)
            $q = self::$db->prepare("INSERT INTO `customer_order_item` (id_customer_order, id_product_variant, id_product, product_name, sku, quantity, unit_price_amount, discount_amount, tax_amount, line_total_amount)
                                     VALUES (:id_customer_order, NULL, :id_product, :product_name, :sku, :quantity, :unit_price_amount, 0, :tax_amount, :line_total_amount)");
            foreach($items as $item){
                $q->execute(array(
                    ':id_customer_order' => $this->id_customer_order,
                    ':id_product' => (int)$item['id_product'],
                    ':product_name' => substr($item['name'], 0, 200),
                    ':sku' => substr($item['sku'], 0, 100),
                    ':quantity' => $item['quantity'],
                    ':unit_price_amount' => $item['unit_price_amount'],
                    ':tax_amount' => $item['tax_amount'],
                    ':line_total_amount' => $item['line_total_amount'],
                ));
            }

            self::$db->exec('COMMIT');
            return $this->id_customer_order;
        }
        catch(\Throwable $e){
            if($started){
                try{ self::$db->exec('ROLLBACK'); } catch(\Throwable $ignored){}
            }
            error_log('Order: '.$e->getMessage());
            $this->id_customer_order = 0;
            return 0;
        }
    }


    /**
     * Change the status of an order. Cancelling it puts the quantities of its lines back in the stock, once;
     * a cancelled order cannot change anymore.
     * @return string '' when it is done, otherwise the error to display
     */
    public function setStatus(int $idCustomerOrder, string $status):string{
        $this->restocked = false;
        if(!isset(self::STATUSES[$status])){
            return T_ORDER_STATUS_INVALID;
        }

        $started = false;
        try{
            self::$db->exec('BEGIN IMMEDIATE');
            $started = true;

            $q = self::$db->prepare("SELECT `status`, `stock_reduced` FROM `customer_order` WHERE `id_customer_order` = :id");
            $q->execute(array(':id' => $idCustomerOrder));
            $row = $q->fetch(\PDO::FETCH_ASSOC);

            if(!$row || $row['status'] == $status || $row['status'] == self::STATUS_CANCELLED){
                self::$db->exec('ROLLBACK');
                if(!$row) return T_ORDER_NOT_FOUND;
                return $row['status'] == $status ? '' : T_ORDER_CANCELLED_FINAL;
            }

            $stockReduced = (int)$row['stock_reduced'];
            if($status == self::STATUS_CANCELLED && $stockReduced == 1){
                // The quantities of the lines go back to their product (a deleted product is skipped)
                self::$db->prepare("UPDATE `product` SET `stock` = `stock` + (
                                        SELECT SUM(i.`quantity`) FROM `customer_order_item` i
                                        WHERE i.`id_customer_order` = :id AND i.`id_product` = `product`.`id_product`)
                                    WHERE `id_product` IN (SELECT `id_product` FROM `customer_order_item` WHERE `id_customer_order` = :id AND `id_product` IS NOT NULL)")
                         ->execute(array(':id' => $idCustomerOrder));
                $stockReduced = 0;
            }

            self::$db->prepare("UPDATE `customer_order` SET `status` = :status, `stock_reduced` = :stock_reduced WHERE `id_customer_order` = :id")
                     ->execute(array(':status' => $status, ':stock_reduced' => $stockReduced, ':id' => $idCustomerOrder));

            self::$db->exec('COMMIT');
            $this->restocked = $stockReduced != (int)$row['stock_reduced'];
            return '';
        }
        catch(\Throwable $e){
            if($started){
                try{ self::$db->exec('ROLLBACK'); } catch(\Throwable $ignored){}
            }
            error_log('Order status: '.$e->getMessage());
            return T_ORDER_STATUS_NOT_SAVED;
        }
    }


    /**
     * Load an order and its lines, only if it belongs to the user
     * @return int 0 or 1
     */
    public function getForUser(int $idCustomerOrder, int $idUser):int{
        $q = self::$db->prepare("SELECT `id_customer_order`, `id_user`, `id_address_delivery`, `id_address_postal`, `total_amount`, `status`, `created_at`
                                 FROM `customer_order` WHERE `id_customer_order` = :id AND `id_user` = :id_user");
        $q->execute(array(':id' => $idCustomerOrder, ':id_user' => $idUser));
        $row = $q->fetch(\PDO::FETCH_ASSOC);
        if(!$row){
            return 0;
        }
        $this->order = $row;
        $this->id_customer_order = (int)$row['id_customer_order'];

        $q = self::$db->prepare("SELECT `id_product`, `product_name`, `sku`, `quantity`, `unit_price_amount`, `tax_amount`, `line_total_amount`
                                 FROM `customer_order_item` WHERE `id_customer_order` = :id ORDER BY `id_customer_order_item`");
        $q->execute(array(':id' => $this->id_customer_order));
        $this->items = $q->fetchAll(\PDO::FETCH_ASSOC);
        return 1;
    }
}
