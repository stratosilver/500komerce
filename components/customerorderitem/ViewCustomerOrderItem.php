<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\CustomerOrderItem;

class ViewCustomerOrderItem extends \Apgenic\Classes\ViewTemplate {
var $customerOrderList1 = array();
var $productVariantList1 = array();

public string $filtersGet = '';

public $fileters = array();

function __construct($customerOrderList1, $productVariantList1) {
$this->customerOrderList1 = $customerOrderList1;
$this->productVariantList1 = $productVariantList1;
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
        <a href="#" id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=customer_order_item&task=edit&id_customer_order_item=<?=$id?>"><?=T_TAB_INFO?></a>
      </li>

    </ul>



<?php
}
/**
 * Edit form for the table customer_order_item
 * @parameter ModelCustomerOrderItem $data
 * @parameter int $showTabs
 * @parameter array $message
 * @return void
 */
public function edit(ModelCustomerOrderItem $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_customer_order_item, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>

		<form class="card card-body shadow-sm p-lg-4 mb-4" method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">


                        <?php
                        if(!isset($this->filters['id_customer_order'])){ ?>

        <div class="col-lg-4 col-md-6">
        <div class="mb-3">
            <label class="form-label" for="id_customer_order"><?=T_FIELD_ID_CUSTOMER_ORDER?>&nbsp;*</label>
            <select  class="form-select" name="id_customer_order" id="id_customer_order">
                <option value=""></option>
        <?php
        foreach (self::notTrashed($this->customerOrderList1, $data->id_customer_order) as $key => $val){
                ?>
                <option value="<?=$val['id_customer_order']?>" <?php if($data->id_customer_order == $val['id_customer_order']) echo 'selected="selected"';?>><?=$val['id_user']?></option>
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

                            <input type="hidden" name="id_customer_order" id="id_customer_order" value="<?php if(!isset($data->id_customer_order) || $data->id_customer_order < 1) echo $_GET['filters']['id_customer_order']; else echo $data->id_customer_order?>"/>
                            <?php
                        }
                        ?>


                        <?php
                        if(!isset($this->filters['id_product_variant'])){ ?>

        <div class="col-lg-4 col-md-6">
        <div class="mb-3">
            <label class="form-label" for="id_product_variant"><?=T_FIELD_ID_PRODUCT_VARIANT?></label>
            <select  class="form-select" name="id_product_variant" id="id_product_variant">
                <option value=""></option>
        <?php
        foreach (self::notTrashed($this->productVariantList1, $data->id_product_variant) as $key => $val){
                ?>
                <option value="<?=$val['id_product_variant']?>" <?php if($data->id_product_variant == $val['id_product_variant']) echo 'selected="selected"';?>><?=$val['name']?></option>
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

                            <input type="hidden" name="id_product_variant" id="id_product_variant" value="<?php if(!isset($data->id_product_variant) || $data->id_product_variant < 1) echo $_GET['filters']['id_product_variant']; else echo $data->id_product_variant?>"/>
                            <?php
                        }
                        ?>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="product_name"><?=T_FIELD_PRODUCT_NAME?>&nbsp;*</label>
		<input required class="form-control" type="text" name="product_name" id="product_name" size="10" value="<?=htmlentities((string)(string)$data->product_name)?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="sku"><?=T_FIELD_SKU?>&nbsp;*</label>
		<input required class="form-control" type="text" name="sku" id="sku" size="10" value="<?=htmlentities((string)(string)$data->sku)?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="quantity"><?=T_FIELD_QUANTITY?>&nbsp;*</label>
		<input required="required" class="form-control" type="number" name="quantity" id="quantity" value="<?=$data->quantity?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="unit_price_amount"><?=T_FIELD_UNIT_PRICE_AMOUNT?>&nbsp;*</label>
		<input required="required" class="form-control" type="number" name="unit_price_amount" id="unit_price_amount" value="<?=$data->unit_price_amount?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="discount_amount"><?=T_FIELD_DISCOUNT_AMOUNT?></label>
		<input required="" class="form-control" type="number" name="discount_amount" id="discount_amount" value="<?=$data->discount_amount?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="tax_amount"><?=T_FIELD_TAX_AMOUNT?></label>
		<input required="" class="form-control" type="number" name="tax_amount" id="tax_amount" value="<?=$data->tax_amount?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="line_total_amount"><?=T_FIELD_LINE_TOTAL_AMOUNT?>&nbsp;*</label>
		<input required="required" class="form-control" type="number" name="line_total_amount" id="line_total_amount" value="<?=$data->line_total_amount?>"/>
	    </div>
	    </div>

		<div class="col-12">

		<div class="border-top pt-3">
		<input type="hidden" name="id_customer_order_item" id="id" value="<?=$data->id_customer_order_item ?>"/>
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
 * Display rows from the table customer_order_item
 * @parameter array $data ,
 * @parameter string $orderBy
 * @parameter string $order
 * @parameter array $message
 * @return void
 */
public function viewList(array $data ,  string $orderBy='', string $order='desc', array $message=null){
    $this->message($message);


	?>
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_customer_order_item" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'viewlist')?>?orderBy=id_customer_order_item&order=<?php if($orderBy == "id_customer_order_item") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_customer_order" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'viewlist')?>?orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_CUSTOMER_ORDER?></a></th>
	<th scope="col" id="product_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'viewlist')?>?orderBy=product_name&order=<?php if($orderBy == "product_name") echo $order; else echo 'asc';?>"><?=T_FIELD_PRODUCT_NAME?></a></th>
	<th scope="col" id="quantity" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'viewlist')?>?orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>"><?=T_FIELD_QUANTITY?></a></th>
	<th scope="col" id="unit_price_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'viewlist')?>?orderBy=unit_price_amount&order=<?php if($orderBy == "unit_price_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_UNIT_PRICE_AMOUNT?></a></th>
	<th scope="col" id="discount_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'viewlist')?>?orderBy=discount_amount&order=<?php if($orderBy == "discount_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_DISCOUNT_AMOUNT?></a></th>
	<th scope="col" id="tax_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'viewlist')?>?orderBy=tax_amount&order=<?php if($orderBy == "tax_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_TAX_AMOUNT?></a></th>
	<th scope="col" id="line_total_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'viewlist')?>?orderBy=line_total_amount&order=<?php if($orderBy == "line_total_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_LINE_TOTAL_AMOUNT?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'viewlist')?>?orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="text-nowrap"><?php echo $element['id_customer_order_item']?></td>
		<td ><?php echo $this->customerOrderList1[$element['id_customer_order']]['id_user'] ?? '';?></td>
		<td ><?php echo $element['product_name']?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['unit_price_amount']?></td>
		<td ><?php echo $element['discount_amount']?></td>
		<td ><?php echo $element['tax_amount']?></td>
		<td ><?php echo $element['line_total_amount']?></td>
		<td ><?php echo $element['created_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=view&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-secondary" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
            <?=self::icon('view', 16)?>
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
 * Display details of a record from the table customer_order_item
 *
 * @parameter ModelCustomerOrderItem $data
 * @parameter array $message
 * @return void
 */
public function view(ModelCustomerOrderItem $data , array $message=null){
        $this->message($message);


	$i=1;

	if ($data->id_customer_order_item != null){
	?>

    <div class="card shadow-sm">
        <div class="card-body p-lg-4">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row mb-0">

		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID?></dt><dd class="col-sm-9"><?php echo $data->id_customer_order_item?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID_CUSTOMER_ORDER?></dt><dd class="col-sm-9"><?php echo $this->customerOrderList1[$data->id_customer_order]['id_user'] ?? '';?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID_PRODUCT_VARIANT?></dt><dd class="col-sm-9"><?php echo $this->productVariantList1[$data->id_product_variant]['name'] ?? '';?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_PRODUCT_NAME?></dt><dd class="col-sm-9"><?php echo $data->product_name?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_SKU?></dt><dd class="col-sm-9"><?php echo $data->sku?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_QUANTITY?></dt><dd class="col-sm-9"><?php echo $data->quantity?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_UNIT_PRICE_AMOUNT?></dt><dd class="col-sm-9"><?php echo $data->unit_price_amount?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_DISCOUNT_AMOUNT?></dt><dd class="col-sm-9"><?php echo $data->discount_amount?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_TAX_AMOUNT?></dt><dd class="col-sm-9"><?php echo $data->tax_amount?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_LINE_TOTAL_AMOUNT?></dt><dd class="col-sm-9"><?php echo $data->line_total_amount?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_CREATED_AT?></dt><dd class="col-sm-9"><?php echo $data->created_at?></dd>
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
 * Display rows from the table customer_order_item
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
    <a href="#" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=<?=$task?>&value=<?php echo $value?>&id_customer_order_item=<?php echo $pkVal;?>" class="btn btn-sm btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>
    <?php
}

/**
 * Display rows from the table customer_order_item with edit and del buttons
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
	<tr>	<th scope="col" id="id_customer_order_item" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'editlist')?>?orderBy=id_customer_order_item&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_customer_order_item") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_customer_order" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'editlist')?>?orderBy=id_customer_order&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_CUSTOMER_ORDER?></a></th>
	<th scope="col" id="product_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'editlist')?>?orderBy=product_name&<?=$this->filtersGet?>&order=<?php if($orderBy == "product_name") echo $order; else echo 'asc';?>"><?=T_FIELD_PRODUCT_NAME?></a></th>
	<th scope="col" id="quantity" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'editlist')?>?orderBy=quantity&<?=$this->filtersGet?>&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>"><?=T_FIELD_QUANTITY?></a></th>
	<th scope="col" id="unit_price_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'editlist')?>?orderBy=unit_price_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "unit_price_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_UNIT_PRICE_AMOUNT?></a></th>
	<th scope="col" id="discount_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'editlist')?>?orderBy=discount_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "discount_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_DISCOUNT_AMOUNT?></a></th>
	<th scope="col" id="tax_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'editlist')?>?orderBy=tax_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "tax_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_TAX_AMOUNT?></a></th>
	<th scope="col" id="line_total_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'editlist')?>?orderBy=line_total_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "line_total_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_LINE_TOTAL_AMOUNT?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'editlist')?>?orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>

		<th class="tight"></th>
		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order_item']?>">
		<td class="text-nowrap"><?php echo $element['id_customer_order_item']?></td>
		<td >
                                            <a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=editList&id_customer_order=<?php echo $element['id_customer_order']?>">
                                            <?php echo $this->customerOrderList1[$element['id_customer_order']]['id_user'] ?? '';?>
                                            </a>
                                            </td>

		<td ><?php echo $element['product_name']?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['unit_price_amount']?></td>
		<td ><?php echo $element['discount_amount']?></td>
		<td ><?php echo $element['tax_amount']?></td>
		<td ><?php echo $element['line_total_amount']?></td>
		<td ><?php echo $element['created_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=edit&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('pencil', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_customer_order_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=delHtmx&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>">
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
 * Display rows from the table customer_order_item with edit and del buttons
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
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_customer_order_item" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=id_customer_order_item&order=<?php if($orderBy == "id_customer_order_item") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_customer_order" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID_CUSTOMER_ORDER?></a></th>
	<th scope="col" id="product_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=product_name&order=<?php if($orderBy == "product_name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_PRODUCT_NAME?></a></th>
	<th scope="col" id="quantity" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_QUANTITY?></a></th>
	<th scope="col" id="unit_price_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=unit_price_amount&order=<?php if($orderBy == "unit_price_amount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_UNIT_PRICE_AMOUNT?></a></th>
	<th scope="col" id="discount_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=discount_amount&order=<?php if($orderBy == "discount_amount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_DISCOUNT_AMOUNT?></a></th>
	<th scope="col" id="tax_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=tax_amount&order=<?php if($orderBy == "tax_amount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_TAX_AMOUNT?></a></th>
	<th scope="col" id="line_total_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=line_total_amount&order=<?php if($orderBy == "line_total_amount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_LINE_TOTAL_AMOUNT?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_CREATED_AT?></a></th>

		<th class="tight"></th>
		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order_item']?>">
		<td class="text-nowrap"><?php echo $element['id_customer_order_item']?></td>
		<td >
                                            <a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=editList&id_customer_order=<?php echo $element['id_customer_order']?>">
                                            <?php echo $this->customerOrderList1[$element['id_customer_order']]['id_user'];?>
                                            </a>
                                            </td>

		<td ><?php echo $element['product_name']?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['unit_price_amount']?></td>
		<td ><?php echo $element['discount_amount']?></td>
		<td ><?php echo $element['tax_amount']?></td>
		<td ><?php echo $element['line_total_amount']?></td>
		<td ><?php echo $element['created_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childList&showTabs=0&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('screwdriver', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_customer_order_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=delHtmx&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>"><?=self::icon('trash3', 16)?></a></td>
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
 * Display trashed rows from the table customer_order_item
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
	<tr>	<th scope="col" id="id_customer_order_item" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'trashedlist')?>?orderBy=id_customer_order_item&order=<?php if($orderBy == "id_customer_order_item") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_customer_order" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'trashedlist')?>?orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_CUSTOMER_ORDER?></a></th>
	<th scope="col" id="product_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'trashedlist')?>?orderBy=product_name&order=<?php if($orderBy == "product_name") echo $order; else echo 'asc';?>"><?=T_FIELD_PRODUCT_NAME?></a></th>
	<th scope="col" id="quantity" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'trashedlist')?>?orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>"><?=T_FIELD_QUANTITY?></a></th>
	<th scope="col" id="unit_price_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'trashedlist')?>?orderBy=unit_price_amount&order=<?php if($orderBy == "unit_price_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_UNIT_PRICE_AMOUNT?></a></th>
	<th scope="col" id="discount_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'trashedlist')?>?orderBy=discount_amount&order=<?php if($orderBy == "discount_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_DISCOUNT_AMOUNT?></a></th>
	<th scope="col" id="tax_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'trashedlist')?>?orderBy=tax_amount&order=<?php if($orderBy == "tax_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_TAX_AMOUNT?></a></th>
	<th scope="col" id="line_total_amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'trashedlist')?>?orderBy=line_total_amount&order=<?php if($orderBy == "line_total_amount") echo $order; else echo 'asc';?>"><?=T_FIELD_LINE_TOTAL_AMOUNT?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('customerorderitem', 'trashedlist')?>?orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order_item']?>">
		<td class="text-nowrap"><?php echo $element['id_customer_order_item']?></td>
		<td ><?php echo $this->customerOrderList1[$element['id_customer_order']]['id_user'] ?? '';?></td>
		<td ><?php echo $element['product_name']?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['unit_price_amount']?></td>
		<td ><?php echo $element['discount_amount']?></td>
		<td ><?php echo $element['tax_amount']?></td>
		<td ><?php echo $element['line_total_amount']?></td>
		<td ><?php echo $element['created_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_customer_order_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=undelHtmx&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-sm btn-outline-secondary" title="<?=T_RESTORE?>" aria-label="<?=T_RESTORE?>">
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
