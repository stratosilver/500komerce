<?php
/**
 * Created by apgenic.com
 * Date: 12-02-2026
 * Time: 14:45
 * Project: {PROJECT_NAME}
 */

session_start();
ini_set('display_errors', 'On');
error_reporting(E_ALL ^ E_NOTICE);

require_once('config.php');
// Texts of the interface in the chosen language: constants T_...
require_once('translations.php');
require_once('autoload.php');

// CSRF protection
// ------------------------------------------------------------------------------------------------
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE', 'PATCH'])) {
    $csrf_token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? null;

    if ($csrf_token == null ||
        !hash_equals($_SESSION['csrf_token'], $csrf_token)){
        die(T_INVALID_TOKEN);
    }
}


// Routing: clean urls of the front-end, back office under /admin/ (classes/Url.php)
// ------------------------------------------------------------------------------------------------
$route = Apgenic\Classes\Url::route();

if($route == 'notfound'){
    $errorMessage = '';
    include '404.php';
    exit;
}

// Dispatch component
// ------------------------------------------------------------------------------------------------
if(isset($_GET['component']) && is_string($_GET['component']) && $_GET['component'] != ''){
    define('COMPONENT', $_GET['component']);
}
else{
    // Home page: the products, for everybody. Home of the back office: /admin/
    define('COMPONENT', $route == 'admin' ? Apgenic\Classes\Url::ADMIN_HOME : 'product');
    if($route != 'admin'){
        $_GET['task'] = 'viewlist';
    }
}

// Access control (classes/Auth.php)
// ------------------------------------------------------------------------------------------------
$task = isset($_GET['task']) && is_string($_GET['task']) ? $_GET['task'] : '';

// Default page of a component: in the back office the edit list, otherwise the page of the front-end
if(!isset($_GET['task'])){
    if(COMPONENT == 'shop'){
        $task = 'cart';
    }
    elseif(COMPONENT == 'accesscode'){
        $task = 'code';
    }
    elseif(COMPONENT == 'blog'){
        $task = 'viewlist';
    }
    elseif($route == 'admin' || Apgenic\Classes\Auth::can(COMPONENT, 'editlist')){
        $task = 'editlist';
    }
    else{
        $task = COMPONENT == 'user' ? 'profile' : 'viewlist';
    }
    $_GET['task'] = $task;
}

// Each page has one address: the old addresses (index.php?component=...) and the views asked under /admin/
// (the views are only in the front-end) are sent to the address of the page
if($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_SERVER['HTTP_HX_REQUEST'])
    && ($route == 'legacy' || ($route == 'admin' && Apgenic\Classes\Url::isFront(COMPONENT, $task)))){
    $params = $_GET;
    unset($params['component'], $params['task']);
    $target = Apgenic\Classes\Url::to(COMPONENT, $task, $params);
    // A page without clean url keeps its address
    if(strpos($target, BASE_URL.'/index.php') !== 0){
        header('Location: '.$target, true, 301);
        exit;
    }
}

// The public tasks (sign in, products, cart, checkout) are allowed to everybody, see Auth::PUBLIC_TASKS
if(!Apgenic\Classes\Auth::can(COMPONENT, $task)){

    // 1. Authentication: all the other pages need a logged user.
    //    A page of the back office is opened again after the sign in, the front-end goes to the home page.
    if(!Apgenic\Classes\Auth::isLogged()){
        $return = Apgenic\Classes\Url::adminReturn();
        $loginUrl = Apgenic\Classes\Url::to('user', 'login', array('return' => $return));
        header((isset($_SERVER['HTTP_HX_REQUEST']) ? 'HX-Redirect: ' : 'Location: ').$loginUrl);
        exit;
    }

    // 2. Authorization: this task is not allowed with the permissions of the user
    http_response_code(403);
    $oView = new Apgenic\Classes\ViewTemplate();
    $denied = array('type' => 'danger', 'text' => T_ACCESS_DENIED_TEXT);
    if($_SERVER['PHP_SELF'] == '/htmx.php'){
        $oView->message($denied);
    }
    else{
        $oView->header(T_ACCESS_DENIED);
        $oView->message($denied);
        $oView->footer();
    }
    exit;
}

try{
    switch(COMPONENT){

            case 'media':    
                $oCtrl = new Apgenic\Media\ControllerMedia();
                break;
            
            case 'user':    
                $oCtrl = new Apgenic\User\ControllerUser();
                break;
            
            case 'post':    
                $oCtrl = new Apgenic\Post\ControllerPost();
                break;
            
            case 'product':    
                $oCtrl = new Apgenic\Product\ControllerProduct();
                break;
            
            case 'productmedia':    
                $oCtrl = new Apgenic\Productmedia\ControllerProductmedia();
                break;
            
            case 'category':    
                $oCtrl = new Apgenic\Category\ControllerCategory();
                break;
            
            case 'productcategory':    
                $oCtrl = new Apgenic\Productcategory\ControllerProductcategory();
                break;
            
            case 'translation':    
                $oCtrl = new Apgenic\Translation\ControllerTranslation();
                break;
            
            case 'productvariant':    
                $oCtrl = new Apgenic\Productvariant\ControllerProductvariant();
                break;
            
            case 'cartitem':    
                $oCtrl = new Apgenic\Cartitem\ControllerCartitem();
                break;
            
            case 'customerorder':    
                $oCtrl = new Apgenic\Customerorder\ControllerCustomerorder();
                break;
            
            case 'customerorderitem':    
                $oCtrl = new Apgenic\Customerorderitem\ControllerCustomerorderitem();
                break;
            
            case 'discount':    
                $oCtrl = new Apgenic\Discount\ControllerDiscount();
                break;

            case 'shop':
                $oCtrl = new Apgenic\Shop\ControllerShop();
                break;

            case 'accesscode':
                $oCtrl = new Apgenic\Accesscode\ControllerAccesscode();
                break;

            case 'blog':
                $oCtrl = new Apgenic\Blog\ControllerBlog();
                break;

            default:
                throw new ErrorException(T_PAGE_NOT_FOUND, 404);
    }
    $oCtrl->render();
}
catch (exception $e){
    // Displayed by 404.php, in the layout of the site
    $errorMessage = $e->getMessage();
    include '404.php';
}

