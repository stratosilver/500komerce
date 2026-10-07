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
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=product&task=edit&id_product=<?=$id?>">Info</a>
      </li>
      
        <li class="nav-item">
            <a id="tab_0" class="form_tabs nav-link <?php if($activeTab == 'productmedia') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=productmedia&task=childlist&filters[id_product]=<?=$id ?>&showTabs=0">Product_media</a>
        </li>
        
        <li class="nav-item">
            <a id="tab_1" class="form_tabs nav-link <?php if($activeTab == 'productcategory') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=productcategory&task=childlist&filters[id_product]=<?=$id ?>&showTabs=0">Product_category</a>
        </li>
        
        <li class="nav-item">
            <a id="tab_2" class="form_tabs nav-link <?php if($activeTab == 'cartitem') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=cartitem&task=childlist&filters[id_product_variant]=<?=$id ?>&showTabs=0">Cart_item</a>
        </li>
        
        <li class="nav-item">
            <a id="tab_3" class="form_tabs nav-link <?php if($activeTab == 'customerorder') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=customerorder&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0">Customer_order</a>
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
            <label class="control-label">Id&nbsp;user:</label>
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
		<label class="control-label">Name&nbsp;*:</label>
		<input required class="form-control" type="text" name="name" id="name" size="10" value="<?=htmlentities((string)(string)$data->name)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Slug:</label>
		<input  class="form-control" type="text" name="slug" id="slug" size="10" value="<?=htmlentities((string)(string)$data->slug)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Summary:</label>
		<input  class="form-control" type="text" name="summary" id="summary" size="10" value="<?=htmlentities((string)(string)$data->summary)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Price&nbsp;(without&nbsp;tax):</label>
		<input  class="form-control" type="number" min="0" step="0.01" name="price" id="price" value="<?=number_format((int)$data->price_amount / 100, 2, '.', '')?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-12 col-md-12"><div class="form-group">
		<label><span>Description:</span></label>
		<textarea  class="form-control" name="description" id="description" rows="4" cols="49"><?=$data->description?></textarea>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
            <label class="control-label">Status:</label>
            <select required="" class="form-control" name="status" id="status">
                <option value=""></option>
            
                <option value="draft" <?php if($data->status == 'draft') echo 'selected="selected"' ?>>draft</option>
                <option value="active" <?php if($data->status == 'active') echo 'selected="selected"' ?>>active</option>
                <option value="archived" <?php if($data->status == 'archived') echo 'selected="selected"' ?>>archived</option>
            </select>
		</div>
	    </div>
		

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Published&nbsp;at:</label>
		<input  class="form-control" type="datetime-local" name="published_at" id="published_at" value="<?=htmlentities((string)$data->published_at)?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
		<div class="form-group">
		<input type="hidden" name="id_product" id="id" value="<?=$data->id_product ?>"/>
		<input type="submit" name="Envoyer" title="Envoyer" value="Save" class="btn btn-outline btn-outline-primary">
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
 * Search bar. The visitors and the customers get a simple text search, the back office keeps the search by field.
 * @return void
 */
