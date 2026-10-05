<?php
/**
 * API of the component customerorderitem: same operations as ControllerCustomerOrderItem, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\CustomerOrderItem;

class ControllerApiCustomerOrderItem extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelCustomerOrderItem::class;
    const PK = array('id_customer_order_item');
    const PK_AUTO = true;
    const SOFT_DELETE = false;
    const AUTO_DATES = array();

    // Fields saved by ModelCustomerOrderItem
    const FIELDS = array(
        'id_customer_order'    => array('type' => 'int', 'null' => false, 'required' => true),
        'id_product_variant'   => array('type' => 'int', 'null' => true, 'required' => false),
        'product_name'         => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 200),
        'sku'                  => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 100),
        'quantity'             => array('type' => 'int', 'null' => false, 'required' => true),
        'unit_price_amount'    => array('type' => 'int', 'null' => false, 'required' => true),
        'discount_amount'      => array('type' => 'int', 'null' => false, 'required' => false, 'default' => 0),
        'tax_amount'           => array('type' => 'int', 'null' => false, 'required' => false, 'default' => 0),
        'line_total_amount'    => array('type' => 'int', 'null' => false, 'required' => true),
    );

    // All the fields of the table `customer_order_item`
    public static array $fieldsNames = array('id_customer_order_item', 'id_customer_order', 'id_product_variant', 'product_name', 'sku', 'quantity', 'unit_price_amount', 'discount_amount', 'tax_amount', 'line_total_amount', 'created_at');

}
