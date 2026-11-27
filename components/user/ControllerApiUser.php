<?php
/**
 * API of the component user: same operations as ControllerUser, same model, JSON instead of views.
 * See api.php for the way to call it.
 *
 * In addition to the common tasks: login, logout, me, register, forgot, reset.
 * The sign in with Google, Facebook, X needs a browser and stays in ControllerUser. An account is recognized by
 * its email: the one created there signs in here too, once it has a password (forgot + reset, or the profile).
 */

declare(strict_types=1);
namespace Apgenic\User;

use Apgenic\Classes\ApiException;
use Apgenic\Classes\Security;
use Apgenic\Classes\Throttle;

class ControllerApiUser extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelUser::class;
    const PK = array('id_user');
    const PK_AUTO = true;
    const SOFT_DELETE = true;
    const AUTO_DATES = array();
    // The hash of the password never leaves the server
    const HIDDEN = array('password_hash');
    const PUBLIC_TASKS = array('login', 'logout', 'register', 'forgot', 'reset');

    // Fields saved by ModelUser. The password is sent in the field "password" and stored hashed
    const FIELDS = array(
        'email'                => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 255),
        'email_verified'       => array('type' => 'int', 'null' => false, 'required' => false, 'default' => 0),
        'first_name'           => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 100),
        'last_name'            => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 100),
        'id_media'             => array('type' => 'int', 'null' => true, 'required' => false),
        'provider'             => array('type' => 'string', 'null' => false, 'required' => false, 'default' => 'local', 'max' => 50),
        'provider_user_id'     => array('type' => 'string', 'null' => true, 'required' => false, 'max' => 255),
        'status'               => array('type' => 'string', 'null' => false, 'required' => false, 'default' => 'active', 'enum' => array('active', 'suspended', 'disabled')),
        'permissions'          => array('type' => 'int', 'null' => false, 'required' => false, 'default' => 0),
        'last_login_at'        => array('type' => 'string', 'null' => true, 'required' => false),
    );

    // All the fields of the table `user`
    public static array $fieldsNames = array('id_user', 'email', 'email_verified', 'password_hash', 'first_name', 'last_name', 'id_media', 'provider', 'provider_user_id', 'status', 'permissions', 'last_login_at', 'created_at', 'updated_at', 'deleted_at');


    protected function dispatch(string $task, string $method):void{
        switch($task){
            case 'login':    self::allow($method, array('POST')); $this->login(); break;
            case 'logout':   self::allow($method, array('POST')); $this->logout(); break;
            case 'me':       self::allow($method, array('GET')); $this->me(); break;
            case 'register': self::allow($method, array('POST')); $this->register(); break;
            case 'forgot':   self::allow($method, array('POST')); $this->forgot(); break;
            case 'reset':    self::allow($method, array('POST')); $this->reset(); break;
            default: parent::dispatch($task, $method);
        }
    }


    /**
     * Email and password of add / edit
     * @return void
     */
    protected function validate(array &$errors, bool $isNew):void{
        if(!isset($errors['permissions']) && ((int)$this->data->permissions < 0 || (int)$this->data->permissions > 100)){
            $errors['permissions'] = 'Must be between 0 and 100';
        }

        if(!isset($errors['email'])){
            // value() has escaped the text: the address is checked and stored as typed, like the web interface does
            $email = trim(html_entity_decode((string)$this->data->email, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if(!Security::isValidEmail($email)){
                $errors['email'] = 'Not a valid email address';
            }
            else{
                $this->data->email = $email;
            }
        }

        // A session user cannot change his own level nor suspend his own account (same rule as the web interface)
        $self = (int)($_SESSION['user']['id_user'] ?? 0);
        if(!$isNew && $self > 0 && (int)$this->data->id_user === $self){
            $stored = new ModelUser();
            $stored->id_user = $self;
            if($stored->get()){
                $this->data->permissions = $stored->permissions;
                $this->data->status = $stored->status;
            }
        }

        if(array_key_exists('password', $this->input) && $this->input['password'] !== null && $this->input['password'] !== ''){
            $error = self::passwordError($this->input['password']);
            if($error != ''){
                $errors['password'] = $error;
            }
            else{
                $this->data->password_hash = password_hash((string)$this->input['password'], PASSWORD_DEFAULT);
            }
        }
        elseif($isNew){
            // Account without password, ex: created for a sign in with Google
            $this->data->password_hash = null;
        }
    }


    // Authentication
    // ------------------------------------------------------------------------------------------------

    /**
     * @return string error message, empty if the password is accepted
     */
    private static function passwordError($password):string{
        if(!is_string($password) || strlen($password) < ControllerUser::PASSWORD_MIN_LENGTH){
            return 'The password must contain at least '.ControllerUser::PASSWORD_MIN_LENGTH.' characters';
        }
        // bcrypt ignores what is after 72 bytes
        if(strlen($password) > 72){
            return 'The password is too long (72 characters maximum)';
        }
        return '';
    }


    /**
     * Store the user in the session, like the login page does
     * @return void
     */
    private function openSession():void{
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['user'] = array(
            'id_user' => (int)$this->data->id_user,
            'email' => $this->data->email,
            'first_name' => $this->data->first_name,
            'last_name' => $this->data->last_name,
        );
        unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
    }


    /**
     * Answer of login, register and me: the user, and the CSRF token to send back
     * in the header X-CSRF-Token of the requests that change something
     * @return void
     */
    private function sendSession(int $status):void{
        self::send($status, array('success' => true, 'data' => $this->record(), 'csrf_token' => $_SESSION['csrf_token']));
    }


    /**
     * POST email, password: open a session (cookie)
     * @return void
     */
    private function login():void{
        $email = is_string($this->input['email'] ?? null) ? trim($this->input['email']) : '';
        $password = is_string($this->input['password'] ?? null) ? $this->input['password'] : '';

        // The counters of the session are not enough: a program does not keep the cookie (classes/Throttle.php)
        if(($_SESSION['login_locked_until'] ?? 0) > time()
            || Throttle::lockedFor('login-ip', Throttle::ip()) > 0
            || Throttle::lockedFor('login-account', $email) > 0){
            throw new ApiException(429, 'Too many attempts, try again in a few minutes');
        }

        $found = filter_var($email, FILTER_VALIDATE_EMAIL) ? $this->data->getByEmail($email) : 0;

        // Always verify a hash, so the response time does not tell if the email exists
        $hash = $found && $this->data->password_hash ? $this->data->password_hash : ControllerUser::DUMMY_HASH;
        $passwordOk = password_verify($password, $hash);

        // Any account having a password, also the ones created with Google, Facebook, X
        if($found && $this->data->password_hash && $passwordOk && $this->data->status == 'active'){
            if(password_needs_rehash($this->data->password_hash, PASSWORD_DEFAULT)){
                $this->data->updatePassword(password_hash($password, PASSWORD_DEFAULT));
            }
            $this->data->touchLastLogin();
            $this->openSession();
            Throttle::clear('login-account', $email);
            $this->data->get();
            $this->sendSession(200);
            return;
        }

        Throttle::hit('login-ip', Throttle::ip(), ControllerUser::LOGIN_MAX_BY_IP, ControllerUser::LOGIN_WINDOW, ControllerUser::LOGIN_WINDOW);
        if($email != ''){
            Throttle::hit('login-account', $email, ControllerUser::LOGIN_MAX_BY_ACCOUNT, ControllerUser::LOGIN_WINDOW, ControllerUser::LOGIN_WINDOW);
        }
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if($_SESSION['login_attempts'] >= ControllerUser::MAX_LOGIN_ATTEMPTS){
            $_SESSION['login_locked_until'] = time() + ControllerUser::LOGIN_LOCK_TIME;
            $_SESSION['login_attempts'] = 0;
        }
        // Same message whatever the reason
        throw new ApiException(401, 'Wrong email or password');
    }


    /**
     * POST: close the session
     * @return void
     */
    private function logout():void{
        $_SESSION = array();
        if(ini_get('session.use_cookies')){
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        self::send(200, array('success' => true));
    }


    /**
     * GET: the user of the session
     * @return void
     */
    private function me():void{
        if(!isset($_SESSION['user']['id_user'])){
            throw new ApiException(404, 'No user session, the request is authenticated with the API token');
        }
        $this->data->id_user = (int)$_SESSION['user']['id_user'];
        if(!$this->data->get()){
            throw new ApiException(404, 'Not found');
        }
        $this->sendSession(200);
    }


    /**
     * POST email, first_name, last_name, password: create a local account and open a session
     * @return void
     */
    private function register():void{
        $errors = array();
        $values = array();
        foreach(array('email', 'first_name', 'last_name') as $field){
            $spec = static::FIELDS[$field];
            $values[$field] = $this->value($field, $spec, is_string($this->input[$field] ?? null) ? trim($this->input[$field]) : null, $errors);
        }
        // The address is stored as typed (like the web interface): it must not have characters of HTML
        $email = is_string($this->input['email'] ?? null) ? trim($this->input['email']) : '';
        if(!isset($errors['email']) && !Security::isValidEmail($email)){
            $errors['email'] = 'Not a valid email address';
        }
        $error = self::passwordError($this->input['password'] ?? null);
        if($error != ''){
            $errors['password'] = $error;
        }
        if(count($errors)){
            throw new ApiException(422, 'Validation failed', array('fields' => $errors));
        }

        // The email is unique in the table, logically deleted users included
        if($this->data->getByEmail($email, false)){
            throw new ApiException(409, 'An account already exists with this email address');
        }
        if(Throttle::hit('register-ip', Throttle::ip(), ControllerUser::REGISTER_MAX_BY_IP, 3600, 3600) > 0){
            throw new ApiException(429, 'Too many attempts, try again later');
        }

        $this->data = new ModelUser();
        $this->data->email = $email;
        $this->data->first_name = $values['first_name'];
        $this->data->last_name = $values['last_name'];
        $this->data->password_hash = password_hash($this->input['password'], PASSWORD_DEFAULT);

        if(!$this->data->register()){
            throw new ApiException(500, 'The account could not be created');
        }
        $this->openSession();
        $this->data->get();
        $this->sendSession(201);
    }


    /**
     * POST email: send by email a link to choose a new password
     * @return void
     */
    private function forgot():void{
        $email = is_string($this->input['email'] ?? null) ? trim($this->input['email']) : '';
        $answer = array('success' => true, 'message' => 'If an account exists with this email address, a link to choose a new password has been sent');

        if(Throttle::hit('forgot-ip', Throttle::ip(), ControllerUser::FORGOT_MAX_BY_IP, 3600, 3600) > 0){
            throw new ApiException(429, 'Too many attempts, try again later');
        }

        if(Security::isValidEmail($email)
            && $this->data->getByEmail($email)
            && $this->data->status == 'active'
            && Throttle::hit('forgot-account', $email, ControllerUser::FORGOT_MAX_BY_ACCOUNT + 1, 3600, 3600) == 0){

            $token = $this->data->createPasswordResetToken(ControllerUser::RESET_TOKEN_VALIDITY);

            // The link opens the "new password" page of the web interface. Never built with the Host header of
            // the request outside of a development host: APP_URL is required (see Security::absoluteUrl)
            $link = Security::absoluteUrl(\Apgenic\Classes\Url::to('user', 'reset', array('token' => $token)));

            $body  = "Hello,\r\n\r\n";
            $body .= "Use the following link to choose a new password, it is valid for ".ControllerUser::RESET_TOKEN_VALIDITY." minutes:\r\n\r\n";
            $body .= $link."\r\n\r\n";
            $body .= "If you did not ask for a new password, ignore this email.\r\n";
            $headers = "Content-Type: text/plain; charset=utf-8\r\n";
            if(defined('MAIL_FROM') && MAIL_FROM != ''){
                $headers .= 'From: '.MAIL_FROM."\r\n";
            }
            if($link != ''){
                @mail($this->data->email, 'Reset your password', $body, $headers);
            }

            // DEVELOPMENT ONLY, see config.php: never in the answer of a request that does not come from the server itself
            if(Security::showDevLinks()){
                $answer['dev'] = array('token' => $token, 'link' => $link);
            }
        }

        // Same answer if the email is unknown, to not reveal which emails have an account
        self::send(200, $answer);
    }


    /**
     * POST token, password: choose a new password with the token received by email
     * @return void
     */
    private function reset():void{
        if(!$this->data->getByPasswordResetToken(is_string($this->input['token'] ?? null) ? $this->input['token'] : '')){
            throw new ApiException(400, 'This token is not valid or has expired');
        }
        $error = self::passwordError($this->input['password'] ?? null);
        if($error != ''){
            throw new ApiException(422, 'Validation failed', array('fields' => array('password' => $error)));
        }

        $this->data->updatePassword(password_hash($this->input['password'], PASSWORD_DEFAULT));
        // The token can be used only once
        $this->data->deletePasswordResetTokens();

        self::send(200, array('success' => true, 'message' => 'The password has been changed'));
    }

}
