<?php
/**
 * API of the component media: same operations as ControllerMedia, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\Media;

class ControllerApiMedia extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelMedia::class;
    const PK = array('id_media');
    const PK_AUTO = true;
    const SOFT_DELETE = true;
    const AUTO_DATES = array();

    // Fields saved by ModelMedia
    const FIELDS = array(
        'filename'             => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 255),
        'mime_type'            => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 100),
        'size_bytes'           => array('type' => 'int', 'null' => true, 'required' => false),
        'width'                => array('type' => 'int', 'null' => true, 'required' => false),
        'height'               => array('type' => 'int', 'null' => true, 'required' => false),
        'alt_text'             => array('type' => 'string', 'null' => true, 'required' => false, 'max' => 500),
        'caption'              => array('type' => 'string', 'null' => true, 'required' => false),
    );

    // All the fields of the table `media`
    public static array $fieldsNames = array('id_media', 'filename', 'mime_type', 'size_bytes', 'width', 'height', 'alt_text', 'caption', 'created_at', 'updated_at', 'deleted_at');

}