public function search(array $fieldsList = [], $task = 'viewlist'):void{
    if(\Apgenic\Classes\Auth::canEdit() || $task != 'viewlist'){
        parent::search($fieldsList, $task);
        return;
    }
    ?>
    <form class="form-inline" method="get" action="<?=BASE_URL?>/index.php" role="search">
        <input type="hidden" name="component" value="product">
        <input type="hidden" name="task" value="viewlist">
        <label class="sr-only" for="text_search">Search a product</label>
        <div class="input-group w-100">
            <input class="form-control" type="search" name="text_search" id="text_search" value="<?=htmlspecialchars((string)($_GET['text_search'] ?? ''))?>" placeholder="Search a product">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </div>
    </form>
    <?php
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
        'Newest' => array('id_product', 'desc'),
        'Price: low to high' => array('price_amount', 'asc'),
        'Price: high to low' => array('price_amount', 'desc'),
        'Name' => array('name', 'asc'),
    );
    $search = trim((string)($_GET['text_search'] ?? ''));
    $sortUrl = BASE_URL.'/index.php?component=product&task=viewlist&'.$this->filtersGet.($search != '' ? 'text_search='.urlencode($search).'&' : '');
	?>
	<div class="d-flex flex-wrap justify-content-between align-items-center mt-4 mb-3">
	    <span class="text-muted mb-2"><?=count($data)?> product<?=count($data) > 1 ? 's' : ''?> on this page</span>
	    <div class="btn-group btn-group-sm mb-2" role="group" aria-label="Sort the products">
	        <?php foreach($sorts as $label => $sort){
	            $active = $orderBy == $sort[0] && $currentOrder == $sort[1]; ?>
	        <a class="btn btn-outline-secondary <?php if($active) echo 'active';?>" href="<?=htmlspecialchars($sortUrl.'orderBy='.$sort[0].'&order='.$sort[1])?>"><?=$label?></a>
	        <?php } ?>
	    </div>
	</div>

	<?php if(!count($data)){ ?>
	<div class="alert alert-info">No product found</div>
	<?php } ?>

	<div class="row">
	<?php
	foreach ($data as $element){
	    $id = (int)$element['id_product'];
	    $url = BASE_URL.'/index.php?component=product&task=view&id_product='.$id;
	    $image = $this->imageUrl($id, 0, 'medium');
	    $price = (int)$element['price_amount'];
		?>
		<div class="col-sm-6 col-lg-4 col-xl-3 mb-4">
		    <div class="card h-100 shadow-sm">
		        <a class="embed-responsive embed-responsive-1by1 bg-light" href="<?=$url?>">
		            <?php if($image != ''){ ?>
		            <img class="embed-responsive-item" style="object-fit: cover" src="<?=htmlspecialchars($image)?>" alt="<?=$element['name']?>" loading="lazy">
		            <?php } ?>
		        </a>
		        <div class="card-body d-flex flex-column pb-2">
		            <h2 class="card-title h5 mt-0 mb-1"><a class="text-dark" href="<?=$url?>"><?=$element['name']?></a></h2>
		            <?php if($canEdit && $element['status'] != 'active'){ ?>
		            <div><span class="badge badge-secondary"><?=$element['status']?></span></div>
		            <?php } ?>
		            <p class="card-text text-muted small flex-grow-1 mb-2"><?=$element['summary']?></p>
		            <div class="h5 mb-0"><?=self::money(self::priceWithTax($price))?></div>
		            <small class="text-muted"><?=self::money($price)?> excl. tax</small>
		        </div>
		        <div class="card-footer bg-white border-0 pt-0">
		            <?php if($element['status'] == 'active'){
		                self::addToCart($id, false, true);
		            } else { ?>
		            <button type="button" class="btn btn-outline-secondary btn-block" disabled>Not on sale</button>
		            <?php } ?>
		        </div>
		    </div>
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

	    // All the images of the product, or one illustration
	    $gallery = array();
	    for($i = 0; $i < max(1, count($this->images[$id] ?? array())); $i++){
	        $large = $this->imageUrl($id, $i, 'medium');
	        if($large != ''){
	            $gallery[] = array('large' => $large, 'thumb' => $this->imageUrl($id, $i, 'thumb'));
	        }
	    }
	?>
	
    <p class="mt-3"><a href="<?=BASE_URL?>/index.php?component=product&task=viewlist">&laquo; All the products</a></p>

    <div class="row">
        <div class="col-md-6 col-lg-5 mb-4">
            <div class="embed-responsive embed-responsive-1by1 bg-light border rounded">
                <?php if(count($gallery)){ ?>
                <img id="product_image" class="embed-responsive-item" style="object-fit: contain" src="<?=htmlspecialchars($gallery[0]['large'])?>" alt="<?=$data->name?>">
                <?php } ?>
            </div>
            <?php if(count($gallery) > 1){ ?>
            <div class="d-flex flex-wrap mt-2">
                <?php foreach($gallery as $n => $picture){ ?>
                <a class="mr-2 mb-2" href="<?=htmlspecialchars($picture['large'])?>" onclick="document.getElementById('product_image').src = this.href; return false;">
                    <img class="img-thumbnail" style="width: 72px; height: 72px; object-fit: cover" src="<?=htmlspecialchars($picture['thumb'])?>" alt="<?=$data->name?> - image <?=$n + 1?>" loading="lazy">
                </a>
                <?php } ?>
            </div>
            <?php } ?>
        </div>

        <div class="col-md-6 col-lg-7">
            <h2 class="mt-0 mb-2"><?=$data->name?>
                <?php if($canEdit && !$onSale){ ?><span class="badge badge-secondary align-middle"><?=$data->deleted_at !== null ? 'deleted' : $data->status?></span><?php } ?>
            </h2>
            <?php if((string)$data->summary != ''){ ?>
            <p class="lead"><?=$data->summary?></p>
            <?php } ?>

            <div class="my-4">
                <span class="h2"><?=self::money(self::priceWithTax($price))?></span> <span class="text-muted">incl. tax</span>
                <div class="text-muted small"><?=self::money($price)?> excl. tax</div>
            </div>

            <?php if($onSale){
                self::addToCart($id, true);
            } else { ?>
            <div class="alert alert-secondary d-inline-block mt-0">This product is not on sale</div>
            <?php } ?>

            <?php if((string)$data->description != ''){ ?>
            <hr class="my-4">
            <h3 class="h5">Description</h3>
            <div><?=nl2br((string)$data->description)?></div>
            <?php } ?>

            <?php if($canEdit){ // Back office ?>
            <hr class="my-4">
            <dl class="row small text-muted mb-2">
                <dt class="col-sm-4">Id&nbsp;product</dt><dd class="col-sm-8"><?php echo $data->id_product?></dd>
                <dt class="col-sm-4">Id&nbsp;user</dt><dd class="col-sm-8"><?php echo $this->userList2[$data->id_user]['email'] ?? '';?></dd>
                <dt class="col-sm-4">Slug</dt><dd class="col-sm-8"><?php echo $data->slug?></dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><?php echo $data->status?></dd>
                <dt class="col-sm-4">Published&nbsp;at</dt><dd class="col-sm-8"><?php echo $data->published_at?></dd>
                <dt class="col-sm-4">Created&nbsp;at</dt><dd class="col-sm-8"><?php echo $data->created_at?></dd>
                <dt class="col-sm-4">Updated&nbsp;at</dt><dd class="col-sm-8"><?php echo $data->updated_at?></dd>
                <dt class="col-sm-4">Deleted&nbsp;at</dt><dd class="col-sm-8"><?php echo $data->deleted_at?></dd>
            </dl>
            <a class="btn btn-outline btn-outline-primary btn-sm" href="<?=BASE_URL?>/index.php?component=product&task=edit&id_product=<?=$id?>"><?=self::icon('pencil', 16)?> Edit</a>
            <?php } ?>
        </div>
    </div>
	<?php
    }
    else{
        $this->message(array('text' => 'No data', 'type' => 'warning'));
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=<?=$task?>&value=<?php echo $value?>&id_product=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
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
	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=id_product&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=id_user&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="name"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=name&<?=$this->filtersGet?>&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>">Name</a></th>
	<th scope="col" id="price_amount"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=price_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "price_amount") echo $order; else echo 'asc';?>">Price</a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=status&<?=$this->filtersGet?>&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>">Status</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=product&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist" class="btn btn-outline btn-outline-secondary">
