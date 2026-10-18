<?php
/**
 * Access by code: a code opens a form of 15 fields, the user then gives his email
 * and receives a link to the form he has filled, with 5 new codes to pass on.
 *
 * Tasks
 *   code   GET: form asking the code. POST code: the code must exist in access_code    public
 *   form   the 15 fields, reached only after a valid code                              public
 *   email  GET: form asking the email. POST email: saves the form, sends link + codes  public
 *   sent   the link has been sent                                                      public
 *   link   page opened by the link of the email (token)                                public
 * The tasks are declared public in \Apgenic\Classes\Auth::PUBLIC_TASKS.
 */

declare(strict_types=1);
namespace Apgenic\Accesscode;

class ControllerAccesscode {

    // The 15 fields of the form: column of access_form => label. Change the labels here.
    const FIELDS = array(
        'field_01' => T_ACCESS_FIELD_01,
        'field_02' => T_ACCESS_FIELD_02,
        'field_03' => T_ACCESS_FIELD_03,
        'field_04' => T_ACCESS_FIELD_04,
        'field_05' => T_ACCESS_FIELD_05,
        'field_06' => T_ACCESS_FIELD_06,
        'field_07' => T_ACCESS_FIELD_07,
        'field_08' => T_ACCESS_FIELD_08,
        'field_09' => T_ACCESS_FIELD_09,
        'field_10' => T_ACCESS_FIELD_10,
        'field_11' => T_ACCESS_FIELD_11,
        'field_12' => T_ACCESS_FIELD_12,
        'field_13' => T_ACCESS_FIELD_13,
        'field_14' => T_ACCESS_FIELD_14,
        'field_15' => T_ACCESS_FIELD_15,
    );
    const FIELD_MAX_LENGTH = 255;

    // Wrong codes allowed before the form is locked, and duration of the lock in seconds
    const MAX_ATTEMPTS = 5;
    const LOCK_DURATION = 900;

    // Validity of the link sent by email, in days
    const LINK_VALIDITY_DAYS = 7;

    // New codes created and sent with the link
    const CODES_SENT = 5;

    private ViewAccesscode $HTMLAccesscode;
    private ModelAccesscode $dataAccesscode;
    private array $message = array();


    function render(){
        $this->HTMLAccesscode = new ViewAccesscode();
        $this->dataAccesscode = new ModelAccesscode();

        $task = (string)filter_var($_GET['task'] ?? 'code', FILTER_SANITIZE_SPECIAL_CHARS);

        if(isset($_SESSION['accesscode_message'])){
            $this->message = $_SESSION['accesscode_message'];
            unset($_SESSION['accesscode_message']);
        }

        switch($task){
            case 'code':  $this->code(); break;
            case 'form':  $this->form(); break;
            case 'email': $this->email(); break;
            case 'sent':  $this->sent(); break;
            case 'link':  $this->link(); break;
            default: throw new \ErrorException(T_PAGE_NOT_FOUND, 404, E_ERROR);
        }
    }


    private static function url(string $task, string $params = ''):string{
        return BASE_URL.'/index.php?component=accesscode&task='.$task.$params;
    }

    private static function redirect(string $url):void{
        header((isset($_SERVER['HTTP_HX_REQUEST']) ? 'HX-Redirect: ' : 'Location: ').$url);
    }

    private static function flash(string $type, string $text):void{
        $_SESSION['accesscode_message'] = array('type' => $type, 'text' => $text);
    }

    /** Id of the code validated in this session, 0 if none */
    private static function validatedCode():int{
        return (int)($_SESSION['accesscode']['id_access_code'] ?? 0);
    }


    // 1. Code
    // ------------------------------------------------------------------------------------------------

    private function code():void{
        $code = '';

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $lockedUntil = (int)($_SESSION['accesscode_locked_until'] ?? 0);
            $code = trim((string)($_POST['code'] ?? ''));

            if($lockedUntil > time()){
                $this->message = array('type' => 'danger', 'text' => sprintf(T_ERROR_TOO_MANY_CODES, ceil(($lockedUntil - time()) / 60)));
            }
            elseif($code === '' || strlen($code) > 64){
                $this->message = array('type' => 'danger', 'text' => T_ERROR_ENTER_CODE);
            }
            else{
                $idAccessCode = $this->dataAccesscode->findCode($code);
                if($idAccessCode > 0){
                    unset($_SESSION['accesscode_attempts'], $_SESSION['accesscode_locked_until']);
                    session_regenerate_id(true);
                    $_SESSION['accesscode'] = array('id_access_code' => $idAccessCode, 'values' => array());
                    self::redirect(self::url('form'));
                    return;
                }

                $_SESSION['accesscode_attempts'] = (int)($_SESSION['accesscode_attempts'] ?? 0) + 1;
                if($_SESSION['accesscode_attempts'] >= self::MAX_ATTEMPTS){
                    $_SESSION['accesscode_locked_until'] = time() + self::LOCK_DURATION;
                    $_SESSION['accesscode_attempts'] = 0;
                }
                $this->message = array('type' => 'danger', 'text' => T_ERROR_CODE_UNKNOWN);
            }
        }

