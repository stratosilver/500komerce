<?php
/**
 * Created by PhpStorm.
 * User: TILMED199
 * Date: 11-05-21
 * Time: 19:18
 * Project: exsilicon
 *
 * Layout of all the pages: Bootstrap 5.3.8 and its classes only, public/css/theme.css changes the colors,
 * the rounded corners, the shadows and the fonts.
 * Two layouts, chosen by layout():
 *   shop   the front-end, for everybody: header with the cart, footer
 *   admin  the back office, the addresses /admin/... (users who can edit): side menu (offcanvas on the small screens), top bar
 * The links are made by self::url() (classes/Url.php): clean urls in the front-end, /admin/... in the back office.
 */
namespace Apgenic\Classes;

class ViewTemplate
{
    use ViewTrait;

    // Kept for compatibility, the icons are now in classes/Icons.php
    const ICONS = [
            'plus-lg' => '<path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>',
            'x-lg' => '<path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>',
            'check2' => '<path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>',
            'view' => '<path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/><path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8m0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"/>',
            'pencil' => '<path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>',
            'trash3' => '<path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>',
            'screwdriver' => '<path d="M0 .995.995 0l3.064 2.19a1 1 0 0 1 .417.809v.07c0 .264.105.517.291.704l5.677 5.676.909-.303a1 1 0 0 1 1.018.24l3.338 3.339a.995.995 0 0 1 0 1.406L14.13 15.71a.995.995 0 0 1-1.406 0l-3.337-3.34a1 1 0 0 1-.24-1.018l.302-.909-5.676-5.677a1 1 0 0 0-.704-.291H3a1 1 0 0 1-.81-.417zm11.293 9.595a.497.497 0 1 0-.703.703l2.984 2.984a.497.497 0 0 0 .703-.703z"/>',
            'undel' => '<path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2z"/><path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466"/>',
            'robot' => '<path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5M3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.6 26.6 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.93.93 0 0 1-.765.935c-.845.147-2.34.346-4.235.346s-3.39-.2-4.235-.346A.93.93 0 0 1 3 9.219zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a25 25 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25 25 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135"/><path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2zM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5"/>',
    ];

    /**
     * Menu of the back office: component => label, icon (classes/Icons.php), section. The texts are in translations.php.
     * Only the components that the user can consult are displayed. A new component: add one line here.
     */
    const MENU = [
        'product'       => [T_MENU_PRODUCT, 'box-seam', T_SECTION_CATALOG],
        'category'      => [T_MENU_CATEGORY, 'tags', T_SECTION_CATALOG],
        'media'         => [T_MENU_MEDIA, 'images', T_SECTION_CATALOG],
        'customerorder' => [T_MENU_CUSTOMERORDER, 'receipt', T_SECTION_SALES],
        'discount'      => [T_MENU_DISCOUNT, 'percent', T_SECTION_SALES],
        'post'          => [T_MENU_POST, 'newspaper', T_SECTION_CONTENT],
        'translation'   => [T_MENU_TRANSLATION, 'translate', T_SECTION_CONTENT],
        'user'          => [T_MENU_USER, 'people', T_SECTION_ADMINISTRATION],
        'config'        => [T_MENU_CONFIG, 'gear', T_SECTION_ADMINISTRATION],
    ];
    /** Components that are not in the menu (reached by the tabs): the entry of the menu that is highlighted */
    const MENU_PARENT = [
        'productmedia'      => 'product',
        'productcategory'   => 'product',
        'productvariant'    => 'product',
        'cartitem'          => 'customerorder',
        'customerorderitem' => 'customerorder',
    ];

    /** Color of the bar of the mobile browsers (<meta name="theme-color">): the same as --bs-primary of public/css/theme.css */
    const THEME_COLOR = '#002B59';

    /** Links of the back office in the header of the page: Edit list, View list, Trash, New */
    protected bool $listLinks = true;
    /** The component has a list of the deleted entries (task trashedlist) */
    protected bool $trash = false;
    /** Small title above the title of the page in the back office, the section of the menu by default */
    protected string $eyebrow = '';


    /**
     * Name of the site, escaped for the HTML: setting site_name of the page Paramètres (table config),
     * APP_NAME in config.php by default
     * @return string
     */
    static function appName(): string{
        return htmlspecialchars(\Apgenic\Config\ModelConfig::siteName());
    }


    /**
     * Layout of the page.
     *   'shop'  the front-end (catalog, product, cart, checkout, blog, account...), for everybody
     *   'admin' the back office, the addresses /admin/...: the users who can edit (PERMISSION_EDIT)
     * @return string
     */
    static function layout(): string{
        return (Url::isAdmin() && Auth::canEdit()) ? 'admin' : 'shop';
    }


