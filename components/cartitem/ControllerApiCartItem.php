<?php
/**
 * API of the component cartitem: same operations as ControllerCartItem, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\CartItem;

class ControllerApiCartItem extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelCartItem::class;
    const PK = array('id_cart_item');
    const PK_AUTO = false;
    const SOFT_DELETE = false;
    const AUTO_DATES = array('created_at', 'updated_at');

    // Fields saved by ModelCartItem
    const FIELDS = array(
        'id_cart_item'         => array('type' => 'int', 'null' => false, 'required' => true),
        'id_user'              => array('type' => 'int', 'null' => false, 'required' => true),
        'cookie_id'            => array('type' => 'string', 'null' => true, 'required' => false, 'max' => 255),
        'quantity'             => array('type' => 'int', 'null' => false, 'required' => false, 'default' => 1),
    );

    // All the fields of the table `cart_item`
    public static array $fieldsNames = array('id_cart_item', 'id_user', 'cookie_id', 'id_product', 'id_product_variant', 'quantity', 'created_at', 'updated_at');

}
