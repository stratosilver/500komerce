<?php
/**
 * Shop: the cart, the checkout and the confirmation of the order.
 *
 * Tasks
 *   cart          the cart                                              public
 *   add           POST id_product, quantity: add a product to the cart  public
 *   update        POST quantity[id_cart_item], remove: change the cart  public
 *   checkout      personal data and addresses, creates the order       public (sign in or account creation on the page)
 *   login         POST email, password: sign in from the checkout page public
 *   confirmation  the order that has just been created                 logged user, his own orders only
 * The rights are in \Apgenic\Classes\Auth, checked by index.php.
 */

declare(strict_types=1);
namespace Apgenic\Shop;

use Apgenic\Classes\Auth;
use Apgenic\Address\ModelAddress;
use Apgenic\User\ModelUser;
use Apgenic\User\ControllerUser;

class ControllerShop {

    private ViewShop $HTMLShop;
    private ModelCart $dataCart;
    private array $message = array();


    function render(){
        $this->HTMLShop = new ViewShop();
        $this->dataCart = new ModelCart();

        $task = (string)filter_var($_GET['task'] ?? 'cart', FILTER_SANITIZE_SPECIAL_CHARS);

        // Message left by the previous page (add to cart, login...)
        if(isset($_SESSION['shop_message'])){
            $this->message = $_SESSION['shop_message'];
            unset($_SESSION['shop_message']);
        }

        // The tasks that change something answer with a redirection: nothing is displayed before
        switch($task){
            case 'add':          $this->add(); break;
            case 'update':       $this->update(); break;
            case 'login':        $this->login(); break;
            case 'cart':         $this->cart(); break;
            case 'checkout':     $this->checkout(); break;
            case 'confirmation': $this->confirmation(); break;
            default: throw new \ErrorException('Page not found', 404, E_ERROR);
        }
    }


    private static function url(string $task, string $params = ''):string{
        return BASE_URL.'/index.php?component=shop&task='.$task.$params;
    }

    private static function redirect(string $url):void{
        header((isset($_SERVER['HTTP_HX_REQUEST']) ? 'HX-Redirect: ' : 'Location: ').$url);
    }

    private static function flash(string $type, string $text):void{
        $_SESSION['shop_message'] = array('type' => $type, 'text' => $text);
    }


    // Cart
    // ------------------------------------------------------------------------------------------------

    /**
     * Add a product to the cart, then display the cart
     * @return void
     */
    private function add():void{
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $idProduct = (int)filter_var($_POST['id_product'] ?? null, FILTER_VALIDATE_INT);
            $quantity = (int)filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT);

