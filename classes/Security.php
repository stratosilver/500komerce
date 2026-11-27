<?php
/**
 * Security settings shared by the web interface (index.php) and the API (api.php):
 * session cookie, HTTP security headers, display of the errors, absolute urls of the emails,
 * accepted email addresses and the development helpers.
 */

declare(strict_types=1);
namespace Apgenic\Classes;

class Security
{
    /**
     * Display of the PHP errors: only when APP_DEBUG is true (config.php). The errors always go to the log of PHP.
     * @return void
     */
    public static function errors():void{
        $debug = defined('APP_DEBUG') && APP_DEBUG;
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('display_startup_errors', $debug ? '1' : '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    }


    /**
     * Is the request made over HTTPS (directly or behind a proxy that says it)
     * @return bool
     */
    public static function isHttps():bool{
        if(!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off'){
            return true;
        }
        return (string)($_SERVER['SERVER_PORT'] ?? '') === '443';
    }


    /**
     * Start the session with a safe cookie: not readable by JavaScript (HttpOnly), not sent by the requests
     * of the other sites (SameSite=Lax), only over HTTPS when the site is in HTTPS (Secure).
     * The strict mode refuses a session id that the server has not created (session fixation).
     * @return void
     */
    public static function startSession():void{
        if(session_status() === PHP_SESSION_ACTIVE){
            return;
        }
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.cookie_httponly', '1');
        session_set_cookie_params(array(
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ));
        session_start();
    }


    /**
     * HTTP headers that protect the pages: no display in a frame of another site (clickjacking), no guess of
     * the type of a file by the browser, no address of the page sent to the other sites, HTTPS kept once used.
     * @return void
     */
    public static function headers():void{
        if(headers_sent()){
            return;
        }
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: frame-ancestors 'self'; base-uri 'self'; object-src 'none'; form-action 'self'");
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
        // Not on the development hosts: the browser would refuse http://localhost for a year
        if(self::isHttps() && !self::isDevHost((string)($_SERVER['HTTP_HOST'] ?? ''))){
            header('Strict-Transport-Security: max-age=31536000');
        }
    }


    /**
     * Is the request sent by the computer of the server itself (development on WampServer)
     * @return bool
     */
    public static function isLocalRequest():bool{
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
        return $ip === '127.0.0.1' || $ip === '::1';
    }


    /**
     * DEVELOPMENT ONLY: display the links and the codes sent by email on the page (password reset, access code).
     * AUTH_SHOW_RESET_LINK must be true in config.php AND the request must come from the server itself:
     * a visitor never receives the link of somebody else, even when the setting is forgotten in production.
     * @return bool
     */
    public static function showDevLinks():bool{
        return defined('AUTH_SHOW_RESET_LINK') && AUTH_SHOW_RESET_LINK && self::isLocalRequest();
    }


    /**
     * Is a host name one of the development hosts: localhost, 127.0.0.1, *.localhost, *.test, *.local
     * @return bool
     */
    private static function isDevHost(string $host):bool{
        $host = strtolower((string)preg_replace('/:\d+$/', '', $host));
        return $host === 'localhost' || $host === '127.0.0.1' || $host === '[::1]'
            || (bool)preg_match('/^[a-z0-9-]+(\.[a-z0-9-]+)*\.(localhost|test|local)$/', $host);
    }


    /**
     * Absolute address of a page of the site, ex: for the links of the emails.
     * The Host header is sent by the browser: anybody can change it. An address built with it would send the
     * link of a password reset to the site of an attacker. So:
     *   - APP_URL of config.php is used when it is set (required in production)
     *   - without APP_URL, the host of the request is accepted only when it is a development host
     *     (localhost, *.test...), or when $trusted is false and the address is checked by somebody else
     *     (the return address of Google, Facebook, X: they only accept the addresses declared in their console)
     * @param string $path address starting with /, ex: Url::to('user', 'reset', ...)
     * @param bool $trusted the address is sent by email: the host of the request must not be trusted
     * @return string '' when no safe address can be made (APP_URL is missing): the email must not be sent
     */
    public static function absoluteUrl(string $path, bool $trusted = true):string{
        if(defined('APP_URL') && APP_URL != ''){
            return rtrim((string)APP_URL, '/').$path;
        }
        $host = (string)($_SERVER['HTTP_HOST'] ?? '');
        // Only the characters of a host name and a port
        if(!preg_match('/^(\[[0-9a-f:]+\]|[a-z0-9.-]+)(:\d{1,5})?$/i', $host)){
            return '';
        }
        if($trusted && !self::isDevHost($host)){
            error_log('500komerce: APP_URL is not set in config.php, no link can be sent by email from '.$host);
            return '';
        }
        return (self::isHttps() ? 'https' : 'http').'://'.$host.$path;
    }


    /**
     * Is an email address valid and safe to store and display.
     * PHP accepts addresses like "<script>alert(1)</script>"@example.com (quoted local part): they are refused
     * here, with all the addresses having a character that means something in HTML.
     * @return bool
     */
    public static function isValidEmail(string $email):bool{
        return $email !== ''
            && strlen($email) <= 255
            && filter_var($email, FILTER_VALIDATE_EMAIL) !== false
            && !preg_match('/[<>"&\\\\\s\x00-\x1F\x7F`]/', $email);
    }
}