    /**
     * Address of a page: clean url of the front-end, /admin/... for the back office, see classes/Url.php
     * @param array $params parameters of the page, the empty ones are left out
     * @return string
     */
    static function url(string $component, string $task = '', array $params = array()): string{
        return Url::to($component, $task, $params);
    }


    /**
     * Amount stored in cents, ex: 1999 => 19,99 EUR
     * @return string
     */
    static function money(int $cents): string{
        return number_format($cents / 100, 2, ',', '&nbsp;').'&nbsp;'.(defined('CURRENCY') ? CURRENCY : '&euro;');
    }


    /**
     * Rate of the tax for the texts: 21 => "21", 5.5 => "5,5"
     * @return string
     */
    static function taxRateText(): string{
        return rtrim(rtrim(number_format(\Apgenic\Shop\ModelCart::taxRate(), 2, ',', ''), '0'), ',');
    }


    /**
     * Label of a field of the database, T_FIELD_<FIELD> of translations.php, ex: created_at => T_FIELD_CREATED_AT
     * @return string the name of the field when it has no translation
     */
    static function fieldLabel(string $field): string{
        $constant = 'T_FIELD_'.strtoupper($field);
        return defined($constant) ? constant($constant) : ucfirst(str_replace('_', ' ', $field));
    }


    /**
     * Value of a list of choices (status...), T_VALUE_<VALUE> of translations.php, ex: draft => T_VALUE_DRAFT
     * @return string the value itself when it has no translation
     */
    static function value(string $value): string{
        $constant = 'T_VALUE_'.strtoupper($value);
        return $value != '' && preg_match('/^[a-z0-9_]+$/i', $value) && defined($constant) ? constant($constant) : $value;
    }


    /**
     * Url of the current page in another language (parameter lang, see translations.php)
     * @return string
     */
    static function languageUrl(string $lang): string{
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $path = strtok($uri, '?');
        parse_str((string)parse_url($uri, PHP_URL_QUERY), $query);
        // A page reached by a form (POST) is opened again with GET: only its address is kept
        $query['lang'] = $lang;
        return $path.'?'.http_build_query($query);
    }


    /**
     * Buttons to change the language: FR | EN
     * @param string $class extra classes
     * @param string $button class of the buttons: btn-outline-secondary, btn-outline-light on the dark backgrounds
     * @return void
     */
    static function languageSwitch(string $class = '', string $button = 'btn-outline-secondary'): void{
        if(!defined('LANGUAGES') || count(LANGUAGES) < 2) return;
        ?>
        <nav class="btn-group btn-group-sm <?=$class?>" aria-label="<?=T_LANGUAGE?>">
            <?php foreach(LANGUAGES as $code => $name){ ?>
            <a class="btn <?=$button?> fw-semibold <?php if($code == LANG) echo 'active';?>" href="<?=htmlspecialchars(self::languageUrl($code))?>" hreflang="<?=$code?>" lang="<?=$code?>" title="<?=$name?>" <?php if($code == LANG) echo 'aria-current="true"';?>><?=strtoupper($code)?></a>
            <?php } ?>
        </nav>
        <?php
    }


    /**
     * Button adding a product to the cart of the shop
     * @param bool $withQuantity display a quantity field next to the button (page of the product)
     * @param bool $block button as wide as its container (card of the list of products)
     * @param int|null $stock units in stock: 0 shows a disabled button "Rupture de stock", the quantity cannot be
     *                        above the stock (the cart checks it again, see ModelCart). null: not known
     * @return void
     */
    static function addToCart(int $idProduct, bool $withQuantity = false, bool $block = false, ?int $stock = null): void{
        if($stock !== null && $stock < 1){
            ?>
            <button type="button" class="btn btn-outline-secondary fw-semibold <?=$withQuantity ? 'btn-lg w-100' : ($block ? 'w-100' : '')?> d-inline-flex align-items-center justify-content-center gap-2" disabled><?=self::icon('x-circle', 18)?> <?=T_OUT_OF_STOCK?></button>
            <?php
            return;
        }
        $max = $stock === null ? \Apgenic\Shop\ModelCart::MAX_QUANTITY : min(\Apgenic\Shop\ModelCart::MAX_QUANTITY, $stock);
        $formClass = $withQuantity ? 'row g-2' : ($block ? 'd-grid' : 'd-inline-block');
        ?>
        <form class="<?=$formClass?>" method="POST" action="<?=self::url('shop', 'add')?>">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <input type="hidden" name="id_product" value="<?=$idProduct?>" />
            <?php if($withQuantity){ ?>
            <div class="col-12 col-sm-5">
                <label class="visually-hidden" for="quantity_<?=$idProduct?>"><?=T_QUANTITY?></label>
                <div class="input-group input-group-lg flex-nowrap">
                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center px-3" aria-label="<?=T_LESS?>" onclick="this.parentNode.querySelector('input').stepDown()"><?=self::icon('dash', 16)?></button>
                    <input class="form-control text-center px-1" type="number" name="quantity" id="quantity_<?=$idProduct?>" value="1" min="1" max="<?=$max?>" step="1" inputmode="numeric" />
                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center px-3" aria-label="<?=T_MORE?>" onclick="this.parentNode.querySelector('input').stepUp()"><?=self::icon('plus', 16)?></button>
                </div>
            </div>
            <div class="col-12 col-sm-7">
                <button type="submit" class="btn btn-warning btn-lg fw-semibold w-100 h-100 d-flex align-items-center justify-content-center gap-2 text-nowrap"><?=self::icon('bag-plus', 20)?> <?=T_ADD_TO_CART?></button>
            </div>
            <?php } elseif($block){ ?>
            <button type="submit" class="btn btn-warning fw-semibold d-flex align-items-center justify-content-center gap-2 px-2"><span class="d-none d-sm-inline-flex"><?=self::icon('bag-plus', 18)?></span> <?=T_ADD_TO_CART?></button>
            <?php } else { ?>
            <button type="submit" class="btn btn-warning fw-semibold d-inline-flex align-items-center gap-2 text-nowrap"><?=self::icon('bag-plus', 18)?> <?=T_ADD_TO_CART?></button>
            <?php } ?>
        </form>
        <?php
    }


