<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\User;


class ControllerUser  extends \Apgenic\Classes\Controller {

    private ViewUser $HTMLUser;
    private ModelUser $dataUser;

    // Accepted sort fields
    // Tasks reachable without being logged
    const AUTH_TASKS = array('login', 'logout', 'register', 'forgot', 'reset', 'oauth', 'oauthcallback');
    const PASSWORD_MIN_LENGTH = 8;
    // Validity of the password reset link, in minutes
    const RESET_TOKEN_VALIDITY = 60;
    // After MAX_LOGIN_ATTEMPTS failures, the session must wait LOGIN_LOCK_TIME seconds
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOGIN_LOCK_TIME = 300;
    // Hash of a random password, verified when the email is unknown
    const DUMMY_HASH = '$2y$10$.TFZJHB6cdQ8NHbxJYcxpOp2aU6r5B.ua6EBckC3vn8akeazPUFmi';

    // External identity providers (OAuth 2.0). The client id and secret are set in config.php (OAUTH_PROVIDERS)
    const OAUTH = array(
        'google' => array(
            'label'     => 'Google',
            'authorize' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'token'     => 'https://oauth2.googleapis.com/token',
            'userinfo'  => 'https://openidconnect.googleapis.com/v1/userinfo',
            'scope'     => 'openid email profile',
            'pkce'      => true,
        ),
        'facebook' => array(
            'label'     => 'Facebook',
            'authorize' => 'https://www.facebook.com/v21.0/dialog/oauth',
            'token'     => 'https://graph.facebook.com/v21.0/oauth/access_token',
            'userinfo'  => 'https://graph.facebook.com/v21.0/me?fields=id,email,first_name,last_name',
            'scope'     => 'email public_profile',
            'pkce'      => false,
        ),
        'x' => array(
            'label'     => 'X',
            'authorize' => 'https://x.com/i/oauth2/authorize',
            'token'     => 'https://api.x.com/2/oauth2/token',
            'userinfo'  => 'https://api.x.com/2/users/me?user.fields=confirmed_email,name,username',
            'scope'     => 'users.read tweet.read users.email',
            'pkce'      => true,
        ),
    );
    // Maximum time, in seconds, between the redirection to the provider and the return
    const OAUTH_MAX_TIME = 600;

    public static array $fieldsNames = array('id_user','email','email_verified','password_hash','first_name','last_name','id_media','provider','provider_user_id','status','permissions','last_login_at','created_at','updated_at','deleted_at');

    function __construct(){
        if(filter_var($_GET['showTabs'] ?? null, FILTER_VALIDATE_INT) === 0){
            $this->showTabs = 0;
        }
    }

