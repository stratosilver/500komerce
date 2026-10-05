<?php
/**
 * API of the component translation: same operations as ControllerTranslation, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\Translation;

class ControllerApiTranslation extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelTranslation::class;
    const PK = array('id_translation');
    const PK_AUTO = true;
    const SOFT_DELETE = true;
    const AUTO_DATES = array();

    // Fields saved by ModelTranslation
    const FIELDS = array(
        'text_key'             => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 100),
        'lang'                 => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 3),
        'text'                 => array('type' => 'string', 'null' => false, 'required' => true),
    );

    // All the fields of the table `translation`
    public static array $fieldsNames = array('id_translation', 'text_key', 'lang', 'text', 'created_at', 'updated_at', 'deleted_at');

}