    /**
     * Icon of classes/Icons.php, in the color of the text
     * @param string $class classes of the <svg>
     * @return string <svg>
     */
    static function icon(string $name, int $size = 20, string $viewBox = '0 0 16 16', string $class = 'flex-shrink-0 align-text-bottom'): string{
        $paths = Icons::PATHS[$name] ?? self::ICONS[$name] ?? '';
        return '<svg class="'.$class.'" width="'.$size.'" height="'.$size.'" fill="currentColor" viewBox="'.$viewBox.'" aria-hidden="true" focusable="false">'.$paths.'</svg>';
    }


    /**
     * Logo of the site: mark + name
     * @param bool $inverse on a dark background (side menu, footer, page of connection)
     * @param string $class extra classes
     * @return void
     */
    static function brand(bool $inverse = false, string $class = ''): void{
        ?>
        <a class="d-inline-flex align-items-center gap-2 text-decoration-none <?=$inverse ? 'link-light' : 'link-body-emphasis'?> <?=$class?>" href="<?=Url::home()?>">
            <span class="d-inline-flex p-2 rounded-2 <?=$inverse ? 'text-bg-warning' : 'bg-primary text-warning'?>" aria-hidden="true"><?=self::icon('bag', 18)?></span>
            <span class="h5 fw-bold mb-0"><?=self::appName()?></span>
        </a>
        <?php
    }


    /**
     * Menu of the account of the connected user: items of a .dropdown-menu
     * @param bool $admin back office: link to the shop instead of the link to the back office
     * @return void
     */
    static function accountMenu(bool $admin = false): void{
        ?>
        <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?=self::url('user', 'profile')?>"><?=self::icon('person', 16)?> <?=T_MY_PROFILE?></a></li>
        <?php if($admin){ ?>
        <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?=Url::home()?>"><?=self::icon('shop', 16)?> <?=T_VIEW_SHOP?></a></li>
        <?php } elseif(Auth::canEdit()){ ?>
        <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?=Url::admin()?>"><?=self::icon('speedometer2', 16)?> <?=T_BACK_OFFICE?></a></li>
        <?php } else {
            // Consultation pages allowed by the permissions of the user (front-end: /browse/...)
            $links = '';
            foreach(self::MENU as $menuComponent => $item){
                if($menuComponent == 'product' || $menuComponent == 'user' || $menuComponent == 'config' || !Auth::can($menuComponent, 'viewlist')) continue;
                $links .= '<li><a class="dropdown-item d-flex align-items-center gap-2" href="'.self::url($menuComponent, 'viewlist').'">'.self::icon($item[1], 16).' '.$item[0].'</a></li>';
            }
            if($links != ''){
                echo '<li><hr class="dropdown-divider"></li><li><h6 class="dropdown-header">'.T_BROWSE.'</h6></li>'.$links;
            }
        } ?>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form method="POST" action="<?=self::url('user', 'logout')?>">
                <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                <button type="submit" class="dropdown-item d-flex align-items-center gap-2"><?=self::icon('box-arrow-right', 16)?> <?=T_SIGN_OUT?></button>
            </form>
        </li>
        <?php
    }


