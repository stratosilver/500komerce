<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\Product;

class ViewProduct extends \Apgenic\Classes\ViewTemplate {
/** The component has a list of the deleted entries (link Trash of the header) */
protected bool $trash = true;
var $userList2 = array();

public array $filters = array();
public string $filtersGet = '';
/** Images of the displayed products: id_product => list of media.filename (ModelProduct::getImages) */
public array $images = array();
/** Catalog: the categories with their numbers of products (ModelProduct::getCatalogCategories), the chosen one, the number of all the products */
public array $categories = array();
public ?array $category = null;
public int $catalogCount = 0;

public $fileters = array();

function __construct($userList2) {
$this->userList2 = $userList2;
}


/**
* Display tabs above form or detail page of an record
* Tabs can display lists of decords of an other table having a forign key with the
* displayed record.
* @param int $id
* @param string $activeTab
* @return void
*/
public static function tabs(int $id, string $activeTab){
?>

    <ul id="tabs_list" class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a href="#" id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=product&task=edit&id_product=<?=$id?>"><?=T_TAB_INFO?></a>
      </li>

        <li class="nav-item">
            <a href="#" id="tab_0" class="nav-link <?php if($activeTab == 'productmedia') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=productmedia&task=childlist&filters[id_product]=<?=$id ?>&showTabs=0"><?=T_MENU_PRODUCTMEDIA?></a>
        </li>

        <li class="nav-item">
            <a href="#" id="tab_1" class="nav-link <?php if($activeTab == 'productcategory') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=productcategory&task=childlist&filters[id_product]=<?=$id ?>&showTabs=0"><?=T_MENU_PRODUCTCATEGORY?></a>
        </li>

        <li class="nav-item">
            <a href="#" id="tab_2" class="nav-link <?php if($activeTab == 'cartitem') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=cartitem&task=childlist&filters[id_product_variant]=<?=$id ?>&showTabs=0"><?=T_MENU_CARTITEM?></a>
        </li>

        <li class="nav-item">
            <a href="#" id="tab_3" class="nav-link <?php if($activeTab == 'customerorder') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=customerorder&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0"><?=T_MENU_CUSTOMERORDER?></a>
        </li>

    </ul>



<?php
}
/**
 * Edit form for the table product
 * @parameter ModelProduct $data
 * @parameter int $showTabs
 * @parameter array $message
 * @return void
 */
public function edit(ModelProduct $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_product, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>

		<form class="card card-body shadow-sm p-lg-4 mb-4" method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">

        <?php // The user of the product is not chosen here: it is the logged user (ControllerProduct::edit) ?>

        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="name"><?=T_FIELD_NAME?>&nbsp;*</label>
		<input required class="form-control" type="text" name="name" id="name" size="10" value="<?=htmlentities((string)(string)$data->name)?>" data-slug-target="slug"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="slug"><?=T_FIELD_SLUG?></label>
		<input  class="form-control" type="text" name="slug" id="slug" size="10" maxlength="200" value="<?=htmlentities((string)(string)$data->slug)?>"
		        data-slug-auto="<?=self::slugFollowsName((string)$data->slug, (string)$data->name, !($data->id_product > 0)) ? '1' : '0'?>" aria-describedby="slug_help"/>
		<div class="form-text" id="slug_help"><?=T_FIELD_SLUG_HELP?></div>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="summary"><?=T_FIELD_SUMMARY?></label>
		<input  class="form-control" type="text" name="summary" id="summary" size="10" value="<?=htmlentities((string)(string)$data->summary)?>"/>
	    </div>
	    </div>


        <?php
        // The price is typed with tax. The price without tax is computed by the browser from the rate of the VAT
        // (page Paramètres), the same way as \Apgenic\Shop\ModelCart::priceWithoutTax(); the rate is not editable here.
        $taxRate = \Apgenic\Shop\ModelCart::taxRate();
        ?>
        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="price"><?=T_FIELD_PRICE_WITH_TAX?></label>
		<div class="input-group">
		<input  class="form-control text-end" type="number" min="0" step="0.01" name="price" id="price" data-tax-rate="<?=$taxRate?>" value="<?=number_format((int)$data->price_amount / 100, 2, '.', '')?>"
		        oninput="var r = Math.round((parseFloat(this.dataset.taxRate) || 0) * 100), v = parseFloat(String(this.value).replace(',', '.')), o = document.getElementById('price_without_tax'); if(o){ o.value = isNaN(v) ? '' : (Math.round(Math.round(v * 100) * 10000 / (10000 + r)) / 100).toFixed(2).replace('.', ','); }"/>
		<span class="input-group-text"><?=CURRENCY?></span>
		</div>
	    </div>
	    </div>


        <div class="col-lg-2 col-md-6"><div class="mb-3">
		<label class="form-label" for="tax_rate"><?=T_FIELD_TAX_RATE?></label>
		<div class="input-group">
		<input  class="form-control text-end" type="text" id="tax_rate" value="<?=self::taxRateText()?>" disabled aria-describedby="tax_rate_help"/>
		<span class="input-group-text">%</span>
		</div>
		<div class="form-text" id="tax_rate_help"><?php if(\Apgenic\Classes\Auth::can('config', 'edit')){ ?><a href="<?=self::url('config', 'edit')?>"><?=T_FIELD_TAX_RATE_HELP?></a><?php } else { echo T_FIELD_TAX_RATE_HELP; } ?></div>
	    </div>
	    </div>


        <div class="col-lg-2 col-md-6"><div class="mb-3">
		<label class="form-label" for="price_without_tax"><?=T_FIELD_PRICE_WITHOUT_TAX?></label>
		<div class="input-group">
		<input  class="form-control text-end bg-body-tertiary" type="text" id="price_without_tax" value="<?=number_format(\Apgenic\Shop\ModelCart::priceWithoutTax((int)$data->price_amount) / 100, 2, ',', '')?>" readonly tabindex="-1" aria-describedby="price_without_tax_help"/>
		<span class="input-group-text"><?=CURRENCY?></span>
		</div>
		<div class="form-text" id="price_without_tax_help"><?=T_FIELD_PRICE_WITHOUT_TAX_HELP?></div>
	    </div>
	    </div>


        <div class="col-12"><div class="mb-3">
		<label class="form-label" for="description"><?=T_FIELD_DESCRIPTION?></label>
		<textarea  class="form-control" name="description" id="description" rows="4" cols="49"><?=$data->description?></textarea>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
            <label class="form-label" for="status"><?=T_FIELD_STATUS?></label>
            <select required="" class="form-select" name="status" id="status">
                <option value=""></option>

                <option value="draft" <?php if($data->status == 'draft') echo 'selected="selected"' ?>><?=T_VALUE_DRAFT?></option>
                <option value="active" <?php if($data->status == 'active') echo 'selected="selected"' ?>><?=T_VALUE_ACTIVE?></option>
                <option value="archived" <?php if($data->status == 'archived') echo 'selected="selected"' ?>><?=T_VALUE_ARCHIVED?></option>
            </select>
		</div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="published_at"><?=T_FIELD_PUBLISHED_AT?></label>
		<input  class="form-control" type="datetime-local" name="published_at" id="published_at" value="<?=htmlentities(str_replace(' ', 'T', substr((string)$data->published_at, 0, 16)))?>" aria-describedby="published_at_help"/>
		<div class="form-text" id="published_at_help"><?=T_FIELD_PUBLISHED_AT_HELP?></div>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="stock"><?=T_FIELD_STOCK?></label>
		<input  class="form-control" type="number" min="0" step="1" name="stock" id="stock" value="<?=(int)$data->stock?>" aria-describedby="stock_help"/>
		<div class="form-text" id="stock_help"><?=T_FIELD_STOCK_HELP?></div>
	    </div>
	    </div>

		<div class="col-12">

		<div class="border-top pt-3">
		<input type="hidden" name="id_product" id="id" value="<?=$data->id_product ?>"/>
		<input type="submit" name="Envoyer" title="<?=T_SAVE?>" value="<?=T_SAVE?>" class="btn btn-primary">
		</div>
		</div>


    </div>
	</fieldset>
	</form>
	<?php
	// End of #core_content
	if($coreContent){
	    echo '</div>';
	}
	?>
	</div>
	<?php

	}



/**
 * Search bar. The back office keeps the search by field; in the shop, the search is in the header of the page.
 * @return void
 */
public function search(array $fieldsList = [], $task = 'viewlist'):void{
    if(self::layout() == 'admin' || $task != 'viewlist'){
        parent::search($fieldsList, $task);
    }
}


/**
 * Header of the pages of the shop: title of the catalog, path to the product. The back office keeps the usual header.
 * @return void
 */
function mainHeader($title){
    $task = (string)($_GET['task'] ?? '');
    if(self::layout() == 'admin' || !in_array($task, array('viewlist', 'view'), true)){
        parent::mainHeader($title);
        return;
    }

    $catalogUrl = self::url('product', 'viewlist');
    $search = trim((string)($_GET['text_search'] ?? ''));
    echo '<main id="main" class="flex-grow-1 py-4 py-lg-5"><div class="container">';

    if($task == 'viewlist'){
        // Chosen category: the name of its main category above its name
        $parent = $this->category !== null ? ($this->categories[$this->category['id_parent']] ?? null) : null;
        ?>
        <div class="bg-primary text-white rounded-4 shadow p-4 p-lg-5 mb-4">
            <div class="small text-uppercase fw-semibold text-warning mb-2"><?=$parent !== null ? $parent['name'] : T_SHOP?></div>
            <?php if($search != ''){ ?>
            <h1 class="display-6 fw-bold mb-2"><?=sprintf(T_RESULTS_FOR, htmlspecialchars($search))?></h1>
            <a class="icon-link link-light link-opacity-75 link-opacity-100-hover" href="<?=htmlspecialchars($this->catalogUrl(array('text_search' => '')))?>"><?=self::icon('x-lg', 12)?> <?=T_CLEAR_SEARCH?></a>
            <?php } elseif($this->category !== null){ ?>
            <h1 class="display-6 fw-bold mb-0"><?=$this->category['name']?></h1>
            <?php } else { ?>
            <h1 class="display-6 fw-bold mb-0"><?=T_ALL_PRODUCTS?></h1>
            <?php } ?>
        </div>
        <?php
    }
    else{
        // The name of the product is displayed by view()
        ?>
        <nav aria-label="<?=T_BREADCRUMB?>">
            <ol class="breadcrumb small mb-4">
                <li class="breadcrumb-item"><a class="link-secondary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'viewlist')?>"><?=T_HOME?></a></li>
                <li class="breadcrumb-item"><a class="link-secondary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=$catalogUrl?>"><?=T_SHOP?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
            </ol>
        </nav>
        <?php
    }
    echo '<div id="main_content">';
}

/**
 * Url of an image of a product.
 * The image linked to the product (component productmedia) when there is one, otherwise an illustration
 * (PRODUCT_ILLUSTRATION_URL in config.php).
 * Only the images whose file is in public/images/ are counted: a row of the table media without its file
 * is skipped, so it never hides a real image of the product behind the illustration.
 * @param int $index which image of the product, 0 = the first one
 * @param string $size one of the sizes of \Apgenic\Media\MediaImage::SIZES
 * @return string '' when there is nothing to display
 */
public function imageUrl(int $idProduct, int $index = 0, string $size = 'medium'):string{
    $dir = \Apgenic\Media\MediaImage::dir().DIRECTORY_SEPARATOR;
    $files = array();
    foreach($this->images[$idProduct] ?? array() as $name){
        if($name != '' && preg_match('/^[A-Za-z0-9._-]+$/', $name) && is_file($dir.$name)){
            $files[] = $name;
        }
    }
    $filename = $files[$index] ?? '';

    if($filename != ''){
        // The resized copy when it exists, otherwise the original file
        if(is_file($dir.\Apgenic\Media\MediaImage::variant($filename, $size))){
            return \Apgenic\Media\MediaImage::url($filename, $size);
        }
        return \Apgenic\Media\MediaImage::url($filename);
    }

    if($index == 0 && defined('PRODUCT_ILLUSTRATION_URL') && PRODUCT_ILLUSTRATION_URL != ''){
        $pixels = \Apgenic\Media\MediaImage::SIZES[$size][0] ?? 800;
        return sprintf(PRODUCT_ILLUSTRATION_URL, $idProduct, $pixels, $pixels);
    }
    return '';
}


/**
 * Address of the page of a product: /product/{slug}, /product/{id} when it has no slug
 * @param array|ModelProduct $product row of the list, or the product
 * @return string
 */
public static function productUrl($product):string{
    $product = (array)$product;
    return self::url('product', 'view', array(
        'slug' => html_entity_decode((string)($product['slug'] ?? ''), ENT_QUOTES),
        'id_product' => (int)($product['id_product'] ?? 0),
    ));
}


/**
 * Price without tax (the prices are stored with tax, they are the ones paid by the customer)
 * @return int cents
 */
public static function priceWithoutTax(int $priceAmount):int{
    return \Apgenic\Shop\ModelCart::priceWithoutTax($priceAmount);
}


/**
 * Status of a product as a colored badge (back office)
 * @return string
 */
public static function statusBadge(string $status, bool $deleted = false):string{
    if($deleted){
        return self::badge(T_VALUE_DELETED, 'danger');
    }
    $types = array('active' => 'success', 'draft' => 'warning', 'archived' => 'secondary');
    return self::badge(self::value($status), $types[$status] ?? 'secondary');
}


/**
 * Url of the catalog: the category, the search and the sort of the page are kept, unless they are given in $params
 * @param array $params category (slug, '' for all the products), text_search, orderBy, order
 * @return string / or /category/{slug}, with the search and the sort in the query string
 */
public function catalogUrl(array $params = array()):string{
    $orderBy = (string)($_GET['orderBy'] ?? '');
    $query = array(
        'category'    => $this->category !== null ? html_entity_decode((string)$this->category['slug'], ENT_QUOTES) : '',
        'text_search' => trim((string)($_GET['text_search'] ?? '')),
        'orderBy'     => in_array($orderBy, ControllerProduct::$fieldsNames, true) ? $orderBy : '',
        'order'       => ($_GET['order'] ?? '') == 'desc' ? 'desc' : (($_GET['order'] ?? '') == 'asc' ? 'asc' : ''),
    );
    foreach($params as $name => $value){
        $query[$name] = (string)$value;
    }
    return self::url('product', 'viewlist', $query);
}


/**
 * Categories of the catalog as buttons: the main categories, then the sub-categories of the chosen one.
 * Only the categories that have products are displayed, with their number of products (not during a search).
 * @return void
 */
private function categoryNav():void{
    $categories = $this->categories;
    $current = $this->category;
    // Main category of the chosen one
    $branch = $current;
    for($depth = 0; $branch !== null && isset($categories[$branch['id_parent']]) && $depth < 10; $depth++){
        $branch = $categories[$branch['id_parent']];
    }
    // A category is displayed when it has products, or when it is the chosen one (or contains it)
    $visible = function($category) use ($current, $branch){
        return $category['count'] > 0
            || ($current !== null && $current['id_category'] == $category['id_category'])
            || ($branch !== null && $branch['id_category'] == $category['id_category']);
    };

    $main = array_filter($categories, function($category) use ($categories, $visible){
        return !isset($categories[$category['id_parent']]) && $visible($category);
    });
    if(!count($main)){
        return;
    }
    // Second row: the sub-categories of the chosen category, or its sisters when it has none
    $subParent = null;
    if($current !== null){
        $subParent = count($current['children']) ? $current : ($categories[$current['id_parent']] ?? null);
    }
    $subCategories = $subParent === null ? array() : array_filter(array_map(function($id) use ($categories){ return $categories[$id]; }, $subParent['children']), $visible);

    $withCount = trim((string)($_GET['text_search'] ?? '')) == '';
    $button = function(string $label, string $slug, int $count, bool $active, string $size = '') use ($withCount){
        ?>
        <a class="btn <?=$size?> rounded-pill d-inline-flex align-items-center gap-2 <?=$active ? 'btn-primary' : 'btn-light border'?>" href="<?=htmlspecialchars($this->catalogUrl(array('category' => html_entity_decode($slug, ENT_QUOTES))))?>" <?php if($active) echo 'aria-current="true"';?>>
            <?=$label?>
            <?php if($withCount){ ?><span class="badge rounded-pill <?=$active ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary-emphasis'?>"><?=$count?></span><?php } ?>
        </a>
        <?php
    };
    ?>
    <nav class="mb-4" aria-label="<?=T_CATEGORIES?>">
        <div class="d-flex flex-wrap gap-2">
            <?php
            $button(T_ALL_CATEGORIES, '', $this->catalogCount, $current === null);
            foreach($main as $category){
                $button((string)$category['name'], (string)$category['slug'], (int)$category['count'], $branch !== null && $branch['id_category'] == $category['id_category']);
            }
            ?>
        </div>
        <?php if(count($subCategories)){ ?>
        <div class="d-flex flex-wrap align-items-center gap-2 border-start border-3 border-primary-subtle ps-3 mt-3">
            <?php
            $button(T_SEE_ALL, (string)$subParent['slug'], (int)$subParent['count'], $current['id_category'] == $subParent['id_category'], 'btn-sm');
            foreach($subCategories as $category){
                $button((string)$category['name'], (string)$category['slug'], (int)$category['count'], $current['id_category'] == $category['id_category'], 'btn-sm');
            }
            ?>
        </div>
        <?php } ?>
    </nav>
    <?php
}


/**
 * Catalog of the shop: the products as a grid of cards, with image, price and "Add to cart"
 * @parameter array $data rows of the table product
 * @parameter string $orderBy current sort field
 * @parameter string $order the opposite of the current order (asc / desc)
 * @parameter array $message
 * @return void
 */
public function viewList(array $data ,  string $orderBy='', string $order='desc', array $message=null){
    $this->message($message);

    $canEdit = \Apgenic\Classes\Auth::canEdit();
    $currentOrder = $order == 'asc' ? 'desc' : 'asc';
    $sorts = array(
        T_SORT_NEWEST => array('id_product', 'desc'),
        T_SORT_PRICE_ASC => array('price_amount', 'asc'),
        T_SORT_PRICE_DESC => array('price_amount', 'desc'),
        T_SORT_NAME => array('name', 'asc'),
    );
    $search = trim((string)($_GET['text_search'] ?? ''));
    $this->categoryNav();
	?>
	<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
	    <span class="text-body-secondary"><?=sprintf(count($data) > 1 ? T_PRODUCTS_ON_PAGE : T_PRODUCTS_ON_PAGE_ONE, count($data))?></span>
	    <nav class="d-flex flex-wrap align-items-center gap-2" aria-label="<?=T_SORT_PRODUCTS?>">
	        <span class="small fw-semibold text-body-secondary"><?=T_SORT_BY?></span>
	        <?php foreach($sorts as $label => $sort){
	            $active = $orderBy == $sort[0] && $currentOrder == $sort[1]; ?>
	        <a class="btn btn-sm rounded-pill <?=$active ? 'btn-primary' : 'btn-light border'?>" href="<?=htmlspecialchars($this->catalogUrl(array('orderBy' => $sort[0], 'order' => $sort[1])))?>" <?php if($active) echo 'aria-current="true"';?>><?=$label?></a>
	        <?php } ?>
	    </nav>
	</div>




	<?php if(!count($data)){
	    $filtered = $search != '' || $this->category !== null;
	    self::emptyState('search', T_NO_PRODUCT, $search != '' ? T_NO_PRODUCT_HINT : ($this->category !== null ? T_NO_PRODUCT_CATEGORY : ''), $filtered ? array(self::url('product', 'viewlist'), T_SEE_ALL_PRODUCTS) : array());
	} ?>

	<div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 g-lg-4">
	<?php
	foreach ($data as $element){
	    $id = (int)$element['id_product'];
	    $url = self::productUrl($element);
	    $image = $this->imageUrl($id, 0, 'medium');
	    $price = (int)$element['price_amount'];
	    $onSale = $element['status'] == 'active';
		?>
		<div class="col">
		    <article class="card h-100 shadow-sm overflow-hidden">
		        <div class="position-relative">
		            <a class="ratio ratio-1x1 d-block bg-body-secondary" href="<?=$url?>" tabindex="-1" aria-hidden="true">
		                <?php if($image != ''){ ?>
		                <img class="object-fit-cover" src="<?=htmlspecialchars($image)?>" alt="" loading="lazy">
		                <?php } else { ?>
		                <span class="d-flex align-items-center justify-content-center text-body-tertiary"><?=self::icon('image', 32)?></span>
		                <?php } ?>
		            </a>
		            <?php if($canEdit && !$onSale){ ?>
		            <span class="position-absolute top-0 start-0 m-2"><?=self::statusBadge((string)$element['status'])?></span>
		            <?php } elseif($onSale && (int)$element['stock'] < 1){ ?>
		            <span class="position-absolute top-0 start-0 m-2"><?=self::badge(T_OUT_OF_STOCK, 'danger')?></span>
		            <?php } ?>
		        </div>
		        <div class="card-body d-flex flex-column p-3">
		            <h2 class="h6 fw-bold mb-1"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=$url?>"><?=$element['name']?></a></h2>
		            <?php if((string)$element['summary'] != ''){ ?>
		            <p class="small text-body-secondary mb-2"><?=$element['summary']?></p>
		            <?php } ?>
		            <div class="mt-auto pt-1">
		                <div class="fs-5 fw-bold lh-sm"><?=self::money($price)?></div>
		                <div class="small text-body-secondary"><?=self::money(self::priceWithoutTax($price))?> <?=T_EXCL_TAX?></div>
		            </div>
		        </div>
		        <div class="card-footer bg-transparent border-0 px-3 pb-3 pt-0">
		            <?php if($onSale){
		                self::addToCart($id, false, true, (int)$element['stock']);
		            } else { ?>
		            <button type="button" class="btn btn-light w-100" disabled><?=T_NOT_ON_SALE?></button>
		            <?php } ?>
		        </div>
		    </article>
		</div>
		<?php
	}?>
	</div>
	<?php
}





/**
 * Page of a product of the shop: images, name, price, "Add to cart", description
 *
 * @parameter ModelProduct $data
 * @parameter array $message
 * @return void
 */
public function view(ModelProduct $data , array $message=null){
        $this->message($message);

	if ($data->id_product != null){
	    $id = (int)$data->id_product;
	    $canEdit = \Apgenic\Classes\Auth::canEdit();
	    $onSale = $data->status == 'active' && $data->deleted_at === null;
	    $price = (int)$data->price_amount;
	    $taxRate = self::taxRateText();

	    // All the images of the product, or one illustration
	    $gallery = array();
	    for($i = 0; $i < max(1, count($this->images[$id] ?? array())); $i++){
	        $large = $this->imageUrl($id, $i, 'medium');
	        if($large != ''){
	            $gallery[] = array('large' => $large, 'thumb' => $this->imageUrl($id, $i, 'thumb'));
	        }
	    }
	?>

    <div class="row g-4 g-lg-5 mb-5">
        <div class="col-lg-7">
            <div class="ratio ratio-1x1 card shadow-sm overflow-hidden bg-body-secondary">
                <?php if(count($gallery)){ ?>
                <img id="product_image" class="object-fit-cover" src="<?=htmlspecialchars($gallery[0]['large'])?>" alt="<?=$data->name?>">
                <?php } else { ?>
                <span class="d-flex align-items-center justify-content-center text-body-tertiary"><?=self::icon('image', 48)?></span>
                <?php } ?>
            </div>
            <?php if(count($gallery) > 1){ ?>
            <div class="row row-cols-5 row-cols-sm-6 g-2 mt-1">
                <?php foreach($gallery as $n => $picture){ ?>
                <div class="col">
                    <a class="ratio ratio-1x1 d-block rounded-3 overflow-hidden border border-2 <?=$n == 0 ? 'border-primary' : 'border-light-subtle'?>" href="<?=htmlspecialchars($picture['large'])?>" <?php if($n == 0) echo 'aria-current="true"';?> onclick="document.getElementById('product_image').src = this.href; this.closest('.row').querySelectorAll('a').forEach(a => { a.classList.replace('border-primary', 'border-light-subtle'); a.removeAttribute('aria-current'); }); this.classList.replace('border-light-subtle', 'border-primary'); this.setAttribute('aria-current', 'true'); return false;">
                        <img class="object-fit-cover" src="<?=htmlspecialchars($picture['thumb'])?>" alt="<?=sprintf(T_IMAGE_N, $data->name, $n + 1)?>" loading="lazy">
                    </a>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>


        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <?php if($canEdit && !$onSale){ ?>
                    <div class="mb-2"><?=self::statusBadge((string)$data->status, $data->deleted_at !== null)?></div>
                    <?php } ?>
                    <h1 class="h2 fw-bold"><?=$data->name?></h1>
                    <?php if((string)$data->summary != ''){ ?>
                    <p class="lead fs-6 text-body-secondary"><?=$data->summary?></p>
                    <?php } ?>

                    <div class="border-top border-bottom py-3 my-4">
                        <div class="display-6 fw-bold lh-1"><?=self::money($price)?></div>
                        <div class="small text-body-secondary mt-2"><?=self::money(self::priceWithoutTax($price))?> <?=T_EXCL_TAX?> &middot; <?=sprintf(T_VAT_INCLUDED, $taxRate)?></div>
                        <?php if($onSale && (int)$data->stock < 1){ ?>
                        <div class="mt-3"><?=self::badge(T_OUT_OF_STOCK, 'danger')?></div>
                        <?php } ?>
                    </div>

                    <?php if($onSale){
                        self::addToCart($id, true, false, (int)$data->stock);
                    } else {
                        $this->message(array('type' => 'secondary', 'text' => T_PRODUCT_NOT_ON_SALE));
                    } ?>

                    <ul class="list-unstyled d-grid gap-2 small mt-4 mb-0">
                        <li class="d-flex align-items-center gap-2"><span class="d-inline-flex text-primary"><?=self::icon('bag-check', 18)?></span> <?=T_FEATURE_GUEST?></li>
                        <li class="d-flex align-items-center gap-2"><span class="d-inline-flex text-primary"><?=self::icon('truck', 18)?></span> <?=T_FEATURE_DELIVERY?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php if((string)$data->description != ''){ ?>
    <section class="mb-5">
        <h2 class="h4 fw-bold mb-3"><?=T_FIELD_DESCRIPTION?></h2>
        <div class="card card-body shadow-sm p-4"><div class="col-xl-9"><?=nl2br((string)$data->description)?></div></div>
    </section>
    <?php } ?>

    <?php if($canEdit){ // Back office ?>
    <section class="card shadow-sm border-primary-subtle mb-4">
        <div class="card-header bg-primary-subtle d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
            <h2 class="h6 fw-bold mb-0 d-flex align-items-center gap-2"><?=self::icon('speedometer2', 18)?> <?=T_BACK_OFFICE?></h2>
            <a class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2" href="<?=self::url('product', 'edit', array('id_product' => $id))?>"><?=self::icon('pencil', 14)?> <?=T_EDIT?></a>
        </div>
        <div class="card-body">
            <dl class="row row-cols-2 row-cols-md-4 g-3 mb-0">
                <?php
                $meta = array(
                    T_FIELD_ID => $data->id_product,
                    T_FIELD_ID_USER => htmlspecialchars((string)($this->userList2[$data->id_user]['email'] ?? '')),
                    T_FIELD_SLUG => $data->slug,
                    T_FIELD_STATUS => self::statusBadge((string)$data->status, $data->deleted_at !== null),
                    T_FIELD_PUBLISHED_AT => $data->published_at,
                    T_FIELD_CREATED_AT => $data->created_at,
                    T_FIELD_UPDATED_AT => $data->updated_at,
                    T_FIELD_DELETED_AT => $data->deleted_at,
                );
                foreach($meta as $label => $value){ ?>
                <div class="col">
                    <dt class="small text-uppercase fw-semibold text-body-secondary"><?=$label?></dt>
                    <dd class="mb-0 text-break"><?=(string)$value != '' ? $value : '&ndash;'?></dd>
                </div>
                <?php } ?>
            </dl>
        </div>
    </section>
    <?php } ?>
	<?php
    }
    else{
        self::emptyState('emoji-frown', T_PRODUCT_UNAVAILABLE, '', array(self::url('product', 'viewlist'), T_SEE_ALL_PRODUCTS));
    }



}




/**
 * Display rows from the table product
 * @parameter int $pkVal
 * @parameter string $task
 * @parameter int $value
 * @return void
 */
public function buttonToggle(int $pkVal, string $task, int $value = 0){
    $buttonTyp[0] = 'secondary';
    $buttonTyp[1] = 'success';
    $buttonIcon[0] = self::icon('x-lg', 16);
    $buttonIcon[1] = self::icon('check2', 16);

    ?>
    <a href="#" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=<?=$task?>&value=<?php echo $value?>&id_product=<?php echo $pkVal;?>" class="btn btn-sm btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>
    <?php
}

/**
 * Display rows from the table product with edit and del buttons
 * @parameter array $data ,
 * @parameter string $orderBy
 * @parameter string $order
 * @parameter array $message
 * @return void
 */
public function editList(array $data ,  string $orderBy='',string $order='desc',array $message=null){
    $this->message($message);


	?>
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_product" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'editlist')?>?orderBy=id_product&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" class="tight"><span class="visually-hidden"><?=T_IMAGE?></span></th>
	<th scope="col" id="id_user" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'editlist')?>?orderBy=id_user&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_USER?></a></th>
	<th scope="col" id="name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'editlist')?>?orderBy=name&<?=$this->filtersGet?>&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>"><?=T_FIELD_NAME?></a></th>
	<th scope="col" id="price_amount" class="text-nowrap text-end"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'editlist')?>?orderBy=price_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "price_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_PRICE_WITH_TAX?></a></th>
	<th scope="col" id="stock" class="text-nowrap text-end"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'editlist')?>?orderBy=stock&<?=$this->filtersGet?>&order=<?php if($orderBy == "stock") echo $order; else echo 'asc';?>"><?=T_FIELD_STOCK?></a></th>
	<th scope="col" id="status" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'editlist')?>?orderBy=status&<?=$this->filtersGet?>&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'editlist')?>?orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'editlist')?>?orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
		<th><a href="<?=self::url('product', 'trashedlist')?>" class="btn btn-sm btn-outline-secondary" title="<?=T_TRASH?>" aria-label="<?=T_TRASH?>"><?=self::icon('trash3', 16)?></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?>">
		<td class="text-nowrap"><?php echo $element['id_product']?></td>
		<td class="tight"><?php
		    // Thumbnail of the first image, a grey tile when the product has none
		    $thumb = $this->imageUrl((int)$element['id_product'], 0, 'thumb');
		    if($thumb != ''){ ?><img class="d-block rounded-2 border bg-body object-fit-cover" width="48" height="48" src="<?=htmlspecialchars($thumb)?>" alt="" loading="lazy"><?php }
		    else { ?><span class="d-flex align-items-center justify-content-center rounded-2 border bg-body-secondary text-body-tertiary" style="width:48px;height:48px"><?=self::icon('image', 20)?></span><?php } ?></td>
		<td >
                                            <a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo htmlspecialchars((string)($this->userList2[$element['id_user']]['email'] ?? ''));?>
                                            </a>
                                            </td>

		<td ><?php echo $element['name']?></td>
		<td class="text-end text-nowrap"><?=self::money((int)$element['price_amount'])?></td>
		<td class="text-end text-nowrap"><?=(int)$element['stock'] > 0 ? (int)$element['stock'] : self::badge('0', 'danger')?></td>
		<td ><?=self::statusBadge((string)$element['status'])?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=edit&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('pencil', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_product']?>" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=logicaldeleteHtmx&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>">
            <?=self::icon('x-lg', 16);?>
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>

	</table>
	</div>
	<?php
}


