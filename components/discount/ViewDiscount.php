<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\Discount;

class ViewDiscount extends \Apgenic\Classes\ViewTemplate {
/** The component has a list of the deleted entries (link Trash of the header) */
protected bool $trash = true;

public string $filtersGet = '';

public $fileters = array();

function __construct() {
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
        <a href="#" id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=edit&id_discount=<?=$id?>"><?=T_TAB_INFO?></a>
      </li>

    </ul>



<?php
}
/**
 * Edit form for the table discount
 * @parameter ModelDiscount $data
 * @parameter int $showTabs
 * @parameter array $message
 * @return void
 */
public function edit(ModelDiscount $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_discount, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>

		<form class="card card-body shadow-sm p-lg-4 mb-4" method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=discount&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="date_from"><?=T_FIELD_DATE_FROM?></label>
		<input  class="form-control" type="datetime-local" name="date_from" id="date_from" value="<?=htmlentities((string)$data->date_from)?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="date_to"><?=T_FIELD_DATE_TO?></label>
		<input  class="form-control" type="datetime-local" name="date_to" id="date_to" value="<?=htmlentities((string)$data->date_to)?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="id_category"><?=T_FIELD_ID_CATEGORY?></label>
		<input required="" class="form-control" type="number" name="id_category" id="id_category" value="<?=$data->id_category?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="id_product"><?=T_FIELD_ID_PRODUCT?></label>
		<input required="" class="form-control" type="number" name="id_product" id="id_product" value="<?=$data->id_product?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="amount"><?=T_FIELD_AMOUNT?></label>
		<input required="" class="form-control" type="number" name="amount" id="amount" value="<?=$data->amount?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="percentage"><?=T_FIELD_PERCENTAGE?></label>
		<input required="" class="form-control" type="number" name="percentage" id="percentage" value="<?=$data->percentage?>"/>
	    </div>
	    </div>

		<div class="col-12">

		<div class="border-top pt-3">
		<input type="hidden" name="id_discount" id="id" value="<?=$data->id_discount ?>"/>
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
 * Display rows from the table discount
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
	<tr>	<th scope="col" id="id_discount" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'viewlist')?>?orderBy=id_discount&order=<?php if($orderBy == "id_discount") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="date_from" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'viewlist')?>?orderBy=date_from&order=<?php if($orderBy == "date_from") echo $order; else echo 'asc';?>"><?=T_FIELD_DATE_FROM?></a></th>
	<th scope="col" id="date_to" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'viewlist')?>?orderBy=date_to&order=<?php if($orderBy == "date_to") echo $order; else echo 'asc';?>"><?=T_FIELD_DATE_TO?></a></th>
	<th scope="col" id="id_category" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'viewlist')?>?orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_CATEGORY?></a></th>
	<th scope="col" id="id_product" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'viewlist')?>?orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_PRODUCT?></a></th>
	<th scope="col" id="amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'viewlist')?>?orderBy=amount&order=<?php if($orderBy == "amount") echo $order; else echo 'asc';?>"><?=T_FIELD_AMOUNT?></a></th>
	<th scope="col" id="percentage" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'viewlist')?>?orderBy=percentage&order=<?php if($orderBy == "percentage") echo $order; else echo 'asc';?>"><?=T_FIELD_PERCENTAGE?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'viewlist')?>?orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'viewlist')?>?orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="text-nowrap"><?php echo $element['id_discount']?></td>
		<td ><?php echo $element['date_from']?></td>
		<td ><?php echo $element['date_to']?></td>
		<td ><?php echo $element['id_category']?></td>
		<td ><?php echo $element['id_product']?></td>
		<td ><?php echo $element['amount']?></td>
		<td ><?php echo $element['percentage']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=view&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-secondary" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
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
 * Display details of a record from the table discount
 *
 * @parameter ModelDiscount $data
 * @parameter array $message
 * @return void
 */
