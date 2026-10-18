<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\CartItem;

class ViewCartItem extends \Apgenic\Classes\ViewTemplate {
var $productVariantList = array();
var $userList3 = array();
var $productList3 = array();

public string $filtersGet = '';

public $fileters = array();

function __construct($productVariantList, $userList3, $productList3) {
$this->productVariantList = $productVariantList;
$this->userList3 = $userList3;
$this->productList3 = $productList3;
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
        <a href="#" id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=cart_item&task=edit&id_cart_item=<?=$id?>"><?=T_TAB_INFO?></a>
      </li>
      
    </ul>
    

     
<?php
}
/**
 * Edit form for the table cart_item
 * @parameter ModelCartItem $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelCartItem $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_cart_item, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=cartitem&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_ID?>&nbsp;*</label>
		<input required="required" class="form-control" type="number" name="id_cart_item" id="id_cart_item" value="<?=$data->id_cart_item?>"/>
	    </div>
	    </div>
	    

                        <?php 
                        if(!isset($this->filters['id_user'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label"><?=T_FIELD_ID_USER?>&nbsp;*</label>
            <select  class="form-control" name="id_user" id="id_user">
                <option value=""></option>
        <?php
        foreach ($this->userList3 as $key => $val){
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
		<label class="control-label"><?=T_FIELD_COOKIE_ID?></label>
		<input class="form-control" type="text" maxlength="255" name="cookie_id" id="cookie_id" value="<?=$data->cookie_id?>"/>
	    </div>
	    </div>

                        <?php 
                        if(!isset($this->filters['id_product'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label"><?=T_FIELD_ID_PRODUCT?>&nbsp;*</label>
            <select required="required" class="form-control" name="id_product" id="id_product">
                <option value=""></option>
        <?php
        foreach ($this->productList3 as $key => $val){
                ?>
                <option value="<?=$val['id_product']?>" <?php if($data->id_product == $val['id_product']) echo 'selected="selected"';?>><?=$val['name']?></option>
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
                            
                            <input type="hidden" name="id_product" id="id_product" value="<?php if(!isset($data->id_product) || $data->id_product < 1) echo $_GET['filters']['id_product']; else echo $data->id_product?>"/>
                            <?php
                        }
                        ?>                     

        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label"><?=T_FIELD_ID_PRODUCT_VARIANT?></label>
            <select  class="form-control" name="id_product_variant" id="id_product_variant">
                <option value=""></option>
        <?php
        foreach ($this->productVariantList as $key => $val){
                ?>
                <option value="<?=$val['id_product_variant']?>" <?php if($data->id_product_variant == $val['id_product_variant']) echo 'selected="selected"';?>><?=$val['name']?></option>
        <?php 
        }
        ?>
       
            </select>
		</div>
		</div>

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_QUANTITY?>&nbsp;*</label>
		<input required="required" class="form-control" type="number" min="1" name="quantity" id="quantity" value="<?=$data->quantity == '' ? 1 : $data->quantity?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
		<div class="form-group">
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
 * Display rows from the table cart_item
 * @parameter array $data , 
 * @parameter string $orderBy
 * @parameter string $order
 * @parameter array $message
 * @return void
 */
public function viewList(array $data ,  string $orderBy='', string $order='desc', array $message=null){
    $this->message($message);


	?>
	<div class="table-responsive k-table-card">	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_cart_item"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=id_cart_item&order=<?php if($orderBy == "id_cart_item") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_PRODUCT?></a></th>
	<th scope="col" id="id_product_variant"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=id_product_variant&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_PRODUCT_VARIANT?></a></th>
	<th scope="col" id="quantity"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>"><?=T_FIELD_QUANTITY?></a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $element['id_cart_item']?></td>
		<td ><?php echo $this->productList3[$element['id_product']]['name'] ?? '';?></td>
		<td ><?php echo $this->productVariantList[$element['id_product_variant']]['name'] ?? '';?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=view&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>" class="btn k-btn-icon" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
            <?=self::icon('view')?>		    
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="11" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	</div>
	<?php
}






/**
 * Display details of a record from the table cart_item
 *
 * @parameter ModelCartItem $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelCartItem $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_cart_item != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5><?=T_FIELD_ID?></h5></dt><dd class="col-sm-9"><?php echo $data->id_cart_item?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_ID_PRODUCT?></h5></dt><dd class="col-sm-9"><?php echo $this->productList3[$data->id_product]['name'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_ID_PRODUCT_VARIANT?></h5></dt><dd class="col-sm-9"><?php echo $this->productVariantList[$data->id_product_variant]['name'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_QUANTITY?></h5></dt><dd class="col-sm-9"><?php echo $data->quantity?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_CREATED_AT?></h5></dt><dd class="col-sm-9"><?php echo $data->created_at?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_UPDATED_AT?></h5></dt><dd class="col-sm-9"><?php echo $data->updated_at?></dd>
	             </dl></div>
            </div>
	    </div>
	</div>
	<?php
    }
    else{
        $this->message(array('text' => T_NO_DATA, 'type' => 'warning'));
    }



}

        
/**
 * Display rows from the table cart_item
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
    <a href="#" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=<?=$task?>&value=<?php echo $value?>&id_cart_item=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table cart_item with edit and del buttons
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
	<tr>	<th scope="col" id="id_cart_item"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=id_cart_item&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_cart_item") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=id_product&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_PRODUCT?></a></th>
	<th scope="col" id="id_product_variant"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=id_product_variant&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_PRODUCT_VARIANT?></a></th>
	<th scope="col" id="quantity"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=quantity&<?=$this->filtersGet?>&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>"><?=T_FIELD_QUANTITY?></a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th></th>
		<th></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_cart_item']?>">
		<td class="shrink"><?php echo $element['id_cart_item']?></td>
		<td ><?php echo $this->productList3[$element['id_product']]['name'] ?? '';?></td>
		<td >
                                            <a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=editList&id_product_variant=<?php echo $element['id_product_variant']?>">
                                            <?php echo $this->productVariantList[$element['id_product_variant']]['name'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=view&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>" class="btn k-btn-icon" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=edit&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary k-btn-icon" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a href="#" hx-target="#row-<?php echo $element['id_cart_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=cartitem&task=delHtmx&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-outline btn-outline-danger k-btn-icon" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>">            
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
	<td colspan="11" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	</div>
	<?php
}

        
/**
 * Display rows from the table cart_item with edit and del buttons
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
	<tr>	<th scope="col" id="id_cart_item"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=id_cart_item&order=<?php if($orderBy == "id_cart_item") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_product"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID_PRODUCT?></a></th>
	<th scope="col" id="id_product_variant"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=id_product_variant&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID_PRODUCT_VARIANT?></a></th>
	<th scope="col" id="quantity"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_QUANTITY?></a></th>
	<th scope="col" id="created_at"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_cart_item']?>">
		<td class="shrink"><?php echo $element['id_cart_item']?></td>
		<td ><?php echo $this->productList3[$element['id_product']]['name'] ?? '';?></td>
		<td >
                                            <a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=editList&id_product_variant=<?php echo $element['id_product_variant']?>">
                                            <?php echo $this->productVariantList[$element['id_product_variant']]['name'];?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=view&showTabs=0&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>" class="btn k-btn-icon" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childList&showTabs=0&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary k-btn-icon" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a href="#" hx-target="#row-<?php echo $element['id_cart_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=cartitem&task=delHtmx&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-outline btn-outline-danger k-btn-icon" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>"><?=self::icon('trash3')?></a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="11" class="phppistolsTFooter" style="text-align: right;">
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
 * Display trashed rows from the table cart_item
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
	<tr>	<th scope="col" id="id_cart_item"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=id_cart_item&order=<?php if($orderBy == "id_cart_item") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_PRODUCT?></a></th>
	<th scope="col" id="id_product_variant"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=id_product_variant&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_PRODUCT_VARIANT?></a></th>
	<th scope="col" id="quantity"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>"><?=T_FIELD_QUANTITY?></a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_cart_item']?>">
		<td class="shrink"><?php echo $element['id_cart_item']?></td>
		<td ><?php echo $this->productList3[$element['id_product']]['name'] ?? '';?></td>
		<td ><?php echo $this->productVariantList[$element['id_product_variant']]['name'] ?? '';?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a href="#" hx-target="#row-<?php echo $element['id_cart_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=cartitem&task=undelHtmx&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary k-btn-icon" title="<?=T_RESTORE?>" aria-label="<?=T_RESTORE?>">
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
	<td colspan="11" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	</div>
	<?php
}






}
