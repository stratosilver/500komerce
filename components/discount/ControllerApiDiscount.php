<?php
/**
 * API of the component discount: same operations as ControllerDiscount, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\Discount;

class ControllerApiDiscount extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelDiscount::class;
    const PK = array('id_discount');
    const PK_AUTO = true;
    const SOFT_DELETE = true;
    const AUTO_DATES = array();

    // Fields saved by ModelDiscount
    const FIELDS = array(
        'date_from'            => array('type' => 'string', 'null' => true, 'required' => false),
        'date_to'              => array('type' => 'string', 'null' => true, 'required' => false),
        'id_category'          => array('type' => 'int', 'null' => true, 'required' => false),
        'id_product'           => array('type' => 'int', 'null' => true, 'required' => false),
        'amount'               => array('type' => 'int', 'null' => true, 'required' => false),
        'percentage'           => array('type' => 'int', 'null' => true, 'required' => false),
    );

    // All the fields of the table `discount`
    public static array $fieldsNames = array('id_discount', 'date_from', 'date_to', 'id_category', 'id_product', 'amount', 'percentage', 'created_at', 'updated_at', 'deleted_at');

}
