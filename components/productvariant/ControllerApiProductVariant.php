<?php
/**
 * API of the component productvariant: same operations as ControllerProductVariant, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\ProductVariant;

class ControllerApiProductVariant extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelProductVariant::class;
    const PK = array('id_product_variant');
    const PK_AUTO = true;
    const SOFT_DELETE = true;
    const AUTO_DATES = array();

    // Fields saved by ModelProductVariant
    const FIELDS = array(
        'name'                 => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 45),
    );

    // All the fields of the table `product_variant`
    public static array $fieldsNames = array('id_product_variant', 'name', 'created_at', 'updated_at', 'deleted_at');

}