    /**
     * Controller default method, validate inputs, create object and dispatch the request to other method depending of the request
     * @return void
     */
    function render(){

        // Authentication pages (public, own layout, no menu)
        $authTask = (string)filter_var($_GET['task'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
        if(in_array($authTask, self::AUTH_TASKS, true)){
            $this->HTMLUser = new ViewUser(array());
            $this->dataUser = new ModelUser();
            $this->$authTask();
            return;
        }

        // All the other tasks of this component need a logged user
        if(!self::isLogged()){
            self::redirect(self::url('login'));
            return;
        }

        // Authorization. index.php already refuses the request, it is checked again here because this
        // component is the one that gives the permissions: own profile for everybody, the rest for the administrators
        if(!\Apgenic\Classes\Auth::can('user', $authTask == '' ? 'editlist' : $authTask)){
            throw new \ErrorException('Access denied', 403, E_ERROR);
        }

        $oModelMedia = new \Apgenic\Media\ModelMedia();
        $oModelMedia->getList();

        $this->HTMLUser = new ViewUser($oModelMedia->list);
        $this->dataUser = new ModelUser();



        // Task to execute
        $task = filter_var($_GET['task'] ?? 'editlist', FILTER_SANITIZE_SPECIAL_CHARS); ;

        $this->setOrderAndFilters();
        $this->HTMLUser->filters = $this->filters;
        $this->HTMLUser->filtersGet = $this->filtersGet;


        $textSearch = filter_var($_GET['text_search'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        // Saved before the header, which displays the name of the user
        if($task == 'profile'){
            $this->saveProfile();
        }

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLUser->header($task == 'profile' ? 'My profile' : 'User');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLUser->search(self::$fieldsNames, $task);
            }
            //echo '<div id="core_content">';

        }

        try{
            switch ($task) {
                case 'edit':       $this->edit(); break;
                case 'del':        $this->del(); break;
                case 'delHtmx':    $this->delHTMX(); break;
                case 'editlist':   $this->editlist($textSearch); break;
                case 'childlist':  $this->childlist(); break;

                case 'undelHtmx':  $this->undelHTMX(); break;
                case 'trashedlist':$this->trashedlist(); break;
                case 'logicaldeleteHtmx':    $this->delHTMX(1); break;

                case 'view':       $this->view(); break;
                case 'viewlist':   $this->viewList($textSearch); break;

                case 'profile':    $this->profile(); break;

                default: throw new \ErrorException('Page not found', 404, E_ERROR);
            }
        }
        catch(Exception $e){
            $this->message['type'] = 'danger';
            $this->message['text'] = $e->getMessage();
        }

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            //echo '</div>';
            $this->HTMLUser->footer();
        }

    }

    /**
     * Edit or add a row in user
     * @return void
     */
    function edit(string $task='edit'){
        // For when the edit is under a tab
        $this->dataUser->id_media = (int)($_GET['id_media'] ?? 0);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->dataUser->id_user = filter_var($_POST['id_user'] ?? null, FILTER_VALIDATE_INT);
            if(filter_var($_POST['email'] ?? null, FILTER_UNSAFE_RAW) &&
                filter_var($_POST['first_name'] ?? null, FILTER_UNSAFE_RAW) &&
                filter_var($_POST['last_name'] ?? null, FILTER_UNSAFE_RAW)){

                // Existing user: start from the stored row, so the password is kept when the fields are left empty
                if($this->dataUser->id_user > 0){
                    $this->dataUser->get();
                }

                $this->dataUser->email = (string)filter_var($_POST['email'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataUser->email_verified = isset($_POST['email_verified']) ? 1 : 0;
                // The password is changed only when a new one is typed, with the same confirmation
                $newPassword = (string)($_POST['password_hash'] ?? '');
                if($newPassword != '' && $newPassword === (string)($_POST['confirmation_password_hash'] ?? '')){
                    $this->dataUser->password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
                }
                $this->dataUser->first_name = (string)filter_var($_POST['first_name'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataUser->last_name = (string)filter_var($_POST['last_name'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataUser->id_media = ($_POST['id_media'] ?? '') == '' ? null : (int)filter_var($_POST['id_media'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataUser->provider = (string)filter_var($_POST['provider'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataUser->provider_user_id = (string)filter_var($_POST['provider_user_id'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                if($this->dataUser->provider_user_id == ''){ $this->dataUser->provider_user_id = null; }
                $this->dataUser->status = (string)filter_var($_POST['status'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataUser->last_login_at = (string)filter_var($_POST['last_login_at'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                if($this->dataUser->last_login_at == ''){ $this->dataUser->last_login_at = null; }

                // Permissions, from 0 to 100. An administrator cannot change his own level:
                // the last administrator would lock everybody out of the user management
                if((int)$this->dataUser->id_user !== (int)$_SESSION['user']['id_user']){
                    $this->dataUser->permissions = max(0, min(100, (int)filter_var($_POST['permissions'] ?? 0, FILTER_VALIDATE_INT)));
                }

                $this->dataUser->save();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been saved';
            }

            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }
        }

        else{
            $this->dataUser->id_user = filter_var($_GET['id_user'] ?? null, FILTER_VALIDATE_INT);

        }


        if( 1 && $this->dataUser->id_user > 0 ){
            $this->dataUser->get();
        }
        else{
            // Do not show tabs when wee create the entry
            $this->showTabs = 0;
        }


        if($task == 'edit'){
            $this->HTMLUser->edit($this->dataUser,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataUser = new ModelUser();
            $this->HTMLUser->edit($this->dataUser,  $this->showTabs, $this->message, 'childlist');
        }
    }



    /**
     * Delete a row in user and display the list of elements
     * @return void
     */
    function del(){
        $this->dataUser->id_user = filter_var($_POST['id_user'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataUser->id_user){
            $this->dataUser->del();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }

        $this->editlist();
    }



    /**
     * Delete a row in user and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataUser->id_user = filter_var($_GET['id_user'] ?? null, FILTER_VALIDATE_INT);

        if( 1  && $this->dataUser->id_user ){

            if($logical == 1){
                $this->dataUser->logicalDel(1);
            }
            else{
                $this->dataUser->del();
            }
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }

        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLUser->message($this->message);
        }
    }

    /**
     * Display a list of row from user with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition
        $this->filters['deleted_at'] = NULL;
        $nbItems = $this->dataUser->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLUser->editList($this->dataUser->list ,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLUser->pagination($nbPages, $this->page, "?component=user&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataUser->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLUser->childList($this->dataUser->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }


    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataUser->id_user = filter_var($_GET['id_user'] ?? null, FILTER_VALIDATE_INT);
        $this->dataUser->get();
        $this->HTMLUser->view($this->dataUser,  $this->message);
    }


    /**
     * Display a list of row from user with only the view button
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list
        $this->filters['deleted_at'] = NULL;


        $nbItems = $this->dataUser->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLUser->viewList($this->dataUser->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLUser->pagination($nbPages, $this->page, "?component=user&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display a list of row from user with only the view button
     * For consultation without editing
     * @return void
     */
    function trashedList($textSearch = ''){
        // Display items list
        $nbItems = $this->dataUser->getList(($this->page * $this->itemsByPage)-$this->itemsByPage ,
                                                                 $this->itemsByPage, array('deleted_at' => 1),
                                                                 $this->orderBy,
                                                                 strtoupper($this->order),
                                                                 $textSearch);

        $this->HTMLUser->trashedList($this->dataUser->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLUser->pagination($nbPages, $this->page, "?component=user&task=trashedlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * UnDelete a row in user and display the list of elements
     * @return void
     */
    function undelHTMX(){
        $this->dataUser->id_user = filter_var($_GET['id_user'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataUser->id_user){
            $this->dataUser->logicalUnDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLUser->message($this->message);
        }
    }


    /**
     * Delete a row in user and display the list of elements
     * @return void
     */
    function logicaldeleteHtmx(){
        $this->dataUser->id_user = filter_var($_GET['id_user'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataUser->id_user){
            $this->dataUser->logicalDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLUser->message($this->message);
        }
    }



    /**
     * Set the order and filter private variable depending of the browser request
     * @return void
     */
    protected function setOrderAndFilters($defaultOrderBy = '', $defaultOrder = 'asc'):void{
        // FK Filter(s)
        if(isset($_GET['filters']['id_media'])){
            $this->filters['id_media'] = intval($_GET['filters']['id_media']);
        }

        if(isset($_GET['field_search'])){
            $this->filters[$_GET['field_search']] = $_GET['field_search_value'];
        }

        parent::setOrderAndFilters('id_user', 'desc');
    }

    // Profile of the logged user
    // ------------------------------------------------------------------------------------------------

    /**
     * Save the profile form: the user changes his own name, email and password, nothing else.
     * The account is always the one of the session, never an id sent by the browser.
     * @return void
     */
    private function saveProfile():void{
        $this->dataUser->id_user = (int)$_SESSION['user']['id_user'];
        if(!$this->dataUser->get() || $_SERVER['REQUEST_METHOD'] !== 'POST'){
            return;
        }
        $isLocal = $this->dataUser->provider == 'local';

        $firstName = trim((string)filter_var($_POST['first_name'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
        $lastName = trim((string)filter_var($_POST['last_name'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
        // The email of an account created with Google, Facebook, X comes from the provider
        $email = $isLocal ? trim((string)($_POST['email'] ?? '')) : (string)$this->dataUser->email;
        $password = (string)($_POST['password'] ?? '');

        $error = '';
        if($firstName == '' || $lastName == '' || $email == ''){
            $error = 'Fill all mandatory fields';
        }
        elseif(strlen($firstName) > 100 || strlen($lastName) > 100){
            $error = 'The first name or the last name is too long';
        }
        elseif($isLocal && (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255)){
            $error = 'The email address is not valid';
        }
        elseif($isLocal && $password != ''){
            if(!password_verify((string)($_POST['current_password'] ?? ''), (string)$this->dataUser->password_hash)){
                $error = 'The current password is wrong';
            }
            else{
                $error = $this->passwordError($password, (string)($_POST['password_confirmation'] ?? ''));
            }
        }

        // The email is unique in the table, logically deleted users included
        if($error == '' && strcasecmp($email, (string)$this->dataUser->email) != 0){
            $other = new ModelUser();
            if($other->getByEmail($email, false)){
                $error = 'An account already exists with this email address';
            }
        }

        if($error != ''){
            $this->message = array('type' => 'danger', 'text' => $error);
            // Display what has been typed
            $this->dataUser->first_name = $firstName;
            $this->dataUser->last_name = $lastName;
            $this->dataUser->email = htmlspecialchars($email);
            return;
        }

        $this->dataUser->updateProfile($email, $firstName, $lastName);
        if($isLocal && $password != ''){
            $this->dataUser->updatePassword(password_hash($password, PASSWORD_DEFAULT));
        }
        $this->dataUser->get();

        $_SESSION['user']['email'] = $this->dataUser->email;
        $_SESSION['user']['first_name'] = $this->dataUser->first_name;
        $_SESSION['user']['last_name'] = $this->dataUser->last_name;

        $this->message = array('type' => 'success', 'text' => 'Your profile has been saved');
    }


    /**
     * Display the profile form
     * @return void
     */
    function profile(){
        $this->HTMLUser->profile($this->dataUser, $this->message, self::PASSWORD_MIN_LENGTH);
    }


    // Authentication
    // ------------------------------------------------------------------------------------------------

    /**
     * Is a user logged in the current session
     * @return bool
     */
    public static function isLogged():bool{
        return \Apgenic\Classes\Auth::isLogged();
    }


    /**
     * Url of a task of this component
     * @return string
     */
    private static function url(string $task, string $params = ''):string{
        return BASE_URL.'/index.php?component=user&task='.$task.$params;
    }


    /**
     * Redirect the browser (also works for a request sent by HTMX)
     * @return void
     */
    private static function redirect(string $url):void{
        if(isset($_SERVER['HTTP_HX_REQUEST'])){
            header('HX-Redirect: '.$url);
        }
        else{
            header('Location: '.$url);
        }
    }


    /**
     * Store the user in the session
     * @return void
     */
    private function openSession():void{
        // New session id (session fixation) and new CSRF token
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['user'] = array(
            'id_user' => (int)$this->dataUser->id_user,
            'email' => $this->dataUser->email,
            'first_name' => $this->dataUser->first_name,
            'last_name' => $this->dataUser->last_name,
        );
        unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
    }


    /**
     * Message set by a previous page and displayed once
     * @return void
     */
    private function flashMessage():void{
        if(isset($_SESSION['auth_message'])){
            $this->message = $_SESSION['auth_message'];
            unset($_SESSION['auth_message']);
        }
    }


    /**
     * Check the new password and his confirmation
     * @return string error message, empty if the password is accepted
     */
    private function passwordError(string $password, string $confirmation):string{
        if(strlen($password) < self::PASSWORD_MIN_LENGTH){
            return 'The password must contain at least '.self::PASSWORD_MIN_LENGTH.' characters';
        }
        // bcrypt ignores what is after 72 bytes
        if(strlen($password) > 72){
            return 'The password is too long (72 characters maximum)';
        }
        if($password !== $confirmation){
            return 'The password and its confirmation are different';
        }
        return '';
    }


    /**
     * Login form and login
     * @return void
     */
    function login(){
        if(self::isLogged()){
            self::redirect(BASE_URL.'/index.php');
            return;
        }

        $this->flashMessage();
        $email = '';

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            if(($_SESSION['login_locked_until'] ?? 0) > time()){
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Too many attempts, try again in a few minutes';
            }
            else{
                $found = filter_var($email, FILTER_VALIDATE_EMAIL) ? $this->dataUser->getByEmail($email) : 0;

                // Always verify a hash, so the response time does not tell if the email exists
                $hash = $found && $this->dataUser->password_hash ? $this->dataUser->password_hash : self::DUMMY_HASH;
                $passwordOk = password_verify($password, $hash);

                if($found && $passwordOk && $this->dataUser->provider == 'local' && $this->dataUser->status == 'active'){

                    if(password_needs_rehash($this->dataUser->password_hash, PASSWORD_DEFAULT)){
                        $this->dataUser->updatePassword(password_hash($password, PASSWORD_DEFAULT));
                    }
                    $this->dataUser->touchLastLogin();
                    $this->openSession();
                    self::redirect(BASE_URL.'/index.php');
                    return;
                }

                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                if($_SESSION['login_attempts'] >= self::MAX_LOGIN_ATTEMPTS){
                    $_SESSION['login_locked_until'] = time() + self::LOGIN_LOCK_TIME;
                    $_SESSION['login_attempts'] = 0;
                }

                // Same message whatever the reason
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Wrong email or password';
            }
        }

        $this->HTMLUser->login($email, $this->message, self::oauthProviders());
    }


    /**
     * Logout (POST only, protected by the CSRF token)
     * @return void
     */
    function logout(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $_SESSION = array();
            if(ini_get('session.use_cookies')){
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            }
            session_destroy();
        }
        self::redirect(self::url('login'));
    }


    /**
     * Registration form and creation of the account
     * @return void
     */
    function register(){
        if(self::isLogged()){
            self::redirect(BASE_URL.'/index.php');
            return;
        }

        $values = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $values['email'] = trim((string)($_POST['email'] ?? ''));
            $values['first_name'] = trim((string)filter_var($_POST['first_name'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
            $values['last_name'] = trim((string)filter_var($_POST['last_name'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
            $password = (string)($_POST['password'] ?? '');

            $error = '';
            if($values['first_name'] == '' || $values['last_name'] == '' || $values['email'] == ''){
                $error = 'Fill all mandatory fields';
            }
            elseif(!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || strlen($values['email']) > 255){
                $error = 'The email address is not valid';
            }
            elseif(strlen($values['first_name']) > 100 || strlen($values['last_name']) > 100){
                $error = 'The first name or the last name is too long';
            }
            else{
                $error = $this->passwordError($password, (string)($_POST['password_confirmation'] ?? ''));
            }

            // The email is unique in the table, logically deleted users included
            if($error == '' && $this->dataUser->getByEmail($values['email'], false)){
                $error = 'An account already exists with this email address';
            }

            if($error == ''){
                $this->dataUser = new ModelUser();
                $this->dataUser->email = $values['email'];
                $this->dataUser->first_name = $values['first_name'];
                $this->dataUser->last_name = $values['last_name'];
                $this->dataUser->password_hash = password_hash($password, PASSWORD_DEFAULT);

                if($this->dataUser->register()){
                    $this->openSession();
                    self::redirect(BASE_URL.'/index.php');
                    return;
                }
                $error = 'The account could not be created';
            }

            $this->message['type'] = 'danger';
            $this->message['text'] = $error;
        }

        $this->HTMLUser->register($values, $this->message, self::PASSWORD_MIN_LENGTH, self::oauthProviders());
    }


    /**
     * Form asking a password reset link, send the link by email
     * @return void
     */
    function forgot(){
        $devResetLink = '';

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $email = trim((string)($_POST['email'] ?? ''));

            if(filter_var($email, FILTER_VALIDATE_EMAIL)
                && $this->dataUser->getByEmail($email)
                && $this->dataUser->provider == 'local'
                && $this->dataUser->status == 'active'){

                $token = $this->dataUser->createPasswordResetToken(self::RESET_TOKEN_VALIDITY);
                $link = $this->absoluteUrl(self::url('reset', '&token='.$token));
                $this->sendResetMail($this->dataUser->email, $link);

                if(defined('AUTH_SHOW_RESET_LINK') && AUTH_SHOW_RESET_LINK){
                    $devResetLink = $link;
                }
            }

            // Same message if the email is unknown, to not reveal which emails have an account
            $this->message['type'] = 'success';
            $this->message['text'] = 'If an account exists with this email address, a link to choose a new password has been sent';
        }

        $this->HTMLUser->forgot($this->message, $devResetLink);
    }


    /**
     * Form to choose a new password, reached with the link sent by email
     * @return void
     */
    function reset(){
        $token = (string)($_POST['token'] ?? $_GET['token'] ?? '');
        $tokenValid = (bool)$this->dataUser->getByPasswordResetToken($token);

        if(!$tokenValid){
            $this->message['type'] = 'danger';
            $this->message['text'] = 'This link is not valid or has expired';
        }
        elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
            $password = (string)($_POST['password'] ?? '');
            $error = $this->passwordError($password, (string)($_POST['password_confirmation'] ?? ''));

            if($error == ''){
                $this->dataUser->updatePassword(password_hash($password, PASSWORD_DEFAULT));
                // The link can be used only once
                $this->dataUser->deletePasswordResetTokens();

                unset($_SESSION['user'], $_SESSION['login_attempts'], $_SESSION['login_locked_until']);
                $_SESSION['auth_message'] = array('type' => 'success', 'text' => 'Your password has been changed, you can sign in');
                self::redirect(self::url('login'));
                return;
            }

            $this->message['type'] = 'danger';
            $this->message['text'] = $error;
        }

        // The token must not leak in the Referer header
        header('Referrer-Policy: no-referrer');
        $this->HTMLUser->reset($token, $tokenValid, $this->message, self::PASSWORD_MIN_LENGTH);
    }


    /**
     * Absolute url used in emails. Define APP_URL in config.php in production,
     * the Host header sent by the browser cannot be trusted.
     * @return string
     */
    private function absoluteUrl(string $url):string{
        if(defined('APP_URL') && APP_URL != ''){
            return rtrim(APP_URL, '/').$url;
        }
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        return ($https ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$url;
    }


    /**
     * Send the password reset link by email
     * @return bool
     */
    private function sendResetMail(string $to, string $link):bool{
        $subject = 'Reset your password';
        $body  = "Hello,\r\n\r\n";
        $body .= "Use the following link to choose a new password, it is valid for ".self::RESET_TOKEN_VALIDITY." minutes:\r\n\r\n";
        $body .= $link."\r\n\r\n";
        $body .= "If you did not ask for a new password, ignore this email.\r\n";

        $headers = "Content-Type: text/plain; charset=utf-8\r\n";
        if(defined('MAIL_FROM') && MAIL_FROM != ''){
            $headers .= 'From: '.MAIL_FROM."\r\n";
        }

        return @mail($to, $subject, $body, $headers);
    }


    // Login with Google, Facebook, X (OAuth 2.0 authorization code flow)
    // ------------------------------------------------------------------------------------------------

    /**
     * Client id and secret of a provider, set in config.php
     * @return array empty if the provider is not configured
     */
    private static function oauthCredentials(string $provider):array{
        if(!isset(self::OAUTH[$provider]) || !defined('OAUTH_PROVIDERS')){
            return array();
        }
        $c = OAUTH_PROVIDERS[$provider] ?? array();
        if(($c['client_id'] ?? '') == '' || ($c['client_secret'] ?? '') == ''){
            return array();
        }
        return $c;
    }


    /**
     * Providers having a client id and secret
     * @return array key => label
     */
    private static function oauthProviders():array{
        $list = array();
        foreach(self::OAUTH as $key => $conf){
            if(count(self::oauthCredentials($key))){
                $list[$key] = $conf['label'];
            }
        }
        return $list;
    }


    /**
     * Url where the provider sends the user back. It is the same for all the providers and
     * must be declared, exactly, in the application created at each provider.
     * @return string
     */
    private function oauthRedirectUri():string{
        return $this->absoluteUrl(self::url('oauthcallback'));
    }


    /**
     * Go back to the login page with an error message
     * @return void
     */
    private function oauthFail(string $text):void{
        unset($_SESSION['oauth']);
        $_SESSION['auth_message'] = array('type' => 'danger', 'text' => $text);
        self::redirect(self::url('login'));
    }


    /**
     * Send the user to the login page of the provider
     * @return void
     */
    function oauth(){
        $provider = (string)($_GET['provider'] ?? '');
        $credentials = self::oauthCredentials($provider);

        if(self::isLogged() || !count($credentials)){
            self::redirect(self::isLogged() ? BASE_URL.'/index.php' : self::url('login'));
            return;
        }
        $conf = self::OAUTH[$provider];

        // The state protects the return against CSRF, the PKCE verifier against the theft of the code
        $_SESSION['oauth'] = array(
            'provider' => $provider,
            'state'    => bin2hex(random_bytes(16)),
            'verifier' => bin2hex(random_bytes(32)),
            'time'     => time(),
        );

        $params = array(
            'response_type' => 'code',
            'client_id'     => $credentials['client_id'],
            'redirect_uri'  => $this->oauthRedirectUri(),
            'scope'         => $conf['scope'],
            'state'         => $_SESSION['oauth']['state'],
        );
        if($conf['pkce']){
            $params['code_challenge'] = rtrim(strtr(base64_encode(hash('sha256', $_SESSION['oauth']['verifier'], true)), '+/', '-_'), '=');
            $params['code_challenge_method'] = 'S256';
        }

        header('Location: '.$conf['authorize'].'?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986));
    }


    /**
     * Return from the provider: get the identity of the user, then login or create the account
     * @return void
     */
    function oauthcallback(){
        $oauth = $_SESSION['oauth'] ?? array();
        unset($_SESSION['oauth']);

        $provider = (string)($oauth['provider'] ?? '');
        $credentials = self::oauthCredentials($provider);
        $state = (string)($_GET['state'] ?? '');

        if(!count($credentials)
            || $state == ''
            || !hash_equals((string)$oauth['state'], $state)
            || time() - (int)$oauth['time'] > self::OAUTH_MAX_TIME){
            $this->oauthFail('The sign in has expired, try again');
            return;
        }
        $conf = self::OAUTH[$provider];

        // The user refused, or the provider returned an error
        $code = (string)($_GET['code'] ?? '');
        if($code == ''){
            $this->oauthFail('The sign in with '.$conf['label'].' has been cancelled');
            return;
        }

        try{
            // Exchange the code for an access token
            $post = array(
                'grant_type'   => 'authorization_code',
                'code'         => $code,
                'redirect_uri' => $this->oauthRedirectUri(),
                'client_id'    => $credentials['client_id'],
            );
            $headers = array('Accept: application/json');
            if($conf['pkce']){
                $post['code_verifier'] = $oauth['verifier'];
            }
            if($provider == 'x'){
                // X wants the secret in a Basic authorization header
                $headers[] = 'Authorization: Basic '.base64_encode(rawurlencode($credentials['client_id']).':'.rawurlencode($credentials['client_secret']));
            }
            else{
                $post['client_secret'] = $credentials['client_secret'];
            }

            $token = $this->oauthRequest($conf['token'], $headers, $post);
            if(!isset($token['access_token']) || !is_string($token['access_token'])){
                throw new \RuntimeException('no access token');
            }

            $info = $this->oauthRequest($conf['userinfo'], array('Accept: application/json', 'Authorization: Bearer '.$token['access_token']));
            $profile = $this->oauthProfile($provider, $info);
        }
        catch(\Throwable $e){
            error_log('OAuth '.$provider.': '.$e->getMessage());
            $this->oauthFail('The sign in with '.$conf['label'].' failed, try again');
            return;
        }

        if($profile['id'] == ''){
            $this->oauthFail('The sign in with '.$conf['label'].' failed, try again');
            return;
        }

        // 1. This identity already has an account
        $found = $this->dataUser->getByProvider($provider, $profile['id']);

        // 2. An account exists with the same email, verified by the provider: same person
        if(!$found){
            if($profile['email'] == ''){
                $this->oauthFail('Your '.$conf['label'].' account did not give a verified email address, it is required to create your account');
                return;
            }
            $found = $this->dataUser->getByEmail($profile['email'], false);
        }

        // 3. New user
        if(!$found){
            $this->dataUser = new ModelUser();
            $this->dataUser->email = $profile['email'];
            $this->dataUser->first_name = $profile['first_name'];
            $this->dataUser->last_name = $profile['last_name'];
            $this->dataUser->provider = $provider;
            $this->dataUser->provider_user_id = $profile['id'];

            if(!$this->dataUser->registerSocial()){
                $this->oauthFail('The account could not be created');
                return;
            }
        }
        elseif($this->dataUser->status != 'active' || $this->dataUser->deleted_at !== null){
            $this->oauthFail('This account is disabled');
            return;
        }

        $this->dataUser->touchLastLogin();
        $this->openSession();
        self::redirect(BASE_URL.'/index.php');
    }


    /**
     * Normalize the answer of the provider.
     * The email is kept only when the provider says that it has been verified, because it is
     * used to recognize an existing account.
     * @return array id, email, first_name, last_name
     */
    private function oauthProfile(string $provider, array $info):array{
        $id = ''; $email = ''; $firstName = ''; $lastName = '';

        switch($provider){
            case 'google':
                $id = (string)($info['sub'] ?? '');
                if(($info['email_verified'] ?? false) === true || ($info['email_verified'] ?? '') === 'true'){
                    $email = (string)($info['email'] ?? '');
                }
                $firstName = (string)($info['given_name'] ?? '');
                $lastName = (string)($info['family_name'] ?? '');
                break;

            case 'facebook':
                // Facebook only returns an email that the user has confirmed
                $id = (string)($info['id'] ?? '');
                $email = (string)($info['email'] ?? '');
                $firstName = (string)($info['first_name'] ?? '');
                $lastName = (string)($info['last_name'] ?? '');
                break;

            case 'x':
                $data = is_array($info['data'] ?? null) ? $info['data'] : array();
                $id = (string)($data['id'] ?? '');
                $email = (string)($data['confirmed_email'] ?? '');
                // X has a single display name
                $name = trim((string)($data['name'] ?? ''));
                if($name == ''){ $name = (string)($data['username'] ?? ''); }
                $parts = explode(' ', $name, 2);
                $firstName = $parts[0];
                $lastName = $parts[1] ?? '';
                break;
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255){
            $email = '';
        }
        if(strlen($id) > 255){
            $id = '';
        }
        if(trim($firstName) == ''){
            $firstName = $email != '' ? substr($email, 0, (int)strpos($email, '@')) : 'User';
        }

        return array(
            'id' => $id,
            'email' => $email,
            'first_name' => self::cleanName($firstName),
            'last_name' => self::cleanName($lastName),
        );
    }


    /**
     * Same sanitization as the edit form (the views display the names as they are stored), max 100 characters
     * @return string
     */
    private static function cleanName(string $name):string{
        $name = (string)filter_var(trim($name), FILTER_SANITIZE_SPECIAL_CHARS);
        if(strlen($name) > 100){
            // Do not cut an html entity or an UTF-8 character
            $name = (string)preg_replace('/(&[^;]*|[\xC0-\xFF][\x80-\xBF]*)$/', '', substr($name, 0, 100));
        }
        return $name;
    }


    /**
     * HTTP request to a provider, GET or POST if $post is given
     * @return array the decoded JSON answer
     */
    private function oauthRequest(string $url, array $headers = array(), array $post = null):array{
        if(!function_exists('curl_init')){
            throw new \RuntimeException('the curl extension of PHP is not enabled');
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_FOLLOWLOCATION => false,
        ));
        if($post !== null){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        $body = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if($body === false){
            throw new \RuntimeException($error);
        }
        $json = json_decode((string)$body, true);
        if($status < 200 || $status >= 300 || !is_array($json)){
            throw new \RuntimeException('HTTP '.$status.' '.substr((string)$body, 0, 300));
        }
        return $json;
    }

}
