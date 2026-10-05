<?php
/**
 * API of the component productmedia: same operations as ControllerProductMedia, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\ProductMedia;

class ControllerApiProductMedia extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelProductMedia::class;
    const PK = array('id_product', 'id_media');
    const PK_AUTO = false;
    const SOFT_DELETE = false;
    const AUTO_DATES = array();

    // Fields saved by ModelProductMedia
    const FIELDS = array(
        'id_product'           => array('type' => 'int', 'null' => false, 'required' => true),
        'id_media'             => array('type' => 'int', 'null' => false, 'required' => true),
        'order'                => array('type' => 'int', 'null' => false, 'required' => false, 'default' => 1),
    );

    // All the fields of the table `product_media`
    public static array $fieldsNames = array('id_product', 'id_media', 'order');

}
