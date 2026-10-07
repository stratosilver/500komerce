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
        die('Invalid Token');
    }
}


// Dispatch component
// ------------------------------------------------------------------------------------------------
if(isset($_GET['component']) && $_GET['component'] != ''){
    define('COMPONENT', $_GET['component']);
}
else{
    // Home page: the products for the visitors and the customers
    define('COMPONENT', Apgenic\Classes\Auth::canEdit() ? 'discount' : 'product');
}

// Access control (classes/Auth.php)
// ------------------------------------------------------------------------------------------------
$task = isset($_GET['task']) && is_string($_GET['task']) ? $_GET['task'] : '';

// Default page of a component: the edit list, or the consultation list for the users who cannot edit
if(!isset($_GET['task'])){
    if(COMPONENT == 'shop'){
        $task = 'cart';
    }
    elseif(Apgenic\Classes\Auth::can(COMPONENT, 'editlist')){
        $task = 'editlist';
    }
    else{
        $task = COMPONENT == 'user' ? 'profile' : 'viewlist';
    }
    $_GET['task'] = $task;
}

// The public tasks (sign in, products, cart, checkout) are allowed to everybody, see Auth::PUBLIC_TASKS
if(!Apgenic\Classes\Auth::can(COMPONENT, $task)){

    // 1. Authentication: all the other pages need a logged user
    if(!Apgenic\Classes\Auth::isLogged()){
        $loginUrl = BASE_URL.'/index.php?component=user&task=login';
        header((isset($_SERVER['HTTP_HX_REQUEST']) ? 'HX-Redirect: ' : 'Location: ').$loginUrl);
        exit;
    }

    // 2. Authorization: this task is not allowed with the permissions of the user
    http_response_code(403);
    $oView = new Apgenic\Classes\ViewTemplate();
    $denied = array('type' => 'danger', 'text' => 'Access denied: your permissions do not allow this page');
    if($_SERVER['PHP_SELF'] == '/htmx.php'){
        $oView->message($denied);
    }
    else{
        $oView->header('Access denied');
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
            
    }
    $oCtrl->render();
}
catch (exception $e){
    echo $e->getMessage();
    include '404.php';
}

