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

    <ul id="tabs_list" class="nav nav-tabs">
      <li class="nav-item">
        <a href="#" id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=product&task=edit&id_product=<?=$id?>"><?=T_TAB_INFO?></a>
      </li>
      
        <li class="nav-item">
            <a href="#" id="tab_0" class="form_tabs nav-link <?php if($activeTab == 'productmedia') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=productmedia&task=childlist&filters[id_product]=<?=$id ?>&showTabs=0"><?=T_MENU_PRODUCTMEDIA?></a>
        </li>
        
        <li class="nav-item">
            <a href="#" id="tab_1" class="form_tabs nav-link <?php if($activeTab == 'productcategory') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=productcategory&task=childlist&filters[id_product]=<?=$id ?>&showTabs=0"><?=T_MENU_PRODUCTCATEGORY?></a>
        </li>
        
        <li class="nav-item">
            <a href="#" id="tab_2" class="form_tabs nav-link <?php if($activeTab == 'cartitem') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=cartitem&task=childlist&filters[id_product_variant]=<?=$id ?>&showTabs=0"><?=T_MENU_CARTITEM?></a>
        </li>
        
        <li class="nav-item">
            <a href="#" id="tab_3" class="form_tabs nav-link <?php if($activeTab == 'customerorder') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=customerorder&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0"><?=T_MENU_CUSTOMERORDER?></a>
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
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

                        <?php 
                        if(!isset($this->filters['id_user'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label"><?=T_FIELD_ID_USER?></label>
            <select  class="form-control" name="id_user" id="id_user">
                <option value=""></option>
        <?php
        foreach ($this->userList2 as $key => $val){
                ?>
                <option value="<?=$val['id_user']?>" <?php if($data->id_user == $val['id_user']) echo 'selected="selected"';?>><?=$val['email']?></option>
        <?php 
        }
        ?>
       
            </select>
		</div>
		</div>
		
                        <?php
                        }
                        else{
                            ?>
                            
                            <input type="hidden" name="id_user" id="id_user" value="<?php if(!isset($data->id_user) || $data->id_user < 1) echo $_GET['filters']['id_user']; else echo $data->id_user?>"/>
                            <?php
                        }
                        ?>                     
                        

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_NAME?>&nbsp;*</label>
		<input required class="form-control" type="text" name="name" id="name" size="10" value="<?=htmlentities((string)(string)$data->name)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_SLUG?></label>
		<input  class="form-control" type="text" name="slug" id="slug" size="10" value="<?=htmlentities((string)(string)$data->slug)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_SUMMARY?></label>
		<input  class="form-control" type="text" name="summary" id="summary" size="10" value="<?=htmlentities((string)(string)$data->summary)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_PRICE_WITHOUT_TAX?></label>
		<input  class="form-control" type="number" min="0" step="0.01" name="price" id="price" value="<?=number_format((int)$data->price_amount / 100, 2, '.', '')?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-12 col-md-12"><div class="form-group">
		<label class="control-label"><?=T_FIELD_DESCRIPTION?></label>
		<textarea  class="form-control" name="description" id="description" rows="4" cols="49"><?=$data->description?></textarea>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
            <label class="control-label"><?=T_FIELD_STATUS?></label>
            <select required="" class="form-control" name="status" id="status">
                <option value=""></option>
            
                <option value="draft" <?php if($data->status == 'draft') echo 'selected="selected"' ?>><?=T_VALUE_DRAFT?></option>
                <option value="active" <?php if($data->status == 'active') echo 'selected="selected"' ?>><?=T_VALUE_ACTIVE?></option>
                <option value="archived" <?php if($data->status == 'archived') echo 'selected="selected"' ?>><?=T_VALUE_ARCHIVED?></option>
            </select>
		</div>
	    </div>
		

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_PUBLISHED_AT?></label>
		<input  class="form-control" type="datetime-local" name="published_at" id="published_at" value="<?=htmlentities((string)$data->published_at)?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
		<div class="form-group k-form-actions">
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

    $catalogUrl = BASE_URL.'/index.php?component=product&task=viewlist';
    $search = trim((string)($_GET['text_search'] ?? ''));
    echo '<main id="main" class="k-shop-main"><div class="container k-container">';

    if($task == 'viewlist'){
        ?>
        <div class="k-catalog-hero">
            <div>
                <div class="k-eyebrow"><?=T_SHOP?></div>
                <?php if($search != ''){ ?>
                <h1 class="k-page-title"><?=sprintf(T_RESULTS_FOR, htmlspecialchars($search))?></h1>
                <a class="k-link-muted" href="<?=$catalogUrl?>"><?=self::icon('x-lg', 12)?> <?=T_CLEAR_SEARCH?></a>
                <?php } else { ?>
                <h1 class="k-page-title"><?=T_ALL_PRODUCTS?></h1>
                <?php } ?>
            </div>
        </div>
        <?php
    }
    else{
        // The name of the product is displayed by view()
        ?>
        <nav aria-label="<?=T_BREADCRUMB?>">
            <ol class="breadcrumb k-breadcrumb">
                <li class="breadcrumb-item"><a href="<?=BASE_URL?>/"><?=T_HOME?></a></li>
                <li class="breadcrumb-item"><a href="<?=$catalogUrl?>"><?=T_SHOP?></a></li>
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
 * @param int $index which image of the product, 0 = the first one
 * @param string $size one of the sizes of \Apgenic\Media\MediaImage::SIZES
 * @return string '' when there is nothing to display
 */
public function imageUrl(int $idProduct, int $index = 0, string $size = 'medium'):string{
    $filename = $this->images[$idProduct][$index] ?? '';

    if($filename != '' && preg_match('/^[A-Za-z0-9._-]+$/', $filename)){
        $dir = \Apgenic\Media\MediaImage::dir().DIRECTORY_SEPARATOR;
        // The resized copy when it exists, otherwise the original file
        if(is_file($dir.\Apgenic\Media\MediaImage::variant($filename, $size))){
            return \Apgenic\Media\MediaImage::url($filename, $size);
        }
        if(is_file($dir.$filename)){
            return \Apgenic\Media\MediaImage::url($filename);
        }
    }

    if($index == 0 && defined('PRODUCT_ILLUSTRATION_URL') && PRODUCT_ILLUSTRATION_URL != ''){
        $pixels = \Apgenic\Media\MediaImage::SIZES[$size][0] ?? 800;
        return sprintf(PRODUCT_ILLUSTRATION_URL, $idProduct, $pixels, $pixels);
    }
    return '';
}


/**
 * Price with the tax, the one paid by the customer (the prices are stored without tax)
 * @return int cents
 */
public static function priceWithTax(int $priceAmount):int{
    return $priceAmount + (int)round($priceAmount * \Apgenic\Shop\ModelCart::taxRate() / 100);
}


/**
 * Status of a product as a colored badge (back office)
 * @return string
 */
public static function statusBadge(string $status, bool $deleted = false):string{
    if($deleted){
        return '<span class="badge badge-danger">'.T_VALUE_DELETED.'</span>';
    }
    $types = array('active' => 'success', 'draft' => 'warning', 'archived' => 'secondary');
    return '<span class="badge badge-'.($types[$status] ?? 'secondary').'">'.self::value($status).'</span>';
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
    $sortUrl = BASE_URL.'/index.php?component=product&task=viewlist&'.$this->filtersGet.($search != '' ? 'text_search='.urlencode($search).'&' : '');
	?>
	<div class="k-catalog-toolbar">
	    <span class="k-result-count"><?=sprintf(count($data) > 1 ? T_PRODUCTS_ON_PAGE : T_PRODUCTS_ON_PAGE_ONE, count($data))?></span>
	    <nav class="k-chips" aria-label="<?=T_SORT_PRODUCTS?>">
	        <span class="k-chips-label"><?=T_SORT_BY?></span>
	        <?php foreach($sorts as $label => $sort){
	            $active = $orderBy == $sort[0] && $currentOrder == $sort[1]; ?>
	        <a class="k-chip <?php if($active) echo 'active';?>" href="<?=htmlspecialchars($sortUrl.'orderBy='.$sort[0].'&order='.$sort[1])?>" <?php if($active) echo 'aria-current="true"';?>><?=$label?></a>
	        <?php } ?>
	    </nav>
	</div>

	<?php if(!count($data)){ ?>
	<div class="k-empty">
	    <div class="k-empty-icon"><?=self::icon('search', 28)?></div>
	    <h2 class="k-empty-title"><?=T_NO_PRODUCT?></h2>
	    <?php if($search != ''){ ?>
	    <p><?=T_NO_PRODUCT_HINT?></p>
	    <a class="btn btn-primary" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=T_SEE_ALL_PRODUCTS?></a>
	    <?php } ?>
	</div>
	<?php } ?>

	<div class="row k-product-grid">
	<?php
	foreach ($data as $element){
	    $id = (int)$element['id_product'];
	    $url = BASE_URL.'/index.php?component=product&task=view&id_product='.$id;
	    $image = $this->imageUrl($id, 0, 'medium');
	    $price = (int)$element['price_amount'];
	    $onSale = $element['status'] == 'active';
		?>
		<div class="col-6 col-md-4 col-xl-3">
		    <article class="k-product-card">
		        <a class="k-product-media" href="<?=$url?>" tabindex="-1" aria-hidden="true">
		            <?php if($image != ''){ ?>
		            <img src="<?=htmlspecialchars($image)?>" alt="" loading="lazy">
		            <?php } else { ?>
		            <span class="k-product-placeholder"><?=self::icon('image', 32)?></span>
		            <?php } ?>
		            <?php if($canEdit && !$onSale){ ?>
		            <span class="k-product-flag"><?=self::statusBadge((string)$element['status'])?></span>
		            <?php } ?>
		        </a>
		        <div class="k-product-body">
		            <h2 class="k-product-name"><a href="<?=$url?>"><?=$element['name']?></a></h2>
		            <?php if((string)$element['summary'] != ''){ ?>
		            <p class="k-product-summary"><?=$element['summary']?></p>
		            <?php } ?>
		            <div class="k-price">
		                <span class="k-price-main"><?=self::money(self::priceWithTax($price))?></span>
		                <span class="k-price-sub"><?=self::money($price)?> <?=T_EXCL_TAX?></span>
		            </div>
		        </div>
		        <div class="k-product-footer">
		            <?php if($onSale){
		                self::addToCart($id, false, true);
		            } else { ?>
		            <button type="button" class="btn btn-light btn-block" disabled><?=T_NOT_ON_SALE?></button>
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

    <div class="row k-product-page">
        <div class="col-lg-7 mb-4">
            <div class="k-gallery">
                <div class="k-gallery-main">
                    <?php if(count($gallery)){ ?>
                    <img id="product_image" src="<?=htmlspecialchars($gallery[0]['large'])?>" alt="<?=$data->name?>">
                    <?php } else { ?>
                    <span class="k-product-placeholder"><?=self::icon('image', 48)?></span>
                    <?php } ?>
                </div>
                <?php if(count($gallery) > 1){ ?>
                <div class="k-gallery-thumbs">
                    <?php foreach($gallery as $n => $picture){ ?>
                    <a class="k-gallery-thumb <?php if($n == 0) echo 'active';?>" href="<?=htmlspecialchars($picture['large'])?>" onclick="document.getElementById('product_image').src = this.href; this.parentNode.querySelectorAll('a').forEach(a => a.classList.remove('active')); this.classList.add('active'); return false;">
                        <img src="<?=htmlspecialchars($picture['thumb'])?>" alt="<?=sprintf(T_IMAGE_N, $data->name, $n + 1)?>" loading="lazy">
                    </a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="k-buybox">
                <?php if($canEdit && !$onSale){ ?>
                <div class="mb-2"><?=self::statusBadge((string)$data->status, $data->deleted_at !== null)?></div>
                <?php } ?>
                <h1 class="k-product-title"><?=$data->name?></h1>
                <?php if((string)$data->summary != ''){ ?>
                <p class="k-product-lead"><?=$data->summary?></p>
                <?php } ?>

                <div class="k-price k-price-lg">
                    <span class="k-price-main"><?=self::money(self::priceWithTax($price))?></span>
                    <span class="k-price-sub"><?=self::money($price)?> <?=T_EXCL_TAX?> &middot; <?=sprintf(T_VAT_INCLUDED, $taxRate)?></span>
                </div>

                <?php if($onSale){
                    self::addToCart($id, true);
                } else { ?>
                <div class="alert alert-secondary k-alert"><?=self::icon('info-circle', 20)?><div><?=T_PRODUCT_NOT_ON_SALE?></div></div>
                <?php } ?>

                <ul class="k-feature-list">
                    <li><?=self::icon('bag-check', 18)?> <?=T_FEATURE_GUEST?></li>
                    <li><?=self::icon('truck', 18)?> <?=T_FEATURE_DELIVERY?></li>
                </ul>
            </div>
        </div>
    </div>

    <?php if((string)$data->description != ''){ ?>
    <section class="k-section">
        <h2 class="k-section-title"><?=T_FIELD_DESCRIPTION?></h2>
        <div class="k-prose"><?=nl2br((string)$data->description)?></div>
    </section>
    <?php } ?>

    <?php if($canEdit){ // Back office ?>
    <section class="k-section k-staff-panel">
        <div class="k-staff-panel-header">
            <h2 class="k-section-title"><?=self::icon('speedometer2', 18)?> <?=T_BACK_OFFICE?></h2>
            <a class="btn btn-primary btn-sm" href="<?=BASE_URL?>/index.php?component=product&task=edit&id_product=<?=$id?>"><?=self::icon('pencil', 14)?> <?=T_EDIT?></a>
        </div>
        <dl class="k-meta-list">
            <div><dt><?=T_FIELD_ID?></dt><dd><?php echo $data->id_product?></dd></div>
            <div><dt><?=T_FIELD_ID_USER?></dt><dd><?php echo $this->userList2[$data->id_user]['email'] ?? '';?></dd></div>
            <div><dt><?=T_FIELD_SLUG?></dt><dd><?php echo $data->slug?></dd></div>
            <div><dt><?=T_FIELD_STATUS?></dt><dd><?=self::statusBadge((string)$data->status, $data->deleted_at !== null)?></dd></div>
            <div><dt><?=T_FIELD_PUBLISHED_AT?></dt><dd><?php echo $data->published_at?></dd></div>
            <div><dt><?=T_FIELD_CREATED_AT?></dt><dd><?php echo $data->created_at?></dd></div>
            <div><dt><?=T_FIELD_UPDATED_AT?></dt><dd><?php echo $data->updated_at?></dd></div>
            <div><dt><?=T_FIELD_DELETED_AT?></dt><dd><?php echo $data->deleted_at?></dd></div>
        </dl>
    </section>
    <?php } ?>
	<?php
    }
    else{
        ?>
        <div class="k-empty">
            <div class="k-empty-icon"><?=self::icon('emoji-frown', 28)?></div>
            <h2 class="k-empty-title"><?=T_PRODUCT_UNAVAILABLE?></h2>
            <a class="btn btn-primary" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=T_SEE_ALL_PRODUCTS?></a>
        </div>
        <?php
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
    $buttonIcon[0] = self::icon('x-lg');
    $buttonIcon[1] = self::icon('check2');
   
    ?>
    <a href="#" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=<?=$task?>&value=<?php echo $value?>&id_product=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
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
	<div class="table-responsive k-table-card">	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=id_product&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=id_user&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_USER?></a></th>
	<th scope="col" id="name"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=name&<?=$this->filtersGet?>&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>"><?=T_FIELD_NAME?></a></th>
	<th scope="col" id="price_amount"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=price_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "price_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_PRICE_AMOUNT?></a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=status&<?=$this->filtersGet?>&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th></th>
		<th></th>
		<th><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist" class="btn btn-outline btn-outline-secondary k-btn-icon" title="<?=T_TRASH?>" aria-label="<?=T_TRASH?>"><?=self::icon('trash3')?></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?>">
		<td class="shrink"><?php echo $element['id_product']?></td>
		<td >
                                            <a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo $this->userList2[$element['id_user']]['email'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['name']?></td>
		<td class="text-right text-nowrap"><?=self::money((int)$element['price_amount'])?></td>
		<td ><?=self::statusBadge((string)$element['status'])?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=view&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>" class="btn k-btn-icon" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=edit&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary k-btn-icon" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a href="#" hx-target="#row-<?php echo $element['id_product']?>" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=logicaldeleteHtmx&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-outline btn-outline-danger k-btn-icon" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>">
            <?=self::icon('x-lg');?>
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="14" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

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
    $parentPKValue = $filters[$parentPK];


	?>
	<div class="table-responsive k-table-card">	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_product"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_user"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID_USER?></a></th>
	<th scope="col" id="name"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_NAME?></a></th>
	<th scope="col" id="status"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="created_at"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?>">
		<td class="shrink"><?php echo $element['id_product']?></td>
		<td >
                                            <a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo $this->userList2[$element['id_user']]['email'];?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['name']?></td>
		<td ><?=self::statusBadge((string)$element['status'])?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=view&showTabs=0&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>" class="btn k-btn-icon" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childList&showTabs=0&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary k-btn-icon" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a href="#" hx-target="#row-<?php echo $element['id_product']?>" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=delHtmx&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-outline btn-outline-danger k-btn-icon" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>"><?=self::icon('trash3')?></a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="14" class="phppistolsTFooter" style="text-align: right;">
	    <a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?= BASE_URL ?>/htmx.php?component=<?=COMPONENT?>&task=edit&showTabs=0&<?=$parentPK?>=<?=$parentPKValue?>&<?=$this->filtersGet?>" class="btn btn-primary btn-sm">
        <?=self::icon('plus-lg');?> <?=T_ADD?>
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
	<div class="table-responsive k-table-card">	<table class="table table-bordered table-sm">
	<thead>
	<tr>	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_USER?></a></th>
	<th scope="col" id="name"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>"><?=T_FIELD_NAME?></a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?>">
		<td class="shrink"><?php echo $element['id_product']?></td>
		<td ><?php echo $this->userList2[$element['id_user']]['email'] ?? '';?></td>
		<td ><?php echo $element['name']?></td>
		<td ><?=self::statusBadge((string)$element['status'])?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a href="#" hx-target="#row-<?php echo $element['id_product']?>" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=undelHtmx&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary k-btn-icon" title="<?=T_RESTORE?>" aria-label="<?=T_RESTORE?>">
            <?=self::icon('undel')?>	    
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="14" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	</div>
	<?php
}






}
