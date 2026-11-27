<?php
/**
 * Addresses of the pages: clean urls of the front-end, back office under /admin/.
 *
 * Front-end (the shop), the table ROUTES:
 *   /                          catalog, all the products              product   viewlist
 *   /category/{slug}           catalog, one category                  product   viewlist
 *   /product/{slug}            one product (its id when it has no slug)
 *   /blog, /blog/{slug}        the articles, one article
 *   /cart, /checkout, /order/{id}, /login, /register, /profile...
 *   /browse/{component}        consultation list of a component (logged users)        viewlist
 *   /browse/{component}/{id}   one element of a component                             view
 *   The views (tasks view and viewlist) are only in the front-end.
 *
 * Back office:
 *   /admin/                            ADMIN_HOME
 *   /admin/{component}                 the default task of the component (editlist)
 *   /admin/{component}/{task}?...      any other task, the parameters stay in the query string
 *
 * HTMX: /htmx.php?component=...&task=... is not changed, these addresses are never displayed by the browser.
 * The old addresses /index.php?component=...&task=... are redirected to the new ones (index.php).
 *
 * Link to a page:
 *   Url::to('product', 'view', array('slug' => 't-shirt-noir'))        /product/t-shirt-noir
 *   Url::to('product', 'viewlist', array('category' => 'sweats'))      /category/sweats
 *   Url::to('product', 'editlist', array('orderBy' => 'name'))         /admin/product/editlist?orderBy=name
 *   In the views (classes extending ViewTemplate): self::url(...), same parameters.
 * Change an address: change its line in ROUTES, the links follow.
 */

declare(strict_types=1);
namespace Apgenic\Classes;

class Url
{
    /** First part of the path of the back office */
    const ADMIN = 'admin';
    /** Component displayed by /admin/ */
    const ADMIN_HOME = 'discount';

    /**
     * Clean urls of the front-end: path => component, task.
     * {name} is a part of the path, received by the page as $_GET['name'].
     * {a|b}: for a link, the parameter a, or b when a is empty (ex: the slug of a product, else its id).
     * A link uses the first line of its component and task whose {names} are all given: the most precise first.
     * {component} and {id}: the components of KEYS, {id} is their primary key.
     */
    const ROUTES = array(
        'category/{category}'       => array('product', 'viewlist'),
        ''                          => array('product', 'viewlist'),
        'product/{slug|id_product}' => array('product', 'view'),

        'blog'                      => array('blog', 'viewlist'),
        'blog/{slug|id_post}'       => array('blog', 'view'),

        'cart'                      => array('shop', 'cart'),
        'cart/add'                  => array('shop', 'add'),
        'cart/update'               => array('shop', 'update'),
        'checkout'                  => array('shop', 'checkout'),
        'checkout/login'            => array('shop', 'login'),
        'order/{id_customer_order}' => array('shop', 'confirmation'),

        'login'                     => array('user', 'login'),
        'login/callback'            => array('user', 'oauthcallback'),
        'login/{provider}'          => array('user', 'oauth'),
        'logout'                    => array('user', 'logout'),
        'register'                  => array('user', 'register'),
        'forgot-password'           => array('user', 'forgot'),
        'reset-password'            => array('user', 'reset'),
        'profile'                   => array('user', 'profile'),

        'access-code'               => array('accesscode', 'code'),
        'access-code/form'          => array('accesscode', 'form'),
        'access-code/email'         => array('accesscode', 'email'),
        'access-code/sent'          => array('accesscode', 'sent'),
        'access-code/link'          => array('accesscode', 'link'),

        'browse/{component}'        => array('{component}', 'viewlist'),
        'browse/{component}/{id}'   => array('{component}', 'view'),
        'browse/{component}/view'   => array('{component}', 'view'),
    );

    /** Components having consultation pages under /browse/: component => primary key ('' when it has several columns) */
    const KEYS = array(
        'category'          => 'id_category',
        'media'             => 'id_media',
        'post'              => 'id_post',
        'translation'       => 'id_translation',
        'productvariant'    => 'id_product_variant',
        'productmedia'      => '',
        'productcategory'   => '',
        'discount'          => 'id_discount',
        'customerorder'     => 'id_customer_order',
        'customerorderitem' => 'id_customer_order_item',
        'cartitem'          => 'id_cart_item',
        'user'              => 'id_user',
    );

    /** Kind of the current request, set by route(): front, admin, legacy, htmx, notfound */
    private static string $area = '';


    // Links
    // ------------------------------------------------------------------------------------------------