    /**
     * <head> of the pages: Bootstrap, then the theme
     * @return string
     */
    static function head(string $title): string{
        return '<!doctype html>
        <html lang="'.LANG.'">
          <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content="">
            <meta name="theme-color" content="'.self::THEME_COLOR.'">
            <title>'.ucfirst($title).' &middot; '.self::appName().'</title>
            <!-- htmx 2.0.4, served by the site (public/js): no script of another server without integrity check -->
            <script src="'.BASE_URL.'/js/htmx.min.js" integrity="sha384-HGfztofotfshcF7+8n44JQL2oJmowVChPTg48S+jvZoztPfvwD79OC/LTtG6dMp+"></script>
            <!-- Slug filled while the name is typed, in the forms of the back office (public/js/slug.js, served by the site) -->
            '.(Url::isAdmin() ? '<script src="'.BASE_URL.'/js/slug.js" defer></script>' : '').'
            <!-- Bootstrap 5, then the theme: colors, rounded corners, shadows, fonts -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
            <link href="'.BASE_URL.'/css/theme.css" rel="stylesheet">
            <style>
            .tight{
                width:10px;
            }
            </style>
          </head>';
    }


    /**
     * Scripts at the end of the pages
     * @return string
     */
    static function scripts(): string{
        return '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>';
    }


    /*
     * Beginning of the page: <head>, header or side menu, title of the page
     */
    function header(string $title){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        $layout = self::layout();
        echo self::head($title).'
          <body class="bg-body-tertiary'.($layout == 'shop' ? ' d-flex flex-column min-vh-100' : '').'">
            <a class="visually-hidden-focusable d-block p-2 text-center text-bg-primary" href="#main_content">'.T_SKIP_TO_CONTENT.'</a>
        ';
        $this->topNav();
        $this->leftNav();
        $this->mainHeader($title);
    }