check2   
            </a></th>
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
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo $this->userList2[$element['id_user']]['email'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['name']?></td>
		<td class="text-right text-nowrap"><?=self::money((int)$element['price_amount'])?></td>
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=view&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>" class="btn">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=edit&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_product']?>" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=logicaldeleteHtmx&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">
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
	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_product"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_user"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;user</a></th>
	<th scope="col" id="name"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Name</a></th>
	<th scope="col" id="status"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Status</a></th>
	<th scope="col" id="created_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Updated&nbsp;at</a></th>

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
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo $this->userList2[$element['id_user']]['email'];?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['name']?></td>
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=view&showTabs=0&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>" class="btn">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=product&task=childList&showTabs=0&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>" class="btn  btn-outline btn-outline-primary">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_product']?>" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=delHtmx&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="14" class="phppistolsTFooter" style="text-align: right;">
	    <a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?= BASE_URL ?>/htmx.php?component=<?=COMPONENT?>&task=edit&showTabs=0&<?=$parentPK?>=<?=$parentPKValue?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-success" style="margin-top: 10px;">
        <?=self::icon('plus-lg');?>
    </a>
    </td>
	</tr>
	</tfoot>

	</table>
	
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
	<table class="table table-bordered table-sm">
	<thead>
	<tr>	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="name"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>">Name</a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>">Status</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=product&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

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
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_product']?>" hx-post="<?=BASE_URL?>/htmx.php?component=product&task=undelHtmx&id_product=<?php echo $element['id_product']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
	<?php
}






}