/**
 * Display rows from the table product with edit and del buttons
 * specificly to be displayed below a tab
 * @parameter array $data ,
 * @parameter array $filters
 * @parameter string $orderBy
 * @parameter string $order
 * @parameter array $message
 * @return void
 */
public function childList(array $data , array $filters, string $orderBy='',string $order='desc',array $message=null){
    $this->message($message);
    $parentPK = array_key_first($filters);
    $parentPKValue = rawurlencode((string)($filters[$parentPK] ?? ''));


	?>
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_product" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_user" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID_USER?></a></th>
	<th scope="col" id="name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_NAME?></a></th>
	<th scope="col" id="status" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?>">
		<td class="text-nowrap"><?php echo $element['id_product']?></td>
		<td >
                                            <a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo htmlspecialchars((string)($this->userList2[$element['id_user']]['email'] ?? ''));?>
                                            </a>
                                            </td>

		<td ><?php echo $element['name']?></td>
		<td ><?=self::statusBadge((string)$element['status'])?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childList&showTabs=0&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('screwdriver', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_product']?>" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=delHtmx&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>"><?=self::icon('trash3', 16)?></a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="14" class="text-end">
	    <a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?= BASE_URL ?>/htmx.php?component=<?=COMPONENT?>&task=edit&showTabs=0&<?=$parentPK?>=<?=$parentPKValue?>&<?=$this->filtersGet?>" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
        <?=self::icon('plus-lg', 16);?> <?=T_ADD?>
    </a>
    </td>
	</tr>
	</tfoot>

	</table>
	</div>

	<?php
}


/**
 * Display trashed rows from the table product
 * @parameter array $data ,
 * @parameter string $orderBy
 * @parameter string $order
 * @parameter array $message
 * @return void
 */
public function trashedList(array $data ,  string $orderBy='', string $order='desc', array $message=null){
    $this->message($message);


	?>
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-sm table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_product" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'trashedlist')?>?orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_user" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'trashedlist')?>?orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_USER?></a></th>
	<th scope="col" id="name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'trashedlist')?>?orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>"><?=T_FIELD_NAME?></a></th>
	<th scope="col" id="status" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'trashedlist')?>?orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'trashedlist')?>?orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'trashedlist')?>?orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?>">
		<td class="text-nowrap"><?php echo $element['id_product']?></td>
		<td ><?php echo htmlspecialchars((string)($this->userList2[$element['id_user']]['email'] ?? ''));?></td>
		<td ><?php echo $element['name']?></td>
		<td ><?=self::statusBadge((string)$element['status'])?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_product']?>" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=undelHtmx&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-sm btn-outline-secondary" title="<?=T_RESTORE?>" aria-label="<?=T_RESTORE?>">
            <?=self::icon('undel', 16)?>
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>

	</table>
	</div>
	<?php
}






}
