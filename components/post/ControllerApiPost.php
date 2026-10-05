<?php
/**
 * API of the component post: same operations as ControllerPost, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\Post;

class ControllerApiPost extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelPost::class;
    const PK = array('id_post');
    const PK_AUTO = true;
    const SOFT_DELETE = true;
    const AUTO_DATES = array();

    // Fields saved by ModelPost
    const FIELDS = array(
        'id_user'              => array('type' => 'int', 'null' => true, 'required' => false),
        'lang'                 => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 3),
        'title'                => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 200),
        'id_media'             => array('type' => 'int', 'null' => true, 'required' => false),
        'slug'                 => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 200),
        'excerpt'              => array('type' => 'string', 'null' => true, 'required' => false, 'max' => 500),
        'body'                 => array('type' => 'string', 'null' => false, 'required' => true),
        'status'               => array('type' => 'string', 'null' => false, 'required' => false, 'default' => 'draft', 'enum' => array('draft', 'published', 'archived')),
        'published_at'         => array('type' => 'string', 'null' => true, 'required' => false),
    );

    // All the fields of the table `post`
    public static array $fieldsNames = array('id_post', 'id_user', 'lang', 'title', 'id_media', 'slug', 'excerpt', 'body', 'status', 'published_at', 'created_at', 'updated_at', 'deleted_at');

}
