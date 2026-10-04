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
    define('COMPONENT', 'discount');
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
            
    }
    $oCtrl->render();
}
catch (exception $e){
    echo $e->getMessage();
    include '404.php';
}