    /**
     * Shop: header with the menu, the search, the account and the cart.
     * Back office: opening of the frame, the top bar is displayed by leftNav().
     */
    function topNav(){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        if(self::layout() == 'admin'){
            echo '<div class="container-fluid px-0"><div class="row g-0 min-vh-100">';
            return;
        }

        $cartCount = (new \Apgenic\Shop\ModelCart())->count();
        $component = defined('COMPONENT') ? COMPONENT : '';
        $catalogActive = $component == 'product' && in_array((string)($_GET['task'] ?? ''), array('viewlist', 'view'), true);
        $blogActive = $component == 'blog';
        ?>
        <header class="sticky-top bg-body border-bottom shadow-sm">
            <nav class="navbar navbar-expand-lg" aria-label="<?=T_MAIN_MENU?>">
                <div class="container">
                    <?php self::brand(false, 'me-lg-4'); ?>

                    <div class="d-flex align-items-center gap-2 ms-auto order-lg-last ms-lg-3">
                        <a class="btn btn-light rounded-circle position-relative d-inline-flex p-2 my-1 <?php if($component == 'shop') echo 'active';?>" href="<?=self::url('shop', 'cart')?>" aria-label="<?=sprintf($cartCount > 1 ? T_CART_COUNT : T_CART_COUNT_ONE, $cartCount)?>">
                            <?=self::icon('bag', 22)?>
                            <?php if($cartCount > 0){ ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-warning"><?=$cartCount?></span><?php } ?>
                        </a>
                        <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#shop_nav" aria-controls="shop_nav" aria-expanded="false" aria-label="<?=T_MENU?>">
                            <?=self::icon('list', 24)?>
                        </button>
                    </div>

                    <div class="collapse navbar-collapse" id="shop_nav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link fw-semibold <?php if($catalogActive) echo 'active';?>" href="<?=Url::home()?>" <?php if($catalogActive) echo 'aria-current="page"';?>><?=T_SHOP?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold <?php if($blogActive) echo 'active';?>" href="<?=self::url('blog', 'viewlist')?>" <?php if($blogActive) echo 'aria-current="page"';?>><?=T_BLOG?></a>
                            </li>
                        </ul>

                        <form class="col-lg-5 mx-lg-auto my-2 my-lg-0" method="get" action="<?=Url::home()?>" role="search">
                            <label class="visually-hidden" for="header_search"><?=T_SEARCH_PRODUCT?></label>
                            <div class="input-group">
                                <input class="form-control rounded-start-pill ps-3" type="search" name="text_search" id="header_search" value="<?=$component == 'product' ? htmlspecialchars((string)($_GET['text_search'] ?? '')) : ''?>" placeholder="<?=T_SEARCH_PRODUCT?>">
                                <button class="btn btn-light border rounded-end-pill px-3" type="submit" aria-label="<?=T_SEARCH?>"><?=self::icon('search', 16)?></button>
                            </div>
                        </form>

                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <?php self::languageSwitch(); ?>
                            <ul class="navbar-nav">
                            <?php if(isset($_SESSION['user'])){ ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="<?=self::url('user', 'profile')?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?=self::icon('person-circle', 22)?>
                                        <span><?=trim($_SESSION['user']['first_name'].' '.$_SESSION['user']['last_name'])?></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <?php self::accountMenu(); ?>
                                    </ul>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center gap-2" href="<?=self::url('user', 'login')?>"><?=self::icon('person', 20)?> <?=T_SIGN_IN?></a>
                                </li>
                            <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        <?php
    }


    /**
     * Back office: side menu and top bar. Only the components that the user can consult are in the menu.
     * Shop: nothing.
     * @param string $active component highlighted in the menu, the current one by default
     */
    function leftNav($active = ''){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        if(!isset($_GET['task'] )) $_GET['task'] = '';
        if(self::layout() != 'admin') return '';

        if($active == ''){ $active = defined('COMPONENT') ? COMPONENT : ''; }
        $active = self::MENU_PARENT[$active] ?? $active;
        ?>
        <aside class="col-lg-3 col-xl-2 offcanvas-lg offcanvas-start text-bg-dark" tabindex="-1" id="admin_sidebar" aria-label="<?=T_BACK_OFFICE_MENU?>">
            <div class="sticky-lg-top d-flex flex-column h-100 overflow-y-auto p-3">
                <div class="d-flex align-items-center gap-2 mb-3 px-2">
                    <?php self::brand(true); ?>
                    <button type="button" class="btn-close btn-close-white ms-auto d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#admin_sidebar" aria-label="<?=T_CLOSE?>"></button>
                </div>

                <nav class="flex-grow-1">
                    <?php
                    $section = '';
                    foreach(self::MENU as $component => $item){
                        // The edit lists allowed to the user (the users are managed by the administrators only)
                        if(!Auth::can($component, 'editlist')) continue;
                        if($item[2] != $section){
                            if($section != '') echo '</ul>';
                            $section = $item[2];
                            echo '<div class="small text-uppercase fw-semibold text-white-50 px-3 mt-3 mb-1">'.$section.'</div><ul class="nav nav-pills flex-column gap-1">';
                        }
                        $isActive = $active == $component;
                        ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2 link-light <?=$isActive ? 'active bg-white bg-opacity-10 fw-semibold' : 'link-opacity-75 link-opacity-100-hover'?>" href="<?=Url::admin($component)?>" <?php if($isActive) echo 'aria-current="page"';?>>
                                <span class="d-inline-flex <?php if($isActive) echo 'text-warning';?>"><?=self::icon($item[1], 18)?></span><?=$item[0]?>
                            </a>
                        </li>
                        <?php
                    }
                    if($section != '') echo '</ul>';
                    ?>
                </nav>

                <div class="border-top border-white border-opacity-10 pt-3 mt-4">
                    <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 link-light link-opacity-75 link-opacity-100-hover" href="<?=Url::home()?>"><?=self::icon('shop', 18)?> <?=T_VIEW_SHOP?></a>
                    <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 link-light link-opacity-75 link-opacity-100-hover" href="<?=self::url('blog', 'viewlist')?>"><?=self::icon('newspaper', 18)?> <?=T_VIEW_BLOG?></a>
                </div>
            </div>
        </aside>

        <div class="col-lg-9 col-xl-10 d-flex flex-column">
            <header class="sticky-top bg-body border-bottom">
                <div class="d-flex align-items-center gap-2 px-3 px-lg-4 py-2">
                    <button type="button" class="btn btn-light d-inline-flex d-lg-none p-2" data-bs-toggle="offcanvas" data-bs-target="#admin_sidebar" aria-controls="admin_sidebar" aria-label="<?=T_MENU?>"><?=self::icon('list', 24)?></button>
                    <div class="d-lg-none"><?php self::brand(); ?></div>
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <?php self::languageSwitch(); ?>
                        <a class="btn btn-light btn-sm d-none d-sm-inline-flex align-items-center gap-2" href="<?=self::url('shop', 'cart')?>"><?=self::icon('bag', 16)?> <?=T_CART?></a>
                        <?php if(isset($_SESSION['user'])){ ?>
                        <div class="dropdown">
                            <a class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" href="<?=self::url('user', 'profile')?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?=self::icon('person-circle', 18)?>
                                <span class="d-none d-md-inline"><?=trim($_SESSION['user']['first_name'].' '.$_SESSION['user']['last_name'])?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <?php self::accountMenu(true); ?>
                            </ul>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </header>
        <?php
    }


    /**
     * Header of the content: title of the page and, in the back office, the links Edit list / Trash / New.
     * The views (view, viewlist) are only in the front-end: no link to them in the back office.
     * Opens #main_content, the zone replaced by HTMX
     */
    function mainHeader($title){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        $component = defined('COMPONENT') ? COMPONENT : '';
        $task = (string)($_GET['task'] ?? '');
        $admin = self::layout() == 'admin';

        // Only the links allowed by the permissions of the user are displayed (the access is checked again by index.php)
        $canEditList = $admin && $this->listLinks && Auth::can($component, 'editlist');
        $canTrash = $admin && $this->listLinks && $this->trash && Auth::can($component, 'trashedlist');
        $canAdd = $admin && $this->listLinks && Auth::can($component, 'edit');
        // The title is a link to the list: the edit list in the back office, the consultation list in the front-end
        $listTask = $component == '' ? '' : ($admin ? ($canEditList ? 'editlist' : '') : ($this->listLinks && Auth::can($component, 'viewlist') ? 'viewlist' : ''));

        if($admin){
            echo '<main id="main" class="flex-grow-1 p-3 p-lg-4">';
        }
        else{
            echo '<main id="main" class="flex-grow-1 py-4 py-lg-5"><div class="container">';
        }
        ?>
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <?php if($admin){ ?>
                <div class="small text-uppercase fw-semibold text-body-secondary mb-1"><?=$this->eyebrow != '' ? $this->eyebrow : (self::MENU[self::MENU_PARENT[$component] ?? $component][2] ?? T_BACK_OFFICE)?></div>
                <?php } else { ?>
                <nav aria-label="<?=T_BREADCRUMB?>">
                    <ol class="breadcrumb small mb-2">
                        <li class="breadcrumb-item"><a class="link-secondary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=Url::home()?>"><?=T_HOME?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?=ucfirst($title)?></li>
                    </ol>
                </nav>
                <?php } ?>
                <h1 class="h2 fw-bold mb-0">
                    <?php if($listTask != '' && strtolower($task) != $listTask){ ?>
                    <a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-50-hover" href="<?=self::url($component, $listTask)?>"><?=ucfirst($title)?></a>
                    <?php } else { ?>
                    <?=ucfirst($title)?>
                    <?php } ?>
                </h1>
            </div>

            <?php if($canEditList || $canAdd){ ?>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php if($canEditList && $canTrash){ ?>
                <nav class="btn-group" aria-label="<?=T_LISTS?>">
                    <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 <?php if($task == 'editlist') echo 'active';?>" href="<?=self::url($component, 'editlist')?>" title="<?=T_EDIT_LIST?>" <?php if($task == 'editlist') echo 'aria-current="page"';?>><?=self::icon('pencil', 14)?> <span class="d-none d-sm-inline"><?=T_EDIT_LIST?></span></a>
                    <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 <?php if($task == 'trashedlist') echo 'active';?>" href="<?=self::url($component, 'trashedlist')?>" title="<?=T_TRASH?>" <?php if($task == 'trashedlist') echo 'aria-current="page"';?>><?=self::icon('trash3', 14)?> <span class="d-none d-sm-inline"><?=T_TRASH?></span></a>
                </nav>
                <?php } ?>
                <?php if($canAdd){ ?>
                <a href="<?=self::url($component, 'edit')?>" class="btn btn-primary d-inline-flex align-items-center gap-2"><?=self::icon('plus-lg', 16)?> <?=T_NEW_ENTRY?></a>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <div id="main_content">
        <?php
    }


    /**
     * Search bar of the lists: a field and its value, and a text search
     * @return void
     */
    public function search(array $fieldsList = [], $task = 'viewlist'):void{
        $fieldSearch = $_GET['field_search'] ?? '';
        $fieldSearchValue = $_GET['field_search_value'] ?? '';
        $textSearch = $_GET['text_search'] ?? '';
        $component = defined('COMPONENT') ? COMPONENT : '';
        $active = $fieldSearch != '' || $fieldSearchValue != '' || $textSearch != '';
        ?>
        <form class="card card-body shadow-sm p-2 mb-3" method="get" action="<?=self::url($component, $task)?>" role="search" aria-label="<?=T_SEARCH?>">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md">
                    <label class="visually-hidden" for="search_text"><?=T_TEXT_SEARCH?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-body text-body-secondary"><?=self::icon('search', 16)?></span>
                        <input class="form-control" name="text_search" id="search_text" value="<?=htmlspecialchars((string)$textSearch)?>" type="search" placeholder="<?=T_SEARCH?>">
                    </div>
                </div>
                <div class="col-12 col-md">
                    <label class="visually-hidden" for="search_field"><?=T_FIELD?></label>
                    <label class="visually-hidden" for="search_value"><?=T_VALUE?></label>
                    <div class="input-group">
                        <select name="field_search" id="search_field" class="form-select">
                            <option value=""><?=T_FILTER_ON_FIELD?></option>
                            <?php foreach ($fieldsList as $field) { ?>
                            <option <?php if($fieldSearch == $field) echo 'selected';?> value="<?=$field?>"><?=self::fieldLabel($field)?></option>
                            <?php } ?>
                        </select>
                        <input class="form-control" name="field_search_value" id="search_value" value="<?=htmlspecialchars((string)$fieldSearchValue)?>" type="search" placeholder="<?=T_VALUE?>">
                    </div>
                </div>
                <div class="col-12 col-md-auto d-flex gap-2">
                    <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit"><?=self::icon('funnel', 16)?> <?=T_SEARCH?></button>
                    <?php if($active){ ?>
                    <a class="btn btn-link" href="<?=self::url($component, $task)?>"><?=T_RESET?></a>
                    <?php } ?>
                </div>
            </div>
        </form>
        <?php
    }


    /**
     * End of the content: closes #main_content and the frame of the layout
     */
    function mainFooter(){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        if(self::layout() == 'admin'){
            ?>
            </div><!-- end of #main_content -->
            </main>
            </div><!-- end of the column of the content -->
            </div><!-- end of .row -->
            </div><!-- end of .container-fluid -->
            <?php
        }
        else{
            ?>
            </div><!-- end of #main_content -->
            </div><!-- end of .container -->
            </main>
            <?php
        }
    }


    /**
     * End of the page: footer of the shop, scripts
     */
    function footer(){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';

        $this->mainFooter();

        if(self::layout() == 'shop'){
            $link = 'link-light link-opacity-75 link-opacity-100-hover link-underline-opacity-0 link-underline-opacity-75-hover';
            ?>
            <footer class="text-bg-dark pt-5 pb-4">
                <div class="container">
                    <div class="row g-4">
                        <div class="col-lg-5">
                            <?php self::brand(true); ?>
                            <p class="small text-white-50 mt-3"><?=sprintf(T_FOOTER_VAT, self::taxRateText())?></p>
                            <?php self::languageSwitch('', 'btn-outline-light'); ?>
                        </div>
                        <div class="col-6 col-lg-2 offset-lg-1">
                            <h2 class="h6 text-uppercase fw-semibold text-white-50 mb-3"><?=T_SHOP?></h2>
                            <ul class="list-unstyled d-grid gap-2 mb-0">
                                <li><a class="<?=$link?>" href="<?=Url::home()?>"><?=T_ALL_PRODUCTS?></a></li>
                                <li><a class="<?=$link?>" href="<?=self::url('shop', 'cart')?>"><?=T_CART?></a></li>
                                <li><a class="<?=$link?>" href="<?=self::url('shop', 'checkout')?>"><?=T_CHECKOUT_TITLE?></a></li>
                                <li><a class="<?=$link?>" href="<?=self::url('blog', 'viewlist')?>"><?=T_BLOG?></a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-lg-2">
                            <h2 class="h6 text-uppercase fw-semibold text-white-50 mb-3"><?=T_ACCOUNT?></h2>
                            <ul class="list-unstyled d-grid gap-2 mb-0">
                                <?php if(isset($_SESSION['user'])){ ?>
                                <li><a class="<?=$link?>" href="<?=self::url('user', 'profile')?>"><?=T_MY_PROFILE?></a></li>
                                <?php } else { ?>
                                <li><a class="<?=$link?>" href="<?=self::url('user', 'login')?>"><?=T_SIGN_IN?></a></li>
                                <li><a class="<?=$link?>" href="<?=self::url('user', 'register')?>"><?=T_CREATE_ACCOUNT?></a></li>
                                <?php } ?>
                                <li><a class="<?=$link?>" href="<?=self::url('user', 'forgot')?>"><?=T_FORGOT_PASSWORD?></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="border-top border-white border-opacity-10 small text-white-50 pt-4 mt-4">
                        &copy; <?=date('Y')?> <?=self::appName()?>
                    </div>
                </div>
            </footer>
            <?php
        }
        echo self::scripts();
        ?>
        <script>
            // Tabs above the forms (loaded by HTMX): the clicked tab becomes the active one
            document.addEventListener('click', function(evt){
                const tab = evt.target.closest('#tabs_list .nav-link');
                if(!tab) return;
                document.querySelectorAll('#tabs_list .nav-link').forEach(function(item){
                    item.classList.remove('active');
                    item.removeAttribute('aria-current');
                });
                tab.classList.add('active');
                tab.setAttribute('aria-current', 'page');
            });
        </script>
        </body>
        </html>
        <?php
    }


    /**
     * Links to the pages of a list: first, last, the pages around the current one, previous and next
     * @param string $link url ending with & or ?, the number of the page is added.
     *                     An address written ?component=...&task=... is changed into the url of the page (Url::fromQuery)
     */
    function pagination($numberOfPages, $curentPage, $link){
        $numberOfPages = (int)$numberOfPages;
        $curentPage = max(1, min((int)$curentPage, max(1, $numberOfPages)));
        if($numberOfPages <= 1) return;

        $link = Url::fromQuery((string)$link);
        $link .= strpos($link, '?') === false ? '?' : (in_array(substr($link, -1), array('?', '&'), true) ? '' : '&');

        // Pages displayed: 1, the current one and its neighbours, the last one
        $pages = array();
        for($i = 1; $i <= $numberOfPages; $i++){
            if($i == 1 || $i == $numberOfPages || abs($i - $curentPage) <= 1){
                $pages[] = $i;
            }
        }
        ?>
        <nav class="mt-3" aria-label="<?=T_PAGINATION?>">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?php if($curentPage == 1) echo 'disabled';?>">
                    <a class="page-link" href="<?=$link?>page=<?=$curentPage - 1?>" aria-label="<?=T_PREVIOUS_PAGE?>" <?php if($curentPage == 1) echo 'tabindex="-1" aria-disabled="true"';?>><?=self::icon('chevron-left', 14)?></a>
                </li>
                <?php
                $previous = 0;
                foreach($pages as $i){
                    if($i - $previous > 1){
                        echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                    }
                    $previous = $i;
                    ?>
                    <li class="page-item <?php if($i == $curentPage) echo 'active';?>">
                        <a class="page-link" href="<?=$link?>page=<?=$i?>" <?php if($i == $curentPage) echo 'aria-current="page"';?>><?=$i?></a>
                    </li>
                    <?php
                }
                ?>
                <li class="page-item <?php if($curentPage == $numberOfPages) echo 'disabled';?>">
                    <a class="page-link" href="<?=$link?>page=<?=$curentPage + 1?>" aria-label="<?=T_NEXT_PAGE?>" <?php if($curentPage == $numberOfPages) echo 'tabindex="-1" aria-disabled="true"';?>><?=self::icon('chevron-right', 14)?></a>
                </li>
            </ul>
        </nav>
        <?php
    }

    /**
     * Message: array('type' => success|danger|warning|info|secondary, 'text' => ...)
     */
    function message($message = null){
        if(is_array($message) && isset($message['text'])){
            $icons = array('success' => 'check-circle', 'danger' => 'x-circle', 'warning' => 'exclamation-triangle', 'info' => 'info-circle');
            $type = (string)($message['type'] ?? 'info');
            ?>
            <div class="alert alert-<?=$type?> d-flex align-items-start gap-2" role="<?=in_array($type, array('danger', 'warning'), true) ? 'alert' : 'status'?>">
                <?=self::icon($icons[$type] ?? 'info-circle', 20)?>
                <div><?=$message['text']?></div>
            </div>
            <?php
        }
    }

    /**
     * Badge in soft colors: status of an order, of a product...
     * @param string $type success|warning|danger|info|primary|secondary
     * @return string
     */
    static function badge(string $text, string $type = 'secondary'): string{
        return '<span class="badge rounded-pill bg-'.$type.'-subtle text-'.$type.'-emphasis border border-'.$type.'-subtle">'.$text.'</span>';
    }


    /**
     * The slug field of a form follows the name while it is typed (public/js/slug.js): when it is empty, or for a
     * new entry when it is the slug of the name. The slug of an entry already saved does not change by itself: its
     * address stays the same.
     * @return bool
     */
    static function slugFollowsName(string $slug, string $name, bool $isNew): bool{
        return $slug === '' || ($isNew && $slug === Url::slug($name));
    }


    /**
     * Choices of a drop-down list: the rows that are not in the trash (column deleted_at empty).
     * The current value of the field stays in the choices even when it is in the trash: the form
     * shows it and saving the form does not lose it.
     * @param array $list rows of a model, by id (ModelXxx::getList)
     * @param mixed $current value of the field in the edited row, null when there is none
     * @return array
     */
    static function notTrashed(array $list, $current = null): array{
        return array_filter($list, function($row, $id) use ($current){
            return empty($row['deleted_at']) || ($current !== null && $current !== '' && (string)$id === (string)$current);
        }, ARRAY_FILTER_USE_BOTH);
    }


    /**
     * Nothing to display: icon, title, text and a link
     * @param array $link array(url, label) or array()
     * @return void
     */
    static function emptyState(string $icon, string $title, string $text = '', array $link = array()): void{
        ?>
        <div class="card card-body shadow-sm text-center py-5 px-4 mb-4">
            <div class="d-inline-flex rounded-circle bg-primary-subtle text-primary mx-auto mb-3 p-3"><?=self::icon($icon, 28)?></div>
            <h2 class="h5 fw-bold"><?=$title?></h2>
            <?php if($text != ''){ ?>
            <p class="text-body-secondary mb-0"><?=$text?></p>
            <?php } ?>
            <?php if(count($link) == 2){ ?>
            <div class="mt-3"><a class="btn btn-primary" href="<?=$link[0]?>"><?=$link[1]?></a></div>
            <?php } ?>
        </div>
        <?php
    }


}
