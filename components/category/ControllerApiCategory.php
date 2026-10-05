<?php
/**
 * API of the component category: same operations as ControllerCategory, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\Category;

class ControllerApiCategory extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelCategory::class;
    const PK = array('id_category');
    const PK_AUTO = true;
    const SOFT_DELETE = true;
    const AUTO_DATES = array();

    // Fields saved by ModelCategory
    const FIELDS = array(
        'id_parent'            => array('type' => 'int', 'null' => true, 'required' => false),
        'name'                 => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 200),
        'slug'                 => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 200),
        'position'             => array('type' => 'int', 'null' => false, 'required' => false, 'default' => 0),
    );

    // All the fields of the table `category`
    public static array $fieldsNames = array('id_category', 'id_parent', 'name', 'slug', 'position', 'created_at', 'updated_at', 'deleted_at');

}
