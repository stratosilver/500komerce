<?php
/**
 * Created by PhpStorm.
 * User: TILMED199
 * Date: 11-05-21
 * Time: 19:18
 * Project: exsilicon
 *
 * Layout of all the pages (Bootstrap 4.6.2 + public/css/theme.css + public/css/styles.css).
 * Two layouts, chosen by layout():
 *   shop   the visitors and the customers, and the pages of the shop for everybody: header with the cart, footer
 *   admin  the back office (users who can edit): side menu, top bar
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
    ];
    /** Components that are not in the menu (reached by the tabs): the entry of the menu that is highlighted */
    const MENU_PARENT = [
        'productmedia'      => 'product',
        'productcategory'   => 'product',
        'productvariant'    => 'product',
        'cartitem'          => 'customerorder',
        'customerorderitem' => 'customerorder',
    ];

    /** Color of the bar of the mobile browsers (<meta name="theme-color">): the same as --k-brand of public/css/theme.css */
    const THEME_COLOR = '#002B59';

    /** Links of the back office in the header of the page: Edit list, View list, Trash, New */
    protected bool $listLinks = true;
    /** The component has a list of the deleted entries (task trashedlist) */
    protected bool $trash = false;
    /** Small title above the title of the page in the back office, the section of the menu by default */
    protected string $eyebrow = '';


    /**
     * Name of the site, APP_NAME in config.php
     * @return string
     */
    static function appName(): string{
        return defined('APP_NAME') ? APP_NAME : '100Komerce';
    }


    /**
     * Layout of the page.
     *   'shop'  for the visitors and the customers, and the pages of the shop (catalog, product, cart, checkout) for everybody
     *   'admin' for the back office: the users who can edit (PERMISSION_EDIT)
     * @return string
     */
    static function layout(): string{
        $component = defined('COMPONENT') ? COMPONENT : '';
        $task = (string)($_GET['task'] ?? '');
        $shopPage = in_array($component, array('shop', 'accesscode'), true)
                 || ($component == 'product' && in_array($task, array('viewlist', 'view'), true));
        return (!Auth::canEdit() || $shopPage) ? 'shop' : 'admin';
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
     * Links to change the language: FR | EN
     * @param string $class extra class
     * @return void
     */
    static function languageSwitch(string $class = ''): void{
        if(!defined('LANGUAGES') || count(LANGUAGES) < 2) return;
        ?>
        <nav class="k-lang <?=$class?>" aria-label="<?=T_LANGUAGE?>">
            <?php foreach(LANGUAGES as $code => $name){ ?>
            <a class="<?php if($code == LANG) echo 'active';?>" href="<?=htmlspecialchars(self::languageUrl($code))?>" hreflang="<?=$code?>" lang="<?=$code?>" title="<?=$name?>" <?php if($code == LANG) echo 'aria-current="true"';?>><?=strtoupper($code)?></a>
            <?php } ?>
        </nav>
        <?php
    }


    /**
     * Button adding a product to the cart of the shop
     * @param bool $withQuantity display a quantity field next to the button (page of the product)
     * @param bool $block button as wide as its container (card of the list of products)
     * @return void
     */
    static function addToCart(int $idProduct, bool $withQuantity = false, bool $block = false): void{
        ?>
        <form class="k-add-to-cart <?=$block ? 'd-block' : ''?> <?=$withQuantity ? 'k-add-to-cart-lg' : ''?>" method="POST" action="<?=BASE_URL?>/index.php?component=shop&task=add">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <input type="hidden" name="id_product" value="<?=$idProduct?>" />
            <?php if($withQuantity){ ?>
            <div class="k-qty">
                <button type="button" class="k-qty-btn" aria-label="<?=T_LESS?>" onclick="this.parentNode.querySelector('input').stepDown()"><?=self::icon('dash', 16)?></button>
                <label class="sr-only" for="quantity_<?=$idProduct?>"><?=T_QUANTITY?></label>
                <input class="k-qty-input" type="number" name="quantity" id="quantity_<?=$idProduct?>" value="1" min="1" max="99" step="1" inputmode="numeric" />
                <button type="button" class="k-qty-btn" aria-label="<?=T_MORE?>" onclick="this.parentNode.querySelector('input').stepUp()"><?=self::icon('plus', 16)?></button>
            </div>
            <?php } ?>
            <button type="submit" class="btn btn-accent text-nowrap <?=$block ? 'btn-block' : ''?> <?=$withQuantity ? 'btn-lg' : ''?>"><?=self::icon('bag-plus', $withQuantity ? 20 : 18)?> <?=T_ADD_TO_CART?></button>
        </form>
        <?php
    }


    /**
     * Icon of classes/Icons.php, in the color of the text
     * @return string <svg>
     */
    static function icon(string $name, int $size = 20, string $viewBox = '0 0 16 16'): string{
        $paths = Icons::PATHS[$name] ?? self::ICONS[$name] ?? '';
        return '<svg class="icon icon-'.$name.'" width="'.$size.'" height="'.$size.'" fill="currentColor" viewBox="'.$viewBox.'" aria-hidden="true" focusable="false">'.$paths.'</svg>';
    }


    /**
     * Logo of the site: mark + name
     * @param string $class extra class
     * @return void
     */
    static function brand(string $class = ''): void{
        ?>
        <a class="k-brand <?=$class?>" href="<?=BASE_URL?>/">
            <span class="k-brand-mark" aria-hidden="true"><?=self::icon('bag', 18)?></span>
            <span class="k-brand-name"><?=self::appName()?></span>
        </a>
        <?php
    }


    /*
     * Beginning of the page: <head>, header or side menu, title of the page
     */
    function header(string $title){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        $layout = self::layout();
        echo '<!doctype html>
        <html lang="'.LANG.'">
          <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="">
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta name="theme-color" content="'.self::THEME_COLOR.'">
            <title>'.ucfirst($title).' &middot; '.self::appName().'</title>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/htmx/2.0.4/htmx.min.js"></script>
            <!-- Bootstrap core CSS, then the theme (design tokens) and the styles of the site -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
            <link href="'.BASE_URL.'/css/theme.css" rel="stylesheet">
            <link href="'.BASE_URL.'/css/styles.css" rel="stylesheet">
          </head>
          <body class="k-layout-'.$layout.'">
            <a class="k-skip-link" href="#main_content">'.T_SKIP_TO_CONTENT.'</a>
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
            echo '<div class="k-admin">';
            return;
        }

        $cartCount = (new \Apgenic\Shop\ModelCart())->count();
        $component = defined('COMPONENT') ? COMPONENT : '';
        $catalogActive = $component == 'product' && in_array((string)($_GET['task'] ?? ''), array('viewlist', 'view'), true);
        ?>
        <header class="k-shop-header">
            <nav class="navbar navbar-expand-lg k-navbar" aria-label="<?=T_MAIN_MENU?>">
                <div class="container k-container">
                    <?php self::brand(); ?>

                    <div class="k-header-tools order-lg-last">
                        <a class="k-cart-button <?php if($component == 'shop') echo 'active';?>" href="<?=BASE_URL?>/index.php?component=shop&task=cart" aria-label="<?=sprintf($cartCount > 1 ? T_CART_COUNT : T_CART_COUNT_ONE, $cartCount)?>">
                            <?=self::icon('bag', 22)?>
                            <?php if($cartCount > 0){ ?><span class="k-cart-count"><?=$cartCount?></span><?php } ?>
                        </a>
                        <button class="navbar-toggler k-icon-button" type="button" data-toggle="collapse" data-target="#k_shop_nav" aria-controls="k_shop_nav" aria-expanded="false" aria-label="<?=T_MENU?>">
                            <?=self::icon('list', 24)?>
                        </button>
                    </div>

                    <div class="collapse navbar-collapse" id="k_shop_nav">
                        <ul class="navbar-nav k-main-nav">
                            <li class="nav-item">
                                <a class="nav-link <?php if($catalogActive) echo 'active';?>" href="<?=BASE_URL?>/index.php?component=product&task=viewlist" <?php if($catalogActive) echo 'aria-current="page"';?>><?=T_SHOP?></a>
                            </li>
                        </ul>

                        <form class="k-header-search" method="get" action="<?=BASE_URL?>/index.php" role="search">
                            <input type="hidden" name="component" value="product">
                            <input type="hidden" name="task" value="viewlist">
                            <label class="sr-only" for="k_header_search"><?=T_SEARCH_PRODUCT?></label>
                            <?=self::icon('search', 16)?>
                            <input class="form-control" type="search" name="text_search" id="k_header_search" value="<?=$component == 'product' ? htmlspecialchars((string)($_GET['text_search'] ?? '')) : ''?>" placeholder="<?=T_SEARCH_PRODUCT?>">
                        </form>

                        <?php self::languageSwitch('k-lang-header'); ?>

                        <ul class="navbar-nav k-account-nav">
                        <?php if(isset($_SESSION['user'])){
                            $name = trim($_SESSION['user']['first_name'].' '.$_SESSION['user']['last_name']);
                            ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle k-account-toggle" href="<?=BASE_URL?>/index.php?component=user&task=profile" id="k_account_menu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="k-avatar" aria-hidden="true"><?=mb_strtoupper(mb_substr((string)$_SESSION['user']['first_name'], 0, 1).mb_substr((string)$_SESSION['user']['last_name'], 0, 1))?></span>
                                    <span class="k-account-name"><?=$name?></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="k_account_menu">
                                    <a class="dropdown-item" href="<?=BASE_URL?>/index.php?component=user&task=profile"><?=self::icon('person', 16)?> <?=T_MY_PROFILE?></a>
                                    <?php if(Auth::canEdit()){ ?>
                                    <a class="dropdown-item" href="<?=BASE_URL?>/index.php"><?=self::icon('speedometer2', 16)?> <?=T_BACK_OFFICE?></a>
                                    <?php } else {
                                        // Consultation pages allowed by the permissions of the user
                                        $links = '';
                                        foreach(self::MENU as $menuComponent => $item){
                                            if($menuComponent == 'product' || !Auth::can($menuComponent, $menuComponent == 'user' ? 'editlist' : 'viewlist')) continue;
                                            $links .= '<a class="dropdown-item" href="'.BASE_URL.'/index.php?component='.$menuComponent.'">'.self::icon($item[1], 16).' '.$item[0].'</a>';
                                        }
                                        if($links != ''){
                                            echo '<div class="dropdown-divider"></div><h6 class="dropdown-header">'.T_BROWSE.'</h6>'.$links;
                                        }
                                    } ?>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=logout">
                                        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                                        <button type="submit" class="dropdown-item"><?=self::icon('box-arrow-right', 16)?> <?=T_SIGN_OUT?></button>
                                    </form>
                                </div>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item">
                                <a class="nav-link k-signin-link" href="<?=BASE_URL?>/index.php?component=user&task=login"><?=self::icon('person', 20)?> <span><?=T_SIGN_IN?></span></a>
                            </li>
                        <?php } ?>
                        </ul>
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
        <aside class="k-sidebar" id="k_sidebar" aria-label="<?=T_BACK_OFFICE_MENU?>">
            <div class="k-sidebar-brand">
                <?php self::brand('k-brand-inverse'); ?>
                <span class="k-sidebar-tag"><?=T_ADMIN_TAG?></span>
            </div>

            <nav class="k-sidebar-nav">
                <?php
                $section = '';
                foreach(self::MENU as $component => $item){
                    // The users are managed by the administrators only
                    if(!Auth::can($component, $component == 'user' ? 'editlist' : 'viewlist')) continue;
                    if($item[2] != $section){
                        if($section != '') echo '</ul>';
                        $section = $item[2];
                        echo '<div class="k-sidebar-section">'.$section.'</div><ul class="nav flex-column">';
                    }
                    $isActive = $active == $component;
                    ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if($isActive) echo 'active';?>" href="<?=BASE_URL?>/index.php?component=<?=$component?>" <?php if($isActive) echo 'aria-current="page"';?>>
                            <?=self::icon($item[1], 18)?><span><?=$item[0]?></span>
                        </a>
                    </li>
                    <?php
                }
                if($section != '') echo '</ul>';
                ?>
            </nav>

            <div class="k-sidebar-footer">
                <a class="nav-link" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=self::icon('shop', 18)?><span><?=T_VIEW_SHOP?></span></a>
            </div>
        </aside>
        <div class="k-sidebar-backdrop" data-k-sidebar-close></div>

        <div class="k-admin-body">
            <header class="k-admin-topbar">
                <button type="button" class="k-icon-button k-sidebar-toggle" aria-controls="k_sidebar" aria-expanded="false" aria-label="<?=T_MENU?>" data-k-sidebar-toggle><?=self::icon('list', 24)?></button>
                <div class="k-topbar-brand"><?php self::brand(); ?></div>
                <div class="k-topbar-spacer"></div>
                <?php self::languageSwitch(); ?>
                <a class="k-topbar-link d-none d-sm-inline-flex" href="<?=BASE_URL?>/index.php?component=shop&task=cart"><?=self::icon('bag', 18)?> <?=T_CART?></a>
                <?php if(isset($_SESSION['user'])){ ?>
                <div class="dropdown">
                    <a class="k-account-toggle dropdown-toggle" href="<?=BASE_URL?>/index.php?component=user&task=profile" id="k_admin_account" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="k-avatar" aria-hidden="true"><?=mb_strtoupper(mb_substr((string)$_SESSION['user']['first_name'], 0, 1).mb_substr((string)$_SESSION['user']['last_name'], 0, 1))?></span>
                        <span class="k-account-name"><?=$_SESSION['user']['first_name'].' '.$_SESSION['user']['last_name']?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="k_admin_account">
                        <a class="dropdown-item" href="<?=BASE_URL?>/index.php?component=user&task=profile"><?=self::icon('person', 16)?> <?=T_MY_PROFILE?></a>
                        <a class="dropdown-item" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=self::icon('shop', 16)?> <?=T_VIEW_SHOP?></a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=logout">
                            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                            <button type="submit" class="dropdown-item"><?=self::icon('box-arrow-right', 16)?> <?=T_SIGN_OUT?></button>
                        </form>
                    </div>
                </div>
                <?php } ?>
            </header>
        <?php
    }


    /**
     * Header of the content: title of the page and, in the back office, the links Edit list / View list / Trash / New
     * Opens #main_content, the zone replaced by HTMX
     */
    function mainHeader($title){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        $component = defined('COMPONENT') ? COMPONENT : '';
        $task = (string)($_GET['task'] ?? '');

        // Only the links allowed by the permissions of the user are displayed (the access is checked again by index.php)
        $canEditList = $this->listLinks && Auth::can($component, 'editlist');
        $canViewList = $this->listLinks && Auth::can($component, 'viewlist');
        $canTrash = $this->listLinks && $this->trash && Auth::can($component, 'trashedlist');
        $canAdd = $this->listLinks && Auth::can($component, 'edit');
        $listUrl = $canEditList ? 'editlist' : ($canViewList ? 'viewlist' : '');
        $base = BASE_URL.'/index.php?component='.$component.'&task=';

        if(self::layout() == 'admin'){
            echo '<main id="main" class="k-admin-main">';
        }
        else{
            echo '<main id="main" class="k-shop-main"><div class="container k-container">';
        }
        ?>
        <div class="k-page-header">
            <div class="k-page-heading">
                <?php if(self::layout() == 'admin'){ ?>
                <div class="k-eyebrow"><?=$this->eyebrow != '' ? $this->eyebrow : (self::MENU[self::MENU_PARENT[$component] ?? $component][2] ?? T_BACK_OFFICE)?></div>
                <?php } else { ?>
                <nav aria-label="<?=T_BREADCRUMB?>">
                    <ol class="breadcrumb k-breadcrumb">
                        <li class="breadcrumb-item"><a href="<?=BASE_URL?>/"><?=T_HOME?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?=ucfirst($title)?></li>
                    </ol>
                </nav>
                <?php } ?>
                <h1 class="k-page-title">
                    <?php if($listUrl != '' && $task != $listUrl){ ?>
                    <a href="<?=$base.$listUrl?>"><?=ucfirst($title)?></a>
                    <?php } else { ?>
                    <?=ucfirst($title)?>
                    <?php } ?>
                </h1>
            </div>

            <?php if($canEditList || $canViewList || $canAdd){ ?>
            <div class="k-page-actions">
                <?php if(($canEditList ? 1 : 0) + ($canViewList ? 1 : 0) + ($canTrash ? 1 : 0) > 1){ ?>
                <nav class="k-segmented" aria-label="<?=T_LISTS?>">
                    <?php if($canEditList){ ?>
                    <a class="<?php if($task == 'editlist') echo 'active';?>" href="<?=$base?>editlist" <?php if($task == 'editlist') echo 'aria-current="page"';?>><?=self::icon('pencil', 14)?> <?=T_EDIT_LIST?></a>
                    <?php } ?>
                    <?php if($canViewList){ ?>
                    <a class="<?php if($task == 'viewlist') echo 'active';?>" href="<?=$base?>viewlist" <?php if($task == 'viewlist') echo 'aria-current="page"';?>><?=self::icon('eye', 14)?> <?=T_VIEW_LIST?></a>
                    <?php } ?>
                    <?php if($canTrash){ ?>
                    <a class="<?php if($task == 'trashedlist') echo 'active';?>" href="<?=$base?>trashedlist" <?php if($task == 'trashedlist') echo 'aria-current="page"';?>><?=self::icon('trash3', 14)?> <?=T_TRASH?></a>
                    <?php } ?>
                </nav>
                <?php } ?>
                <?php if($canAdd){ ?>
                <a href="<?=$base?>edit" class="btn btn-primary"><?=self::icon('plus-lg', 16)?> <?=T_NEW_ENTRY?></a>
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
        <form class="k-filterbar" method="get" action="<?=BASE_URL?>/index.php" role="search" aria-label="<?=T_SEARCH?>">
            <div class="k-filterbar-text">
                <label class="sr-only" for="k_text_search"><?=T_TEXT_SEARCH?></label>
                <?=self::icon('search', 16)?>
                <input class="form-control" name="text_search" id="k_text_search" value="<?=htmlspecialchars((string)$textSearch)?>" type="search" placeholder="<?=T_SEARCH?>">
            </div>
            <div class="k-filterbar-field">
                <label class="sr-only" for="k_field_search"><?=T_FIELD?></label>
                <select name="field_search" id="k_field_search" class="custom-select">
                    <option value=""><?=T_FILTER_ON_FIELD?></option>
                    <?php foreach ($fieldsList as $field) { ?>
                    <option <?php if($fieldSearch == $field) echo 'selected';?> value="<?=$field?>"><?=self::fieldLabel($field)?></option>
                    <?php } ?>
                </select>
                <label class="sr-only" for="k_field_search_value"><?=T_VALUE?></label>
                <input class="form-control" name="field_search_value" id="k_field_search_value" value="<?=htmlspecialchars((string)$fieldSearchValue)?>" type="search" placeholder="<?=T_VALUE?>">
            </div>
            <input type="hidden" name="component" value="<?=$component?>">
            <input type="hidden" name="task" value="<?=$task?>">
            <div class="k-filterbar-actions">
                <button class="btn btn-primary" type="submit"><?=self::icon('funnel', 16)?> <?=T_SEARCH?></button>
                <?php if($active){ ?>
                <a class="btn btn-link" href="<?=BASE_URL?>/index.php?component=<?=$component?>&task=<?=$task?>"><?=T_RESET?></a>
                <?php } ?>
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
            </div><!-- end of .k-admin-body -->
            </div><!-- end of .k-admin -->
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
            ?>
            <footer class="k-shop-footer">
                <div class="container k-container">
                    <div class="row">
                        <div class="col-lg-5 mb-4">
                            <?php self::brand('k-brand-inverse'); ?>
                            <p class="k-footer-text"><?=sprintf(T_FOOTER_VAT, self::taxRateText())?></p>
                            <?php self::languageSwitch('k-lang-footer'); ?>
                        </div>
                        <div class="col-6 col-lg-2 offset-lg-1 mb-4">
                            <h2 class="k-footer-title"><?=T_SHOP?></h2>
                            <ul class="list-unstyled k-footer-links">
                                <li><a href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=T_ALL_PRODUCTS?></a></li>
                                <li><a href="<?=BASE_URL?>/index.php?component=shop&task=cart"><?=T_CART?></a></li>
                                <li><a href="<?=BASE_URL?>/index.php?component=shop&task=checkout"><?=T_CHECKOUT_TITLE?></a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-lg-2 mb-4">
                            <h2 class="k-footer-title"><?=T_ACCOUNT?></h2>
                            <ul class="list-unstyled k-footer-links">
                                <?php if(isset($_SESSION['user'])){ ?>
                                <li><a href="<?=BASE_URL?>/index.php?component=user&task=profile"><?=T_MY_PROFILE?></a></li>
                                <?php } else { ?>
                                <li><a href="<?=BASE_URL?>/index.php?component=user&task=login"><?=T_SIGN_IN?></a></li>
                                <li><a href="<?=BASE_URL?>/index.php?component=user&task=register"><?=T_CREATE_ACCOUNT?></a></li>
                                <?php } ?>
                                <li><a href="<?=BASE_URL?>/index.php?component=user&task=forgot"><?=T_FORGOT_PASSWORD?></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="k-footer-bottom">
                        <span>&copy; <?=date('Y')?> <?=self::appName()?></span>
                    </div>
                </div>
            </footer>
            <?php
        }
        ?>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
        <script>
            // Tabs above the forms: the clicked tab becomes the active one
            document.addEventListener('htmx:afterSwap', function(evt) {
                const items = document.querySelectorAll('#tabs_list a');
                items.forEach(item => {
                    item.addEventListener('click', () => {
                        items.forEach(i => i.classList.remove('active'));
                        item.classList.add('active');
                    });
                });
            });
            // Back office: side menu of the small screens
            document.querySelectorAll('[data-k-sidebar-toggle], [data-k-sidebar-close]').forEach(function(button){
                button.addEventListener('click', function(){
                    const open = document.body.classList.toggle('k-sidebar-open');
                    document.querySelectorAll('[data-k-sidebar-toggle]').forEach(b => b.setAttribute('aria-expanded', open ? 'true' : 'false'));
                });
            });
        </script>
        </body>
        </html>
        <?php
    }


    /**
     * Links to the pages of a list: first, last, the pages around the current one, previous and next
     * @param string $link url ending with & or ?, the number of the page is added
     */
    function pagination($numberOfPages, $curentPage, $link){
        $numberOfPages = (int)$numberOfPages;
        $curentPage = max(1, min((int)$curentPage, max(1, $numberOfPages)));
        if($numberOfPages <= 1) return;

        // Pages displayed: 1, the current one and its neighbours, the last one
        $pages = array();
        for($i = 1; $i <= $numberOfPages; $i++){
            if($i == 1 || $i == $numberOfPages || abs($i - $curentPage) <= 1){
                $pages[] = $i;
            }
        }
        ?>
        <nav class="k-pagination" aria-label="<?=T_PAGINATION?>">
            <ul class="pagination">
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
            <div class="alert alert-<?=$type?> k-alert" role="<?=in_array($type, array('danger', 'warning'), true) ? 'alert' : 'status'?>">
                <?=self::icon($icons[$type] ?? 'info-circle', 20)?>
                <div><?=$message['text']?></div>
            </div>
            <?php
        }
    }


}
