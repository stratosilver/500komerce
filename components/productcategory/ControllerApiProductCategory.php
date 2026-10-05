<?php
/**
 * API of the component productcategory: same operations as ControllerProductCategory, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\ProductCategory;

class ControllerApiProductCategory extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelProductCategory::class;
    const PK = array('id_product', 'id_category');
    const PK_AUTO = false;
    const SOFT_DELETE = false;
    const AUTO_DATES = array();

    // Fields saved by ModelProductCategory
    const FIELDS = array(
        'id_product'           => array('type' => 'int', 'null' => false, 'required' => true),
        'id_category'          => array('type' => 'int', 'null' => false, 'required' => true),
    );

    // All the fields of the table `product_category`
    public static array $fieldsNames = array('id_product', 'id_category');

}