            if($idProduct > 0 && $this->dataCart->add($idProduct, max(1, $quantity))){
                self::flash('success', 'The product has been added to your cart');
            }
            else{
                self::flash('danger', 'This product cannot be added to the cart');
            }
        }
        self::redirect(self::url('cart'));
    }


    /**
     * Change the quantities, or remove a line
     * @return void
     */
    private function update():void{
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $remove = (int)filter_var($_POST['remove'] ?? null, FILTER_VALIDATE_INT);

            if($remove > 0){
                $this->dataCart->setQuantity($remove, 0);
            }
            elseif(is_array($_POST['quantity'] ?? null)){
                foreach($_POST['quantity'] as $idCartItem => $quantity){
                    $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
                    if($quantity !== false){
                        $this->dataCart->setQuantity((int)$idCartItem, (int)$quantity);
                    }
                }
            }
            self::flash('success', 'Your cart has been updated');
        }
        self::redirect(self::url('cart'));
    }


    private function cart():void{
        $this->dataCart->load();
        $this->HTMLShop->header('Cart');
        $this->HTMLShop->cart($this->dataCart, $this->message);
        $this->HTMLShop->footer();
    }


    // Checkout
    // ------------------------------------------------------------------------------------------------

    /**
     * Sign in from the checkout page, with the same rules as the login page
     * @return void
     */
    private function login():void{
        if($_SERVER['REQUEST_METHOD'] === 'POST' && !Auth::isLogged()){
            $error = ControllerUser::attemptLogin(trim((string)($_POST['email'] ?? '')), (string)($_POST['password'] ?? ''));
            if($error != ''){
                self::flash('danger', $error);
            }
        }
        self::redirect(self::url('checkout'));
    }


    /**
     * Checkout page: sign in, personal data, delivery and postal addresses.
     * The validation creates the account of a new customer, saves the addresses and creates the order.
     * @return void
     */
    private function checkout():void{
        if(!$this->dataCart->load()){
            self::flash('warning', 'Your cart is empty');
            self::redirect(self::url('cart'));
            return;
        }

        $logged = Auth::isLogged();
        $user = new ModelUser();
        $addresses = new ModelAddress();
        if($logged){
            $user->id_user = (int)$_SESSION['user']['id_user'];
            $user->get();
            $addresses->getListByUser((int)$user->id_user);
        }

        // Values displayed in the form
        $values = array(
            'first_name' => (string)$user->first_name,
            'last_name' => (string)$user->last_name,
            'email' => (string)$user->email,
            'phone' => '',
            'id_address_delivery' => 'new',
            'id_address_postal' => 'new',
            'postal_same' => 1,
            'delivery_country' => 'BE',
            'postal_country' => 'BE',
        );
        // Last address used, by type
        foreach($addresses->list as $row){
            if($values['id_address_'.$row['type']] == 'new'){
                $values['id_address_'.$row['type']] = (string)$row['id_address'];
            }
            if($values['phone'] == '' && $row['phone'] != ''){
                $values['phone'] = (string)$row['phone'];
            }
        }
        if($values['id_address_postal'] != 'new'){
            $values['postal_same'] = 0;
        }
        $errors = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->message = array();
            $idOrder = $this->placeOrder($user, $addresses, $logged, $values, $errors);
            if($idOrder > 0){
                self::redirect(self::url('confirmation', '&id_customer_order='.$idOrder));
                return;
            }
            if(!count($this->message)){
                $this->message = array('type' => 'danger', 'text' => 'Some fields are missing or not valid');
            }
            // The account or an address may have been saved before the failure
            if(isset($_SESSION['user']['id_user'])){
                $logged = true;
                $addresses->getListByUser((int)$_SESSION['user']['id_user']);
            }
        }

        $this->HTMLShop->header('Checkout');
        $this->HTMLShop->checkout($this->dataCart, $values, $errors, $addresses->list, $logged, $this->message, ControllerUser::PASSWORD_MIN_LENGTH);
        $this->HTMLShop->footer();
    }


    /**
     * Check the checkout form and create the order
     * @param array $values values of the form, completed with what has been typed
     * @param array $errors field => message, completed
     * @return int id of the order, 0 if the form is not valid or the order could not be created
     */
    private function placeOrder(ModelUser $user, ModelAddress $addresses, bool $logged, array &$values, array &$errors):int{

        // Personal data
        foreach(array('first_name' => 100, 'last_name' => 100) as $field => $max){
            $values[$field] = trim((string)filter_var($_POST[$field] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
            if($values[$field] == ''){
                $errors[$field] = 'Required';
            }
            elseif(strlen($values[$field]) > $max){
                $errors[$field] = 'Too long';
            }
        }
        $values['phone'] = trim((string)filter_var($_POST['phone'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
        if(strlen($values['phone']) > 30){
            $errors['phone'] = 'Too long';
        }

        // New customer: his account is created with the order
        $password = '';
        if(!$logged){
            $email = trim((string)($_POST['email'] ?? ''));
            $values['email'] = htmlspecialchars($email);
            $password = (string)($_POST['password'] ?? '');

            if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255){
                $errors['email'] = 'The email address is not valid';
            }
            elseif((new ModelUser())->getByEmail($email, false)){
                $errors['email'] = 'An account already exists with this email address: sign in above';
            }
            $passwordError = ControllerUser::passwordError($password, (string)($_POST['password_confirmation'] ?? ''));
            if($passwordError != ''){
                $errors['password'] = $passwordError;
            }
        }

        // Addresses: one already saved (it must belong to the user), or a new one
        $postalSame = isset($_POST['postal_same']);
        $values['postal_same'] = $postalSame ? 1 : 0;
        $new = array();
        $ids = array('delivery' => 0, 'postal' => 0);

        foreach(array('delivery', 'postal') as $type){
            if($type == 'postal' && $postalSame){
                continue;
            }
            $chosen = (string)($_POST['id_address_'.$type] ?? 'new');
            $values['id_address_'.$type] = 'new';

            if($logged && $chosen != 'new' && isset($addresses->list[(int)$chosen])){
                $ids[$type] = (int)$chosen;
                $values['id_address_'.$type] = (string)$ids[$type];
                continue;
            }

            $address = new ModelAddress();
            $address->type = $type;
            $errors += $address->fill($_POST, $type.'_');
            // The phone of the personal data is saved with the address
            $address->phone = $values['phone'];
            $new[$type] = $address;
            foreach(array_keys(ModelAddress::FIELDS) as $field){
                $values[$type.'_'.$field] = (string)$address->$field;
            }
            $values[$type.'_country'] = $address->country;
        }

        if(count($errors)){
            return 0;
        }

        // Everything is valid: account, personal data, addresses, order
        if(!$logged){
            $user->email = $email;
            $user->first_name = $values['first_name'];
            $user->last_name = $values['last_name'];
            $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
            if(!$user->register()){
                $this->message = array('type' => 'danger', 'text' => 'The account could not be created');
                return 0;
            }
            $user->get();
            ControllerUser::openSessionFor($user);
            // The cart of the visitor becomes the cart of the new account
            $this->dataCart->identify();
            $this->dataCart->load();
        }
        else{
            $user->updateProfile((string)$user->email, $values['first_name'], $values['last_name']);
            $_SESSION['user']['first_name'] = $values['first_name'];
            $_SESSION['user']['last_name'] = $values['last_name'];
        }
        $idUser = (int)$user->id_user;

        foreach($new as $type => $address){
            $address->id_user = $idUser;
            if(!$address->add()){
                $this->message = array('type' => 'danger', 'text' => 'The address could not be saved');
                return 0;
            }
            $ids[$type] = (int)$address->id_address;
            $values['id_address_'.$type] = (string)$ids[$type];
        }
        if($postalSame){
            $ids['postal'] = $ids['delivery'];
        }

        $order = new ModelOrder();
        $idOrder = $order->create($idUser, $this->dataCart->items, $this->dataCart->total, $ids['delivery'], $ids['postal']);
        if($idOrder < 1){
            $this->message = array('type' => 'danger', 'text' => 'The order could not be created, your cart has been kept');
            return 0;
        }

        $this->dataCart->clear();
        return $idOrder;
    }


    /**
     * The order that has just been created
     * @return void
     */
    private function confirmation():void{
        $order = new ModelOrder();
        $found = $order->getForUser((int)filter_var($_GET['id_customer_order'] ?? null, FILTER_VALIDATE_INT), (int)$_SESSION['user']['id_user']);

        $addresses = array();
        if($found){
            foreach(array('delivery', 'postal') as $type){
                $address = new ModelAddress();
                $address->id_address = (int)$order->order['id_address_'.$type];
                if($address->id_address > 0 && $address->get()){
                    $addresses[$type] = get_object_vars($address);
                }
            }
        }

        $this->HTMLShop->header('Order');
        $this->HTMLShop->confirmation($found ? $order : null, $addresses);
        $this->HTMLShop->footer();
    }
}
