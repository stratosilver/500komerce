<?php
/**
 * Order created by the checkout: one row in customer_order, one row in customer_order_item by line of the cart.
 * The name and the price of the products are copied in the order: it does not change when a product does.
 */

declare(strict_types=1);
namespace Apgenic\Shop;

class ModelOrder extends \Apgenic\Classes\Model{

    public int $id_customer_order = 0;
    /** Filled by getForUser() */
    public array $order = array();
    public array $items = array();


    /**
     * Create the order of a user with the lines of his cart. All or nothing (transaction).
     * @param array $items lines of ModelCart::load()
     * @param int $total total of the order, cents, tax included
     * @return int id of the order, 0 if it could not be created
     */
    public function create(int $idUser, array $items, int $total, int $idAddressDelivery, int $idAddressPostal):int{
        if($idUser < 1 || !count($items)){
            return 0;
        }

        try{
            self::$db->beginTransaction();

            $q = self::$db->prepare("INSERT INTO `customer_order` (id_user, id_address_delivery, id_address_postal, total_amount)
                                     VALUES (:id_user, :id_address_delivery, :id_address_postal, :total_amount)");
            $q->execute(array(':id_user' => $idUser, ':id_address_delivery' => $idAddressDelivery, ':id_address_postal' => $idAddressPostal, ':total_amount' => $total));
            $this->id_customer_order = (int)self::$db->lastInsertId();

            $q = self::$db->prepare("INSERT INTO `customer_order_item` (id_customer_order, id_product_variant, product_name, sku, quantity, unit_price_amount, discount_amount, tax_amount, line_total_amount)
                                     VALUES (:id_customer_order, NULL, :product_name, :sku, :quantity, :unit_price_amount, 0, :tax_amount, :line_total_amount)");
            foreach($items as $item){
                $q->execute(array(
                    ':id_customer_order' => $this->id_customer_order,
                    ':product_name' => substr($item['name'], 0, 200),
                    ':sku' => substr($item['sku'], 0, 100),
                    ':quantity' => $item['quantity'],
                    ':unit_price_amount' => $item['unit_price_amount'],
                    ':tax_amount' => $item['tax_amount'],
                    ':line_total_amount' => $item['line_total_amount'],
                ));
            }

            self::$db->commit();
            return $this->id_customer_order;
        }
        catch(\Throwable $e){
            if(self::$db->inTransaction()){
                self::$db->rollBack();
            }
            error_log('Order: '.$e->getMessage());
            $this->id_customer_order = 0;
            return 0;
        }
    }


    /**
     * Load an order and its lines, only if it belongs to the user
     * @return int 0 or 1
     */
    public function getForUser(int $idCustomerOrder, int $idUser):int{
        $q = self::$db->prepare("SELECT `id_customer_order`, `id_user`, `id_address_delivery`, `id_address_postal`, `total_amount`, `created_at`
                                 FROM `customer_order` WHERE `id_customer_order` = :id AND `id_user` = :id_user");
        $q->execute(array(':id' => $idCustomerOrder, ':id_user' => $idUser));
        $row = $q->fetch(\PDO::FETCH_ASSOC);
        if(!$row){
            return 0;
        }
        $this->order = $row;
        $this->id_customer_order = (int)$row['id_customer_order'];

        $q = self::$db->prepare("SELECT `product_name`, `sku`, `quantity`, `unit_price_amount`, `tax_amount`, `line_total_amount`
                                 FROM `customer_order_item` WHERE `id_customer_order` = :id ORDER BY `id_customer_order_item`");
        $q->execute(array(':id' => $this->id_customer_order));
        $this->items = $q->fetchAll(\PDO::FETCH_ASSOC);
        return 1;
    }
}
