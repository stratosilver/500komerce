<?php
/**
 * API of the component product: same operations as ControllerProduct, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\Product;

class ControllerApiProduct extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelProduct::class;
    const PK = array('id_product');
    const PK_AUTO = true;
    const SOFT_DELETE = true;
    const AUTO_DATES = array();

    // Fields saved by ModelProduct
    const FIELDS = array(
        'id_user'              => array('type' => 'int', 'null' => true, 'required' => false),
        'name'                 => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 200),
        'slug'                 => array('type' => 'string', 'null' => true, 'required' => false, 'max' => 200),
        'summary'              => array('type' => 'string', 'null' => true, 'required' => false, 'max' => 500),
        'description'          => array('type' => 'string', 'null' => true, 'required' => false),
        // Price without tax, in cents
        'price_amount'         => array('type' => 'int', 'null' => false, 'required' => false, 'default' => 0),
        'status'               => array('type' => 'string', 'null' => false, 'required' => false, 'default' => 'draft', 'enum' => array('draft', 'active', 'archived')),
        'published_at'         => array('type' => 'string', 'null' => true, 'required' => false),
    );

    // All the fields of the table `product`
    public static array $fieldsNames = array('id_product', 'id_user', 'name', 'slug', 'summary', 'description', 'price_amount', 'status', 'published_at', 'created_at', 'updated_at', 'deleted_at');

}