    /**
     * Slug of a text, for an address: "Sweat à capuche Gris !" -> "sweat-a-capuche-gris".
     * Letters without accents, digits, separated by -. The same as komerceSlug() of public/js/slug.js, which
     * fills the slug while the name is typed: keep both the same.
     * @param string $text can be html-escaped (the forms store the texts escaped)
     * @param int $max longest slug (the columns slug are VARCHAR(200))
     * @return string '' when the text has no letter and no digit
     */
    public static function slug(string $text, int $max = 200): string{
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
        $text = strtr($text, self::SLUG_LETTERS);
        // The other accents: the letter without its accent (intl), what is still not a-z 0-9 becomes -
        if(class_exists('Normalizer')){
            $text = (string)\Normalizer::normalize($text, \Normalizer::FORM_D);
        }
        // Accents written apart from their letter (and the ones taken apart by intl)
        $text = (string)preg_replace('/[\x{0300}-\x{036f}]+/u', '', $text);
        $text = trim((string)preg_replace('/[^a-z0-9]+/', '-', (string)$text), '-');
        return rtrim(substr($text, 0, $max), '-');
    }

    /** Letters of Url::slug() and of komerceSlug() (public/js/slug.js) */
    const SLUG_LETTERS = array(
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ā' => 'a', 'æ' => 'ae',
        'ç' => 'c', 'č' => 'c', 'ć' => 'c', 'đ' => 'd', 'ď' => 'd',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i', 'ł' => 'l', 'ľ' => 'l',
        'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ō' => 'o', 'œ' => 'oe',
        'ř' => 'r', 'ś' => 's', 'š' => 's', 'ß' => 'ss', 'ť' => 't', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ū' => 'u', 'ů' => 'u',
        'ý' => 'y', 'ÿ' => 'y', 'ź' => 'z', 'ż' => 'z', 'ž' => 'z',
        'ă' => 'a', 'ą' => 'a', 'ĉ' => 'c', 'ĝ' => 'g', 'ğ' => 'g', 'ĥ' => 'h', 'ı' => 'i', 'ĵ' => 'j',
        'ő' => 'o', 'ŝ' => 's', 'ş' => 's', 'ș' => 's', 'ț' => 't', 'ţ' => 't', 'ŭ' => 'u', 'ű' => 'u',
    );


    /**
     * Address of a page: clean url of the front-end, or /admin/... for the back office
     * @param array $params parameters of the page, the empty ones are left out
     * @return string
     */
    public static function to(string $component, string $task = '', array $params = array()): string{
        return self::isFront($component, $task) ? self::front($component, $task, $params) : self::admin($component, $task, $params);
    }


    /** Home page of the site: the shop */
    public static function home(): string{
        return BASE_URL.'/';
    }


    /**
     * Address in the back office
     * @param string $component '' for the home of the back office
     * @param string $task '' for the default task of the component
     * @return string
     */
    public static function admin(string $component = '', string $task = '', array $params = array()): string{
        $url = BASE_URL.'/'.self::ADMIN.'/';
        if($component != ''){
            $url .= rawurlencode($component).($task != '' ? '/'.rawurlencode($task) : '');
        }
        return $url.self::query($params);
    }


    /**
     * Clean address of a page of the front-end
     * @return string
     */
    public static function front(string $component, string $task, array $params = array()): string{
        foreach(self::ROUTES as $pattern => $target){
            if($target[1] !== $task || ($target[0] !== $component && !($target[0] === '{component}' && isset(self::KEYS[$component])))){
                continue;
            }
            $rest = $params;
            $path = array();
            foreach(($pattern === '' ? array() : explode('/', $pattern)) as $part){
                if($part[0] != '{'){
                    $path[] = $part;
                    continue;
                }
                $value = self::takeValue(substr($part, 1, -1), $component, $rest);
                if($value === ''){
                    // A part of the path is missing: next line
                    continue 2;
                }
                $path[] = rawurlencode($value);
            }
            return BASE_URL.'/'.implode('/', $path).self::query($rest);
        }
        // No clean url for this page
        return BASE_URL.'/index.php'.self::query(array('component' => $component, 'task' => $task) + $params);
    }