        $this->HTMLAccesscode->header(T_ACCESS_CODE);
        $this->HTMLAccesscode->code(htmlspecialchars($code), $this->message);
        $this->HTMLAccesscode->footer();
    }


    // 2. Form of 15 fields
    // ------------------------------------------------------------------------------------------------

    private function form():void{
        if(self::validatedCode() < 1){
            self::flash('warning', T_ERROR_CODE_FIRST);
            self::redirect(self::url('code'));
            return;
        }

        $values = $_SESSION['accesscode']['values'] ?? array();
        $errors = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $values = array();
            foreach(array_keys(self::FIELDS) as $field){
                $values[$field] = trim((string)filter_var($_POST[$field] ?? '', FILTER_SANITIZE_SPECIAL_CHARS));
                if(strlen($values[$field]) > self::FIELD_MAX_LENGTH){
                    $errors[$field] = T_ERROR_TOO_LONG;
                }
            }
            $_SESSION['accesscode']['values'] = $values;

            if(!count($errors)){
                $_SESSION['accesscode']['form_done'] = true;
                self::redirect(self::url('email'));
                return;
            }
            $this->message = array('type' => 'danger', 'text' => T_MSG_FIELDS_INVALID);
        }

        $this->HTMLAccesscode->header(T_FORM);
        $this->HTMLAccesscode->form(self::FIELDS, $values, $errors, self::FIELD_MAX_LENGTH, $this->message);
        $this->HTMLAccesscode->footer();
    }


    // 3. Email: save the form and send the link
    // ------------------------------------------------------------------------------------------------

    private function email():void{
        if(self::validatedCode() < 1 || empty($_SESSION['accesscode']['form_done'])){
            self::redirect(self::url(self::validatedCode() < 1 ? 'code' : 'form'));
            return;
        }

        $email = '';
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $email = trim((string)($_POST['email'] ?? ''));

            if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255){
                $this->message = array('type' => 'danger', 'text' => T_ERROR_EMAIL_INVALID);
            }
            else{
                $saved = $this->dataAccesscode->saveFormWithCodes(self::validatedCode(), $_SESSION['accesscode']['values'], $email, self::LINK_VALIDITY_DAYS, self::CODES_SENT);
                if($saved === array()){
                    $this->message = array('type' => 'danger', 'text' => T_ERROR_FORM_NOT_SAVED);
                }
                else{
                    $link = $this->absoluteUrl(self::url('link', '&token='.$saved['token']));
                    $mailSent = $this->sendLinkMail($email, $link, $saved['codes']);

                    // The form is done: a new code is needed for another one
                    unset($_SESSION['accesscode']);
                    $_SESSION['accesscode_sent'] = array(
                        'email' => htmlspecialchars($email),
                        'mail_sent' => $mailSent,
                        // DEVELOPMENT ONLY, same setting as the password reset (config.php)
                        'dev_link' => (defined('AUTH_SHOW_RESET_LINK') && AUTH_SHOW_RESET_LINK) ? $link : '',
                        'dev_codes' => (defined('AUTH_SHOW_RESET_LINK') && AUTH_SHOW_RESET_LINK) ? $saved['codes'] : array(),
                    );
                    self::redirect(self::url('sent'));
                    return;
                }
            }
        }

        $this->HTMLAccesscode->header(T_YOUR_EMAIL);
        $this->HTMLAccesscode->email(htmlspecialchars($email), $this->message);
        $this->HTMLAccesscode->footer();
    }


    private function sent():void{
        $sent = $_SESSION['accesscode_sent'] ?? null;
        if($sent === null){
            self::redirect(self::url('code'));
            return;
        }
        unset($_SESSION['accesscode_sent']);

        $this->HTMLAccesscode->header(T_LINK_SENT);
        $this->HTMLAccesscode->sent($sent['email'], (bool)$sent['mail_sent'], (string)$sent['dev_link'], (array)$sent['dev_codes']);
        $this->HTMLAccesscode->footer();
    }


    // 4. Page opened by the link of the email
    // ------------------------------------------------------------------------------------------------

    private function link():void{
        $row = $this->dataAccesscode->getFormByToken((string)($_GET['token'] ?? ''));

        // The token must not leak in the Referer header
        header('Referrer-Policy: no-referrer');
        $this->HTMLAccesscode->header(T_YOUR_FORM);
        $this->HTMLAccesscode->link(self::FIELDS, $row);
        $this->HTMLAccesscode->footer();
    }


    /**
     * Absolute url used in emails, same rule as the component user: APP_URL in production
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
     * Send the link to the form and the new codes by email
     * @param array $codes the new codes
     * @return bool
     */
    private function sendLinkMail(string $to, string $link, array $codes):bool{
        // Subject encoded for the accents (RFC 2047)
        $subject = '=?UTF-8?B?'.base64_encode(T_YOUR_FORM).'?=';
        $body  = T_MAIL_HELLO."\r\n\r\n";
        $body .= sprintf(T_MAIL_FORM_TEXT, self::LINK_VALIDITY_DAYS)."\r\n\r\n";
        $body .= $link."\r\n\r\n";
        $body .= sprintf(T_MAIL_FORM_CODES, count($codes))."\r\n\r\n";
        foreach($codes as $code){
            $body .= "  ".$code."\r\n";
        }
        $body .= "\r\n".$this->absoluteUrl(self::url('code'))."\r\n";

        $headers = "Content-Type: text/plain; charset=utf-8\r\n";
        if(defined('MAIL_FROM') && MAIL_FROM != ''){
            $headers .= 'From: '.MAIL_FROM."\r\n";
        }

        return @mail($to, $subject, $body, $headers);
    }
}