public function view(ModelDiscount $data , array $message=null){
        $this->message($message);


	$i=1;

	if ($data->id_discount != null){
	?>

    <div class="card shadow-sm">
        <div class="card-body p-lg-4">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row mb-0">

		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID?></dt><dd class="col-sm-9"><?php echo $data->id_discount?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_DATE_FROM?></dt><dd class="col-sm-9"><?php echo $data->date_from?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_DATE_TO?></dt><dd class="col-sm-9"><?php echo $data->date_to?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID_CATEGORY?></dt><dd class="col-sm-9"><?php echo $data->id_category?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID_PRODUCT?></dt><dd class="col-sm-9"><?php echo $data->id_product?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_AMOUNT?></dt><dd class="col-sm-9"><?php echo $data->amount?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_PERCENTAGE?></dt><dd class="col-sm-9"><?php echo $data->percentage?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_CREATED_AT?></dt><dd class="col-sm-9"><?php echo $data->created_at?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_UPDATED_AT?></dt><dd class="col-sm-9"><?php echo $data->updated_at?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_DELETED_AT?></dt><dd class="col-sm-9"><?php echo $data->deleted_at?></dd>
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
 * Display rows from the table discount
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
    <a href="#" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=<?=$task?>&value=<?php echo $value?>&id_discount=<?php echo $pkVal;?>" class="btn btn-sm btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>
    <?php
}

/**
 * Display rows from the table discount with edit and del buttons
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
	<tr>	<th scope="col" id="id_discount" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'editlist')?>?orderBy=id_discount&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_discount") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="date_from" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'editlist')?>?orderBy=date_from&<?=$this->filtersGet?>&order=<?php if($orderBy == "date_from") echo $order; else echo 'asc';?>"><?=T_FIELD_DATE_FROM?></a></th>
	<th scope="col" id="date_to" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'editlist')?>?orderBy=date_to&<?=$this->filtersGet?>&order=<?php if($orderBy == "date_to") echo $order; else echo 'asc';?>"><?=T_FIELD_DATE_TO?></a></th>
	<th scope="col" id="id_category" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'editlist')?>?orderBy=id_category&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_CATEGORY?></a></th>
	<th scope="col" id="id_product" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'editlist')?>?orderBy=id_product&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_PRODUCT?></a></th>
	<th scope="col" id="amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'editlist')?>?orderBy=amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "amount") echo $order; else echo 'asc';?>"><?=T_FIELD_AMOUNT?></a></th>
	<th scope="col" id="percentage" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'editlist')?>?orderBy=percentage&<?=$this->filtersGet?>&order=<?php if($orderBy == "percentage") echo $order; else echo 'asc';?>"><?=T_FIELD_PERCENTAGE?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'editlist')?>?orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'editlist')?>?orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
		<th><a href="<?=self::url('discount', 'trashedlist')?>" class="btn btn-sm btn-outline-secondary" title="<?=T_TRASH?>" aria-label="<?=T_TRASH?>"><?=self::icon('trash3', 16)?></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_discount']?>">
		<td class="text-nowrap"><?php echo $element['id_discount']?></td>
		<td ><?php echo $element['date_from']?></td>
		<td ><?php echo $element['date_to']?></td>
		<td ><?php echo $element['id_category']?></td>
		<td ><?php echo $element['id_product']?></td>
		<td ><?php echo $element['amount']?></td>
		<td ><?php echo $element['percentage']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=edit&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('pencil', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_discount']?>" hx-post="<?=BASE_URL?>/htmx.php?component=discount&task=logicaldeleteHtmx&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>">
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
 * Display rows from the table discount with edit and del buttons
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
	<tr>	<th scope="col" id="id_discount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=id_discount&order=<?php if($orderBy == "id_discount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="date_from" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=date_from&order=<?php if($orderBy == "date_from") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_DATE_FROM?></a></th>
	<th scope="col" id="date_to" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=date_to&order=<?php if($orderBy == "date_to") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_DATE_TO?></a></th>
	<th scope="col" id="id_category" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID_CATEGORY?></a></th>
	<th scope="col" id="id_product" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID_PRODUCT?></a></th>
	<th scope="col" id="amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=amount&order=<?php if($orderBy == "amount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_AMOUNT?></a></th>
	<th scope="col" id="percentage" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=percentage&order=<?php if($orderBy == "percentage") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_PERCENTAGE?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_discount']?>">
		<td class="text-nowrap"><?php echo $element['id_discount']?></td>
		<td ><?php echo $element['date_from']?></td>
		<td ><?php echo $element['date_to']?></td>
		<td ><?php echo $element['id_category']?></td>
		<td ><?php echo $element['id_product']?></td>
		<td ><?php echo $element['amount']?></td>
		<td ><?php echo $element['percentage']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childList&showTabs=0&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('screwdriver', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_discount']?>" hx-post="<?=BASE_URL?>/htmx.php?component=discount&task=delHtmx&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>"><?=self::icon('trash3', 16)?></a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="13" class="text-end">
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
 * Display trashed rows from the table discount
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
	<tr>	<th scope="col" id="id_discount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'trashedlist')?>?orderBy=id_discount&order=<?php if($orderBy == "id_discount") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="date_from" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'trashedlist')?>?orderBy=date_from&order=<?php if($orderBy == "date_from") echo $order; else echo 'asc';?>"><?=T_FIELD_DATE_FROM?></a></th>
	<th scope="col" id="date_to" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'trashedlist')?>?orderBy=date_to&order=<?php if($orderBy == "date_to") echo $order; else echo 'asc';?>"><?=T_FIELD_DATE_TO?></a></th>
	<th scope="col" id="id_category" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'trashedlist')?>?orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_CATEGORY?></a></th>
	<th scope="col" id="id_product" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'trashedlist')?>?orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_PRODUCT?></a></th>
	<th scope="col" id="amount" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'trashedlist')?>?orderBy=amount&order=<?php if($orderBy == "amount") echo $order; else echo 'asc';?>"><?=T_FIELD_AMOUNT?></a></th>
	<th scope="col" id="percentage" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'trashedlist')?>?orderBy=percentage&order=<?php if($orderBy == "percentage") echo $order; else echo 'asc';?>"><?=T_FIELD_PERCENTAGE?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'trashedlist')?>?orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('discount', 'trashedlist')?>?orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_discount']?>">
		<td class="text-nowrap"><?php echo $element['id_discount']?></td>
		<td ><?php echo $element['date_from']?></td>
		<td ><?php echo $element['date_to']?></td>
		<td ><?php echo $element['id_category']?></td>
		<td ><?php echo $element['id_product']?></td>
		<td ><?php echo $element['amount']?></td>
		<td ><?php echo $element['percentage']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_discount']?>" hx-post="<?=BASE_URL?>/htmx.php?component=discount&task=undelHtmx&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-sm btn-outline-secondary" title="<?=T_RESTORE?>" aria-label="<?=T_RESTORE?>">
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