    /**
     * One address by page: a page of the front-end reached by another address (ex: /product/{id} of a product
     * having a slug) sends the browser to its address, with a permanent redirection. Only the GET of the browser.
     * @param string $url address of the page
     * @return void
     */
    public static function canonical(string $url): void{
        if(self::$area != 'front' || ($_SERVER['REQUEST_METHOD'] ?? 'GET') != 'GET' || isset($_SERVER['HTTP_HX_REQUEST'])){
            return;
        }
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '');
        if(rawurldecode((string)parse_url($uri, PHP_URL_PATH)) === rawurldecode((string)parse_url($url, PHP_URL_PATH))){
            return;
        }
        // The parameters of the address are kept (lang...)
        $query = (string)parse_url($uri, PHP_URL_QUERY);
        header('Location: '.$url.($query !== '' ? (strpos($url, '?') === false ? '?' : '&').$query : ''), true, 301);
        exit;
    }


    /**
     * Same page as an address made with ?component=...&task=... (ex: the links of the pagination)
     * @param string $url ex: '?component=post&task=editlist&orderBy=title&'
     * @return string the same address when it has no component
     */
    public static function fromQuery(string $url): string{
        parse_str((string)parse_url($url, PHP_URL_QUERY), $params);
        if(!isset($params['component']) || !is_string($params['component'])){
            return $url;
        }
        $component = $params['component'];
        $task = is_string($params['task'] ?? null) ? $params['task'] : '';
        unset($params['component'], $params['task']);
        return self::to($component, $task, $params);
    }


    /**
     * Is a task displayed in the front-end: the views and the pages of ROUTES (shop, blog, sign in...)
     * @return bool
     */
    public static function isFront(string $component, string $task): bool{
        if(in_array(strtolower($task), array('view', 'viewlist'), true)){
            return true;
        }
        foreach(self::ROUTES as $target){
            if($target[0] === $component && $target[1] === $task){
                return true;
            }
        }
        return false;
    }


    // Request
    // ------------------------------------------------------------------------------------------------

    /**
     * Read the address of the request: sets $_GET['component'], $_GET['task'] and the parameters in the path
     * @return string front, admin, legacy (/index.php?component=...), htmx (/htmx.php), notfound
     */
    public static function route(): string{
        if(strtolower(basename((string)($_SERVER['PHP_SELF'] ?? ''))) == 'htmx.php'){
            return self::$area = 'htmx';
        }

        $path = self::path((string)($_SERVER['REQUEST_URI'] ?? '/'));
        if($path === null){
            return self::$area = 'notfound';
        }
        if($path == 'index.php'){
            return self::$area = 'legacy';
        }
        $segments = $path === '' ? array() : array_map('rawurldecode', explode('/', $path));

        // The component and the task are given by the path only
        unset($_GET['component'], $_GET['task']);

        // Back office: /admin/{component}/{task}
        if(($segments[0] ?? '') === self::ADMIN){
            array_shift($segments);
            if(count($segments) > 2){
                return self::$area = 'notfound';
            }
            if(isset($segments[0])){
                if(!preg_match('/^[a-z]+$/', $segments[0])){
                    return self::$area = 'notfound';
                }
                $_GET['component'] = $segments[0];
            }
            if(isset($segments[1])){
                if(!preg_match('/^[A-Za-z]+$/', $segments[1])){
                    return self::$area = 'notfound';
                }
                $_GET['task'] = $segments[1];
            }
            return self::$area = 'admin';
        }

        // Front-end
        $route = self::match($segments);
        if($route === null){
            return self::$area = 'notfound';
        }
        list($component, $task, $values) = $route;
        foreach($values as $name => $value){
            $_GET[$name] = $value;
        }
        $_GET['component'] = $component;
        $_GET['task'] = $task;
        return self::$area = 'front';
    }


    /**
     * Kind of the current request, see route()
     * @return string
     */
    public static function area(): string{
        return self::$area;
    }


    /**
     * Is the current page in the back office. A request of HTMX is in the back office when it is sent by a page of the back office.
     * @return bool
     */
    public static function isAdmin(): bool{
        switch(self::$area){
            case 'admin':
                return true;
            case 'front':
            case 'notfound':
                return false;
            case 'htmx':
                $current = self::currentPage();
                if($current !== ''){
                    return self::isAdminPath($current);
                }
        }
        // Old address: the kind of the task
        $component = defined('COMPONENT') ? (string)COMPONENT : (string)($_GET['component'] ?? '');
        return !self::isFront($component, (string)($_GET['task'] ?? ''));
    }


    /**
     * Page of the back office to open again after the sign in: the current one, when the request asks a page
     * of the back office (also a request of HTMX sent by a page of the back office)
     * @return string '' for the front-end
     */
    public static function adminReturn(): string{
        if(self::$area == 'htmx'){
            return self::safeReturn(self::currentPage());
        }
        if(self::$area == 'admin' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') == 'GET'){
            return self::safeReturn((string)($_SERVER['REQUEST_URI'] ?? ''));
        }
        return '';
    }


    /**
     * Address where the user is sent after the sign in: only a page of the back office of this site
     * (never another site: the value comes from the address of the login page)
     * @return string the address, '' if it is not accepted
     */
    public static function safeReturn(string $url): string{
        if($url === '' || strlen($url) > 2000 || preg_match('/[\x00-\x20\\\\]/', $url)){
            return '';
        }
        $parts = parse_url($url);
        if($parts === false || isset($parts['scheme']) || isset($parts['host']) || isset($parts['user'])){
            return '';
        }
        $path = (string)($parts['path'] ?? '');
        if(!self::isAdminPath($path) || strpos($path, '//') !== false){
            return '';
        }
        return $path.(isset($parts['query']) && $parts['query'] !== '' ? '?'.$parts['query'] : '');
    }


    // Tools
    // ------------------------------------------------------------------------------------------------

    /**
     * Path of an address without BASE_URL and without the / at the start and at the end
     * @return string|null null when the address is not under BASE_URL
     */
    private static function path(string $uri): ?string{
        $path = (string)parse_url($uri, PHP_URL_PATH);
        if(BASE_URL != ''){
            if($path != BASE_URL && strpos($path, BASE_URL.'/') !== 0){
                return null;
            }
            $path = substr($path, strlen(BASE_URL));
        }
        return trim($path, '/');
    }


    /** Is a path (with BASE_URL) in the back office */
    private static function isAdminPath(string $url): bool{
        $path = self::path($url);
        return $path !== null && ($path === self::ADMIN || strpos($path, self::ADMIN.'/') === 0);
    }


    /**
     * Address of the page displayed by the browser, sent by HTMX (header HX-Current-URL): path and parameters
     * @return string
     */
    private static function currentPage(): string{
        $url = (string)($_SERVER['HTTP_HX_CURRENT_URL'] ?? '');
        if($url === ''){
            return '';
        }
        $path = (string)parse_url($url, PHP_URL_PATH);
        $query = (string)parse_url($url, PHP_URL_QUERY);
        return $path.($query !== '' ? '?'.$query : '');
    }


    /**
     * Line of ROUTES of a path: the one having the most fixed parts
     * @param array $segments parts of the path
     * @return array|null component, task, parameters
     */
    private static function match(array $segments): ?array{
        $best = null;
        $bestScore = -1;
        foreach(self::ROUTES as $pattern => $target){
            $parts = $pattern === '' ? array() : explode('/', $pattern);
            if(count($parts) != count($segments)){
                continue;
            }
            $values = array();
            $score = 0;
            foreach($parts as $i => $part){
                if($part[0] == '{'){
                    if($segments[$i] === ''){
                        continue 2;
                    }
                    $values[explode('|', substr($part, 1, -1))[0]] = $segments[$i];
                }
                elseif($part === $segments[$i]){
                    $score++;
                }
                else{
                    continue 2;
                }
            }
            if($score > $bestScore){
                $best = array($target[0], $target[1], $values);
                $bestScore = $score;
            }
        }
        if($best === null){
            return null;
        }

        // Consultation pages of a component: /browse/{component}/{id}
        if($best[0] === '{component}'){
            $component = (string)($best[2]['component'] ?? '');
            if(!array_key_exists($component, self::KEYS)){
                return null;
            }
            $best[0] = $component;
            unset($best[2]['component']);
            if(isset($best[2]['id'])){
                if(self::KEYS[$component] === ''){
                    return null;
                }
                $best[2][self::KEYS[$component]] = $best[2]['id'];
                unset($best[2]['id']);
            }
        }
        return $best;
    }


    /**
     * Value of a part {a|b} of a path, removed from the parameters
     * @param string $names a|b
     * @param array $params parameters of the link, without the ones used in the path
     * @return string '' when the parameters do not have it
     */
    private static function takeValue(string $names, string $component, array &$params): string{
        $value = '';
        foreach(explode('|', $names) as $name){
            if($name == 'component'){
                return $component;
            }
            if($name == 'id'){
                $name = self::KEYS[$component] ?? '';
                if($name === ''){
                    return '';
                }
            }
            $candidate = $params[$name] ?? null;
            if($value === '' && is_scalar($candidate) && (string)$candidate !== ''){
                $value = (string)$candidate;
            }
            // The other names give the same page: not repeated in the query string
            if($value !== ''){
                unset($params[$name]);
            }
        }
        return $value;
    }


    /**
     * Query string, without the empty parameters
     * @return string '' or ?a=1&b=2
     */
    private static function query(array $params): string{
        $params = self::withoutEmpty($params);
        if(!count($params)){
            return '';
        }
        // The brackets of the arrays (filters[status]=...) are kept readable
        return '?'.str_replace(array('%5B', '%5D'), array('[', ']'), http_build_query($params, '', '&', PHP_QUERY_RFC3986));
    }


    private static function withoutEmpty(array $params): array{
        foreach($params as $name => $value){
            if(is_array($value)){
                $value = self::withoutEmpty($value);
            }
            if($value === null || $value === '' || $value === false || $value === array()){
                unset($params[$name]);
            }
            else{
                $params[$name] = $value;
            }
        }
        return $params;
    }
}
