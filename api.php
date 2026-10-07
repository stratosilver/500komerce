<?php
/**
 * Entry point of the JSON API, called by public/api.php.
 * Each component has a ControllerApiXxx next to its ControllerXxx: same model, no view.
 *
 * URL
 *   /api.php?component=<component>[&task=<task>][&<primary key>=<id>]
 *
 * TASKS (the task can be omitted, it is then deduced from the HTTP method)
 *   list         GET           rows, not logically deleted               default for GET without key
 *   view         GET           one row                                   default for GET with key
 *   add          POST          create a row                              default for POST
 *   edit         POST/PUT/PATCH update a row, only the fields sent       default for PUT, PATCH
 *   del          POST/DELETE   delete a row for good                     default for DELETE
 *   logicaldel   POST/DELETE   move a row to the trash   (components with deleted_at)
 *   undel        POST/PUT/PATCH restore a row            (components with deleted_at)
 *   trashedlist  GET           rows in the trash         (components with deleted_at)
 *   user only:   login, logout, me, register, forgot, reset
 *
 * LIST PARAMETERS
 *   filters[field]=value   exact value of a field, ex: filters[id_product]=3 (the "childlist" of the web interface)
 *   text_search=abc        search in the text fields
 *   orderBy=field&order=asc|desc
 *   page=1&limit=10        limit: 200 maximum
 *
 * BODY of add / edit: JSON (Content-Type: application/json) or form fields.
 *
 * ACCESS, one of:
 *   - header "Authorization: Bearer <API_TOKEN>" (or "X-Api-Key: <API_TOKEN>"), API_TOKEN is set in config.php
 *   - the session cookie received from component=user&task=login; the requests that change something
 *     must then send the csrf_token of the login answer in the header "X-CSRF-Token".
 *     The session has the permissions of the user (classes/Auth.php): with 0, only list, view and user/me.
 *     The API token has all the rights.
 *
 * ANSWER
 *   {"success":true,"data":{...}}                                  one row
 *   {"success":true,"data":[...],"meta":{"total":..,"page":..,"limit":..,"pages":..}}   list
 *   {"success":false,"error":{"code":422,"message":"...","fields":{"name":"Required"}}}
 *   The HTTP status is the error code: 400, 401, 403, 404, 405, 409, 422, 429, 500.
 */

session_start();

require_once('config.php');
require_once('autoload.php');

// The errors must not break the JSON: they go to the error log of PHP
ini_set('display_errors', 'Off');

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Dispatch component
// ------------------------------------------------------------------------------------------------
$apiControllers = array(
    'media'             => 'Apgenic\Media\ControllerApiMedia',
    'user'              => 'Apgenic\User\ControllerApiUser',
    'post'              => 'Apgenic\Post\ControllerApiPost',
    'product'           => 'Apgenic\Product\ControllerApiProduct',
    'productmedia'      => 'Apgenic\ProductMedia\ControllerApiProductMedia',
    'category'          => 'Apgenic\Category\ControllerApiCategory',
    'productcategory'   => 'Apgenic\ProductCategory\ControllerApiProductCategory',
    'translation'       => 'Apgenic\Translation\ControllerApiTranslation',
    'productvariant'    => 'Apgenic\ProductVariant\ControllerApiProductVariant',
    'cartitem'          => 'Apgenic\CartItem\ControllerApiCartItem',
    'customerorder'     => 'Apgenic\CustomerOrder\ControllerApiCustomerOrder',
    'customerorderitem' => 'Apgenic\CustomerOrderItem\ControllerApiCustomerOrderItem',
    'discount'          => 'Apgenic\Discount\ControllerApiDiscount',
);

$component = isset($_GET['component']) && is_string($_GET['component']) ? strtolower($_GET['component']) : '';

if (!isset($apiControllers[$component])) {
    Apgenic\Classes\ControllerApi::send(404, array(
        'success' => false,
        'error' => array('code' => 404, 'message' => 'Unknown component', 'components' => array_keys($apiControllers)),
    ));
    exit;
}

define('COMPONENT', $component);

$oCtrl = new $apiControllers[$component]();
$oCtrl->render();
